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
    public function load(): ?SessionToken;
    public function save(SessionToken $authToken): void;
}
