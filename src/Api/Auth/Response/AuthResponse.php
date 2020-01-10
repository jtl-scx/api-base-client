<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api\Auth\Response;

use JTL\SCX\Client\Model\AuthToken;
use JTL\SCX\Client\Response\AbstractResponse;

class AuthResponse extends AbstractResponse
{
    private AuthToken $authToken;

    public function __construct(AuthToken $authToken, int $statusCode)
    {
        $this->authToken = $authToken;
        parent::__construct($statusCode);
    }

    public function getAuthToken(): AuthToken
    {
        return $this->authToken;
    }

    public function isSuccessful(): bool
    {
        return $this->getStatusCode() === 200;
    }
}
