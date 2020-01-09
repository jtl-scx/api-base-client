<?php
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/26
 */

namespace JTL\SCX\Client\Auth;

use JTL\SCX\Client\Auth\Model\SessionToken;

interface SessionTokenStorage
{
    public function load(string $key): ?SessionToken;
    public function save(string $key, SessionToken $authToken): void;
}
