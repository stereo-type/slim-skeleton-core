<?php
/**
 * @package  AbstractDataProvider.php
 * @copyright 03.03.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Providers;

use App\Core\Components\Catalog\Model\Filter\FilterComparison;
use App\Core\Enum\AppEnvironment;
use App\Core\Services\EntityManagerService;
use App\Core\Exception\ValidationException;
use App\Core\Components\Catalog\Enum\FilterType;
use App\Core\Components\Catalog\Enum\EntityButton;
use App\Core\Components\Catalog\Model\Table\Row;
use App\Core\Components\Catalog\Model\Form\FormField;
use App\Core\Components\Catalog\Model\Filter\TableQueryParams;
use App\Core\Components\Catalog\Model\Filter\Type\Filter;
use App\Core\Components\Catalog\Model\Filter\Collections\Filters;
use App\Core\Components\Catalog\Model\Filter\Collections\FilterComparisons;

use BackedEnum;
use DateTime;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Exception;
use stdClass;
use Throwable;
use ReflectionClass;
use InvalidArgumentException;

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\Expr\Comparison;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class EntityDataProvider extends AbstractDataProvider implements CatalogFormInterface
{
    public const ENTITY_CLASS = null;

    public const ENTITY_ALIES = 'e';

    public const ENTITY_REF_LINK_METHOD = 'getName';

    public const ENTITY_REF_COLLECTION_LINK_METHOD = 'getName';

    public const ENTITY_REF_COLLECTION_LIST = true;

    public const DATE_FORMAT = 'd.m.Y';

    public const FORM_ENTITY_PARAMS_ASSOCIATION = [];

    protected ReflectionClass $reflection;

    private TranslatorInterface $translator;

    private static array $_properties = ['short' => [], 'all' => []];


    /**
     * @param EntityManager $entityManager
     * @param FormFactoryInterface $formFactory
     * @param ContainerInterface $container
     * @param TableQueryParams|null $params
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        EntityManager $entityManager,
        private readonly FormFactoryInterface $formFactory,
        ContainerInterface $container,
        ?TableQueryParams $params = null
    ) {
        if (is_null(static::ENTITY_CLASS)) {
            throw new InvalidArgumentException('Должен быть определен класс сущности');
        }
        if (!class_exists((string)static::ENTITY_CLASS)) {
            throw new InvalidArgumentException('Класс сущности не существует ' . static::ENTITY_CLASS);
        }

        $this->reflection = new ReflectionClass((string)static::ENTITY_CLASS);
        $this->translator = $container->get(TranslatorInterface::class);

        $entityAttributes = $this->reflection->getAttributes('Doctrine\ORM\Mapping\Entity');
        if (empty($entityAttributes)) {
            throw new InvalidArgumentException('Класс сущности должен иметь аттрибут Doctrine\ORM\Mapping\Entity');
        }

        parent::__construct(
            $entityManager,
            $container,
            $params ?? new TableQueryParams(orderBy: self::ENTITY_ALIES . '.id')
        );
    }

    /**Метод исключения свойств сущности
     * @return String[]
     */
    public function exclude_entity_properties(): array
    {
        return [];
    }

    /**Метод ппереименования свойств сущности
     * @return String[]
     */
    public function named_properties(): array
    {
        return [];
    }

    /**Метод исключения свойств сущности из фильтров
     * @return String[]
     */
    public function exclude_entity_filters(): array
    {
        return [];
    }

    /**Метод исключения свойств сущности из формы создания/редактирования
     * @return String[]
     */
    public function exclude_form_elements(array $args): array
    {
        return [];
    }

    /** Метод переопределения полей формы в дочернем классе.
     * @return FormField[]
     * @example Например замена инпута селектом
     * public function override_form_elements(): array
     * {
     *      $formFields = [];
     *      $name = new FormField('name', ChoiceType::class, ['choices' => $this->get_platforms()]);
     *      $formFields[$name->fieldName] = $name;
     *      return $formFields;
     * }
     */
    public function override_form_elements(array $args): array
    {
        return [];
    }

    /**Получение имен свойств сущности
     * @return array
     */
    final protected function get_named(): array
    {
        return array_map(static fn ($item) => $item->replacedName ?? $item->name, $this->get_properties());
    }


    /**Получение свойств сущности с кешированием
     * @param bool $all
     * @return array
     */
    final public function get_properties(bool $all = false): array
    {
        /**Caches*/
        if ($all) {
            if (!empty(self::$_properties['all'])) {
                return self::$_properties['all'];
            }
        } else {
            if (!empty(self::$_properties['short'])) {
                return self::$_properties['short'];
            }
        }

        $props = $this->reflection->getProperties();
        $propsNames = array_map(static fn ($item) => $item->name, $props);
        $propsValues = array_map(static function ($item) {
            $ob = new stdClass();
            $ob->name = $item->name;
            $ob->class = $item->class;
            $ob->manyToOne = false;
            $ob->manyToOneClass = null;
            $ob->isEnum = false;
            $ob->enumClass = null;

            $attributes = array_map(
                static fn ($attr) => (object)['name' => $attr->getName(), 'arguments' => $attr->getArguments()],
                $item->getAttributes()
            );

            foreach ($attributes as $attr) {
                if (isset($attr->arguments['enumType']) && $attr->arguments['enumType']) {
                    $ob->isEnum = true;
                    $ob->enumClass = $attr->arguments['enumType'];
                }
                if ($attr->name == 'Doctrine\ORM\Mapping\ManyToOne') {
                    $ob->manyToOne = true;
                    $ob->manyToOneClass = $attr->arguments['targetEntity'];
                }
            }

            $ob->attributes = $attributes;

            return $ob;
        }, $props);
        $head = array_combine($propsNames, $propsValues);

        /**Подставновка переопределений имен*/
        $names = $this->named_properties();
        foreach ($head as $k => $v) {
            if (array_key_exists($k, $names)) {
                $v->replacedName = $names[$k];
            }
        }

        if (!$all) {
            /**Исключение свойств сущности*/
            foreach ($this->exclude_entity_properties() as $e) {
                if (array_key_exists($e, $head)) {
                    unset($head[$e]);
                }
            }
        }

        if ($all) {
            self::$_properties['all'] = $head;
        } else {
            self::$_properties['short'] = $head;
        }

        return $head;
    }


    /**
     * @return array
     */
    public function head(): array
    {
        $head = $this->get_named();
        $head[] = 'Управление';
        return $head;
    }


    /**
     * @param TableQueryParams $params
     * @return QueryBuilder
     * @throws QueryException
     */
    public function get_query(TableQueryParams $params): QueryBuilder
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select(self::ENTITY_ALIES)
            ->from(static::ENTITY_CLASS, self::ENTITY_ALIES);
        $simpleProps = array_keys(array_filter($this->get_properties(), static fn ($p) => !$p->manyToOne));
        $manyToOne = array_filter($this->get_properties(), static fn ($p) => $p->manyToOne);

        $allowed = FilterComparisons::fromArray(
            array_combine($simpleProps, array_fill(0, count($simpleProps), Comparison::CONTAINS))
        );

        foreach ($manyToOne as $key => $m) {
            $allowed[] = new FilterComparison(
                $key,
                Comparison::EQ,
                manyToOne: true,
                manyToOneClass: $m->manyToOneClass
            );
        }

        return $params->filters->fill_query_builder($qb, self::ENTITY_ALIES, $allowed);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function filters(array $filterData): Filters
    {
        $filters = [];
        $exclude_filters = $this->exclude_entity_filters();
        $translator = $this->translator;
        foreach ($this->get_properties() as $key => $prop) {
            if (!in_array($key, $exclude_filters)) {
                /**TODO сделать поддержку других фильтров*/
                $name = $prop->replacedName ?? $prop->name;
                if ($prop->isEnum) {
                    $cases = $prop->enumClass::cases();
                    $keys = array_map(static fn ($i) => $i->value, $cases);
                    $values = array_map(static function ($v) use ($translator) {
                        return $v instanceof TranslatableInterface ? $v->trans($translator) : $v->value;
                    }, $cases);
                    $options = ['' => $name] + array_combine($keys, $values);
                    $filters[] = Filter::create(
                        FilterType::select,
                        $key,
                        ['placeholder' => $prop->replacedName ?? $prop->name],
                        params: ['options' => $options]
                    );
                } elseif ($prop->manyToOne) {
                    $list = $this->entityManager->getRepository($prop->manyToOneClass)->findAll();
                    $method = self::ENTITY_REF_LINK_METHOD;
                    $keys = array_map(static fn ($i) => $i->getId(), $list);
                    $values = array_map(static fn ($i) => $i->$method(), $list);
                    $options = ['' => $name] + array_combine($keys, $values);
                    $filters[] = Filter::create(
                        FilterType::select,
                        $key,
                        ['placeholder' => $name],
                        params: ['options' => $options]
                    );
                } else {
                    $filters[] = Filter::create(
                        FilterType::input,
                        $key,
                        ['placeholder' => $name]
                    );
                }
            }
        }

        return new Filters($filters);
    }


    /**
     * @param Twig $twig
     * @param array $item
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function transform_data_row(Twig $twig, array $item): iterable
    {
        $result = [];
        $exclude = $this->exclude_entity_properties();


        foreach ($item as $k => $v) {
            if (in_array($k, $exclude)) {
                continue;
            }
            /**Приметивные типы*/
            if (is_null($v)) {
                $result[] = $v;
            } elseif (is_string($v)) {
                $result[] = $v;
            } elseif (is_numeric($v)) {
                $result[] = $v;
            } elseif (is_bool($v)) {
                $result[] = (int)$v;
            } elseif ($v instanceof DateTime) {
                $result[] = date(static::DATE_FORMAT, $v->getTimestamp());
                /**Enum с возможным TranslatableInterface*/
            } elseif ($v instanceof BackedEnum) {
                if ($v instanceof TranslatableInterface) {
                    $result[] = $v->trans($this->translator);
                } else {
                    $result[] = $v->value;
                }
                /**Связь ManyToOne*/
            } elseif (
                $v instanceof PersistentCollection &&
                static::ENTITY_REF_COLLECTION_LIST
            ) {
                $list = array_map(static function ($e) {
                    $method = static::ENTITY_REF_COLLECTION_LINK_METHOD;
                    return '<li>' . $e->$method() . '</li>';
                }, $v->toArray());

                $result[] = '<ul>' . implode('', $list) . '</ul>';
            } else {
                if (is_object($v)) {
                    try {
                        $meta = $this->entityManager->getClassMetadata(get_class($v));
                        $allAssociations = $meta->getAssociationMappings();
                        foreach ($allAssociations as $fieldName => $mapping) {
                            if (in_array($fieldName, $exclude)) {
                                continue;
                            }
                            if ($mapping['type'] === ClassMetadataInfo::MANY_TO_ONE) {
                                $list = array_map(static function ($e) {
                                    $method = static::ENTITY_REF_COLLECTION_LINK_METHOD;
                                    return '<li>' . $e->$method() . '</li>';
                                }, $v->toArray());

                                $result[] = '<ul>' . implode('', $list) . '</ul>';
                                break;
                            } elseif ($mapping['type'] === ClassMetadataInfo::ONE_TO_MANY) {
                                $method = static::ENTITY_REF_LINK_METHOD;
                                $result[] = $v->$method();
                                break;
                            }
                        }
                    } catch (Throwable $e) {
                        $result[] = gettype($v);
                    }
                } else {
                    $result[] = gettype($v);
                }
            }
        }
        $result[] = $this->manage_buttons($twig, (int)$item['id']);

        $row = Row::build($result);
        return $row->cells;
    }

    /**
     * @return EntityButton[]
     */
    protected function buttons(): array
    {
        return [EntityButton::copy, EntityButton::edit, EntityButton::delete];
    }

    /**
     * @param Twig $twig
     * @param int $id
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function manage_buttons(Twig $twig, int $id): string
    {
        $buttons = array_map(static fn ($item) => $item->toMap(), $this->buttons());
        return $twig->fetch('/catalog/manage_buttons.twig', ['buttons' => $buttons, 'id' => $id]);
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @return void
     */
    protected function form_actions(FormBuilderInterface $formBuilder): void
    {
        $formBuilder
            ->add('form_actions', FormType::class, [
                'mapped' => false,
                'label'  => false,
                'attr'   => ['class' => 'not-form-control input-group mt-5 justify-content-evenly'],
            ])
            ->get('form_actions')
            ->add('cancel', ButtonType::class, [
                'label' => 'Отмена',
                'attr'  =>
                    [
                        'class'  =>
                            'form-group btn text-light-emphasis bg-light-subtle border-light-subtle form-control mr-3 ',
                        'action' => 'cancel',
                    ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Сохранить',
                'attr'  =>
                    [
                        'class'  =>
                            'form-group btn text-primary-emphasis bg-primary-subtle border-primary-subtle form-control',
                        'action' => 'submit',
                    ],
            ]);
    }

    /**
     * @param array $args
     * @return FormInterface
     * @throws EntityNotFoundException
     * @throws NotSupported
     * @throws MappingException
     */
    public function build_form(array $args): FormInterface
    {
        $formBuilder = $this->formFactory->createNamedBuilder(
            'catalog_entity_builder',
            FormType::class,
            null,
            ['data_class' => static::ENTITY_CLASS]
        );

        $metadata = $this->entityManager->getClassMetadata(static::ENTITY_CLASS);

        $exclude = $this->exclude_form_elements($args);

        $override = $this->override_form_elements($args);

        $names = $this->named_properties();

        foreach ($metadata->getFieldNames() as $fieldName) {
            if (in_array($fieldName, $exclude, true)) {
                continue;
            }

            if (!$metadata->isIdentifier($fieldName)) {
                if (isset($override[$fieldName]) && $override[$fieldName] instanceof FormField) {
                    $formField = $override[$fieldName];
                    $formBuilder->add($formField->fieldName, $formField->fieldType, $formField->options);
                } else {
                    $options = ['attr' => []];
                    $fieldType = TextType::class;

                    if ($metadata->getTypeOfField($fieldName) === 'datetime') {
                        $fieldType = DateType::class;
                        $options = [
                            'data'   => new DateTime(),
                            'widget' => 'single_text',
                        ];
                    }

                    $map = $metadata->getFieldMapping($fieldName);
                    if (!empty($map['enumType'])) {
                        $fieldType = EnumType::class;
                        $options['class'] = $map['enumType'];
                    }
                    if (isset($map['nullable']) && $map['nullable']) {
                        $options['required'] = false;
                    }
                    if (isset($names[$fieldName])) {
                        $options['label'] = $names[$fieldName];
                        $options['attr']['placeholder'] = $names[$fieldName];
                    } else {
                        $options['attr']['placeholder'] = ucfirst($fieldName);
                    }

                    $formBuilder->add($fieldName, $fieldType, $options);
                }
            }
        }


        foreach ($metadata->getAssociationNames() as $fieldName) {
            if (isset($override[$fieldName]) && $override[$fieldName] instanceof FormField) {
                $formField = $override[$fieldName];
                $formBuilder->add($formField->fieldName, $formField->fieldType, $formField->options);
            } else {
                if ($metadata->isAssociationWithSingleJoinColumn($fieldName)) {
                    $map = $metadata->getAssociationMapping($fieldName);
                    $targetEntity = $map['targetEntity'];
                    $entities = $this
                        ->entityManager
                        ->getRepository($targetEntity)
                        ->findBy(static::FORM_ENTITY_PARAMS_ASSOCIATION);

                    $method = static::ENTITY_REF_LINK_METHOD;
                    $names = array_map(static fn ($e) => $e->$method(), $entities);
                    $choices = array_combine($names, $entities);
                    $options = ['attr' => ['placeholder' => ucfirst($fieldName)], 'choices' => $choices];
                    $formBuilder->add($fieldName, ChoiceType::class, $options);
                }
            }
        }


        $this->form_actions($formBuilder);
        $form = $formBuilder->getForm();

        $id = (int)($args['id'] ?? 0);
        if ($id > 0) {
            $instance = $this->entityManager->getRepository(static::ENTITY_CLASS)->find($id);
            if (!$instance) {
                throw new EntityNotFoundException(static::ENTITY_CLASS . ' with ' . $id . ' not found');
            }
            $this->before_set($instance);
            $form->setData($instance);
            $this->after_set($instance);
        } else {
            $request = $args['request']['formParams'] ?? [];
            if (!empty($request['id']) && $request['copy'] ?? false) {
                $instance = $this->entityManager->getRepository(static::ENTITY_CLASS)->find((int)$request['id']);
                if (!$instance) {
                    throw new EntityNotFoundException(static::ENTITY_CLASS . ' with ' . $id . ' not found for copy');
                }
                $this->before_set($instance);
                $form->setData($instance);
                $this->after_set($instance);
            }
        }

        return $form;
    }

    /**Метод выполняется перед сохранением, может использоваться в качестве кастомного валидатора
     * @param object $instance
     * @return void
     */
    public function before_save(object $instance): void
    {
    }

    /**
     * @param object $instance
     * @return void
     */
    public function after_save(object $instance): void
    {
    }

    /**
     * @param object $instance
     * @return void
     */
    public function before_set(object $instance): void
    {
    }

    /**
     * @param object $instance
     * @return void
     */
    public function after_set(object $instance): void
    {
    }

    /**
     * @param int $id
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws NotSupported
     */
    public function delete(int $id): bool
    {
        $instance = $this->entityManager->getRepository(static::ENTITY_CLASS)->find($id);
        if (!$instance) {
            return false;
        }
        $this->container->get(EntityManagerService::class)->delete($instance, true);
        return true;
    }

    /**
     * @param mixed $data
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function save_form_data(mixed $data): bool
    {
        try {
            if (get_class($data) === static::ENTITY_CLASS) {
                $this->before_save($data);
                $this->container->get(EntityManagerService::class)->sync($data);
                $this->after_save($data);
                return true;
            } else {
                throw new Exception('data must be type ' . static::ENTITY_CLASS);
            }
        } catch (ValidationException $e) {
            /**Перепрокидываем наверх*/
            throw $e;
        } catch (Throwable $e) {
            $message = $e->getMessage();
            if (AppEnvironment::showErrorsDetails($this->container)) {
                $message .=
                    PHP_EOL . "File: {$e->getFile()}" .
                    PHP_EOL . "Line: {$e->getFile()}";
            }
            throw new ValidationException([$message]);
        }
    }

}
