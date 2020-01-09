<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api\Auth\Request;

use JTL\SCX\Client\Request\ScxApiRequest;

class AuthRequest implements ScxApiRequest
{
    private string $refreshToken;

    public function __construct(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getUrl(): string
    {
        return '/auth{?refreshToken}';
    }

    public function getHttpMethod(): string
    {
        return self::HTTP_METHOD_POST;
    }

    public function getParams(): array
    {
        return ['refreshToken' => $this->getRefreshToken()];
    }

    public function getBody(): ?string
    {
        return null;
    }

    public function getContentType(): string
    {
        return self::CONTENT_TYPE_JSON;
    }

    public function getAdditionalHeaders(): array
    {
        return [];
    }
}
