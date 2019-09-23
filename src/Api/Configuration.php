<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/18
 */

namespace JTL\SCX\Client\Api;

class Configuration
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string|null
     */
    private $authToken;

    /**
     * Configuration constructor.
     * @param string $host
     * @param string|null $authToken
     */
    public function __construct(string $host, string $authToken = null)
    {
        $this->host = $host;
        $this->authToken = $authToken;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string|null
     */
    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }
}
