<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Request;

class UrlFactory
{
    /**
     * @param string $host
     * @param string $url
     * @param array $params
     * @return string
     */
    public function create(string $host, string $url, array $params = []): string
    {
        return \GuzzleHttp\uri_template($host . $url, $params);
    }
}
