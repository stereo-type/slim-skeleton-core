<?php

declare(strict_types = 1);

namespace App\Core\Controllers;

use DateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class HomeController
{
    public function __construct(
        private Twig $twig,
    ) {
    }

    /**
     * @param  Response  $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Response $response): Response
    {
        /**You need create enter point to Your project*/
        return $this->twig->render(
            $response,
            'home.twig',
        );
    }

}
