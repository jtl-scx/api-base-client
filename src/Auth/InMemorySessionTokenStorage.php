<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/26
 */

namespace JTL\SCX\Client\Auth;

use JTL\SCX\Client\Auth\Model\SessionToken;

class InMemorySessionTokenStorage implements SessionTokenStorage
{
    /**
     * @var SessionToken[]
     */
    private array $sessionTokenMap;

    /**
     * @param string $key
     * @return SessionToken
     */
    public function load(string $key): ?SessionToken
    {
        return $this->sessionTokenMap[$key];
    }

    /**
     * @param string $key
     * @param SessionToken $authToken
     */
    public function save(string $key, SessionToken $authToken): void
    {
        $this->sessionTokenMap[$key] = $authToken;
    }
}
