<?php
/**
 * @package  trade Arrays.php
 * @copyright 29.04.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Lib;


use Psr\Http\Message\UriInterface;
use Slim\Psr7\Factory\ServerRequestFactory;

use function DI\string;

class Url
{
    public static function build(UriInterface|string $url, array $params = []): UriInterface
    {
        if ($url instanceof UriInterface) {
            $uri = $url;
        } else {
            $uri = ServerRequestFactory::createFromGlobals()->getUri();
            $uri = $uri->withPath($url);
        }
        return $uri->withQuery(http_build_query($params));
    }

}