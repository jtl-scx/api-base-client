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
    public const HOST_SANDBOX = 'https://scx-sbx.api.jtl-software.com';
    public const HOST_PRODUCTION = 'https://scx.api.jtl-software.com';

    private ?string $host;
    private ?string $refreshToken;

    /**
     * Configuration constructor.
     * @param string $host
     * @param string|null $refreshToken
     */
    public function __construct(string $host = self::HOST_PRODUCTION, string $refreshToken = null)
    {
        $this->host = $host;
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function hashConfiguration(): string
    {
        return md5($this->getHost() . $this->getRefreshToken());
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
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }
}
