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
    private $sessionTokenMap;

    /**
     * @param string $host
     * @return SessionToken
     */
    public function load(string $host): ?SessionToken
    {
        return $this->sessionTokenMap[$host];
    }

    /**
     * @param string $host
     * @param SessionToken $authToken
     */
    public function save(string $host, SessionToken $authToken): void
    {
        $this->sessionTokenMap[$host] = $authToken;
    }
}
