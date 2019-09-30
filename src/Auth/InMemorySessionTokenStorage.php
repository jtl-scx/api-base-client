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
     * @var SessionToken
     */
    private $sessionToken;

    /**
     * @return SessionToken
     */
    public function load(): ?SessionToken
    {
        return $this->sessionToken;
    }

    /**
     * @param SessionToken $authToken
     */
    public function save(SessionToken $authToken): void
    {
        $this->sessionToken = $authToken;
    }
}
