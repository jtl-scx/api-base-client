<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api\Auth;

use JTL\SCX\Client\Api\AbstractApi;
use JTL\SCX\Client\Api\Auth\Request\AuthRequest;
use JTL\SCX\Client\Api\Auth\Response\AuthResponse;
use JTL\SCX\Client\Exception\RequestFailedException;
use JTL\SCX\Client\Model\AuthToken;
use JTL\SCX\Client\Serializer\ObjectSerializer;

class AuthApi extends AbstractApi
{
    /**
     * @param AuthRequest $request
     * @return AuthResponse
     * @throws RequestFailedException
     */
    public function auth(AuthRequest $request): AuthResponse
    {
        $response = $this->request(null, ['refreshToken' => $request->getRefreshToken()]);

        /** @var AuthToken $model */
        $model = ObjectSerializer::deserialize($response->getBody()->getContents(), AuthToken::class);
        
        return new AuthResponse($model, $response->getStatusCode());
    }

    /**
     * @return string
     */
    protected function getUrl(): string
    {
        return '/auth{?refreshToken}';
    }

    /**
     * @return string
     */
    protected function getHttpMethod(): string
    {
        return AbstractApi::HTTP_METHOD_POST;
    }
}
