<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Request;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class RequestFactory
{
    /**
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param string|null $body
     * @return RequestInterface
     */
    public function create(string $method, string $url, array $headers, string $body = null): RequestInterface
    {
        return new Request($method, $url, $headers, $body);
    }
}
