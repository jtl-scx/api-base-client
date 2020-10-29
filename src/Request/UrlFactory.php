<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Request;

use GuzzleHttp\UriTemplate\UriTemplate;

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
        if (!$this->endsWithCharacter($host, '/') && !$this->startsWithCharacter($url, '/')) {
            $host .= '/';
        }

        $template = $host . $url;
        return UriTemplate::expand($template, $params);
    }

    /**
     * @param string $value
     * @param string $character
     * @return bool
     */
    private function endsWithCharacter(string $value, string $character): bool
    {
        return (substr($value, -1) === $character);
    }

    /**
     * @param string $value
     * @param string $character
     * @return bool
     */
    private function startsWithCharacter(string $value, string $character): bool
    {
        return (substr($value, 0, 1) === $character);
    }
}
