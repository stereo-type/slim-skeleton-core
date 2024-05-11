<?php

namespace App\Core\Middleware;

use App\Core\Container;
use App\Core\Enum\ServerStatus;
use App\Core\Features\Permission\Entity\Permission;
use App\Core\Features\Role\Entity\Role;
use App\Core\Features\User\Contracts\AuthInterface;
use App\Core\Features\User\Contracts\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PermissionMiddleware implements MiddlewareInterface
{
    /**
     * @var PermissionMiddleware[]
     */
    private static array $_permissions = [];


    public function __construct(private readonly Permission $permission)
    {
    }


    /**
     * @param string $name
     * @return self
     * @throws ContainerExceptionInterface
     * @throws EntityNotFoundException
     * @throws NotFoundExceptionInterface
     */
    public static function instance(string $name): self
    {
        if (isset(self::$_permissions[$name])) {
            return self::$_permissions[$name];
        }
        $container = Container::get_container();
        $instance = $container
            ->get(EntityManagerInterface::class)
            ->getRepository(Permission::class)
            ->findOneBy(['name' => $name]);
        if (!$instance) {
            throw new EntityNotFoundException();
        }

        self::$_permissions[$name] = new PermissionMiddleware($instance);

        return self::$_permissions[$name];
    }




    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $container = Container::get_container();
        $user = $request->getAttributes()['user'] ?? null;
        /**Если вызов происходит перед вызовом AuthMiddleware дополнительно пытаемся получить*/
        if (is_null($user)) {
            $user = $container->get(AuthInterface::class)->user();
        }
        if ($user instanceof UserInterface) {
            $roles = $user->getRoles();
            foreach ($roles as $role) {
                if ($role instanceof Role) {

                    //                    if ($role->getName() == $this->role->getName()) {
                    //                        return $handler->handle($request);
                    //                    }
                }
            }
        }

        return $container
            ->get(ResponseFactoryInterface::class)
            ->createResponse()
            ->withStatus(ServerStatus::FORBIDDEN->value);
    }
}
