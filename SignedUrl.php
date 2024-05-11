<?php

declare(strict_types=1);

namespace App\Core;

use DateTime;
use Slim\Interfaces\RouteParserInterface;

readonly class SignedUrl
{
    public function __construct(private Config $config, private RouteParserInterface $routeParser)
    {
    }

    public function fromRoute(string $routeName, array $routeParams, DateTime $expirationDate): string
    {
        $expiration  = $expirationDate->getTimestamp();
        $queryParams = ['expiration' => $expiration];
        $baseUrl     = trim($this->config->get('app_url'), '/');
        $url         = $baseUrl . $this->routeParser->urlFor($routeName, $routeParams, $queryParams);
        $signature = hash_hmac('sha256', $url, $this->config->get('app_key'));

        return $baseUrl . $this->routeParser->urlFor(
            $routeName,
            $routeParams,
            $queryParams + ['signature' => $signature]
        );
    }
}
