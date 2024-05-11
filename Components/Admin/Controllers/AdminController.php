<?php

declare(strict_types=1);

namespace App\Core\Components\Admin\Controllers;

use App\Core\Components\Admin\Model\Tree\AdminRoot;
use App\Core\Contracts\RequestValidatorFactoryInterface;
use App\Core\Features\User\Contracts\AuthInterface;
use App\Core\Lib\Url;
use App\Core\ResponseFormatter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class AdminController
{
    public function __construct(
        private Twig $twig,
        private RequestValidatorFactoryInterface $requestValidatorFactory,
        private AuthInterface $auth,
        private ResponseFormatter $responseFormatter,
        private App $app
    ) {
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function index(Request $request, Response $response): Response
    {
        $category = $request->getQueryParams()['category'] ?? null;
        $root = AdminRoot::admin_get_root($this->app);
        $categories = $root->children;
        $header = $root->visibleName;
        $backButton = null;
        if (!is_null($category) && !empty($categories)) {
            $cat = array_filter($categories, static fn ($c) => $c->name === $category);
            if (empty($cat)) {
                $find = $root->locate($category);
                if ($find) {
                    $header = $find->visibleName;
                    $categories = [$find];
                    $backButton = (object)['text' => '< Назад', 'url' => Url::build('/admin')];
                }
            }
        }

        if (is_null($category) && !empty($categories)) {
            $category = reset($categories)->name;
        }


        return $this->twig->render(
            $response,
            'admin.twig',
            [
                'header'           => $header,
                'categories'       => $categories,
                'current_category' => $category,
                'backButton'      => $backButton
            ]
        );
    }

}
