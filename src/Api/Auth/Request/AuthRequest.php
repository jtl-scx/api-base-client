<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api\Auth\Request;

use JTL\SCX\Client\Request\AbstractRequest;

class AuthRequest extends AbstractRequest
{
    /**
     * @var string
     */
    private $refreshToken;

    /**
     * AuthRequest constructor.
     * @param string $refreshToken
     */
    public function __construct(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function validate(): void
    {
        return;
    }
}
