<?php

declare(strict_types=1);

namespace App\Core\Controllers;

use App\Core\Contracts\SessionInterface;
use App\Core\Enum\ServerStatus;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class HomeController
{
    public function __construct(
        private Twig $twig,
        private SessionInterface $session,
    ) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request, Response $response): Response
    {
        $user = $this->session->getUser();
        if ($user && $user->isAdmin()) {
            return $response->withHeader('Location', '/admin')->withStatus(
                ServerStatus::REDIRECT->value
            );
        }

        /**You need create enter point to Your project*/
        return $this->twig->render(
            $response,
            'home.twig',
        );
    }

}
