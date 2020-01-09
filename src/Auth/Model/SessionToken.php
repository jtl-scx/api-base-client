<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/26
 */

namespace JTL\SCX\Client\Auth\Model;


class SessionToken
{
    private string $sessionToken;
    private \DateTimeInterface $expiresAt;

    /**
     * AuthToken constructor.
     * @param string $sessionToken
     * @param \DateTimeInterface|null $expiresAt
     */
    public function __construct(string $sessionToken, \DateTimeInterface $expiresAt)
    {
        $this->sessionToken = $sessionToken;
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function getSessionToken(): string
    {
        return $this->sessionToken;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }
}
