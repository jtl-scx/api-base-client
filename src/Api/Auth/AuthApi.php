<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api\Auth;

use GuzzleHttp\Exception\GuzzleException;
use JTL\SCX\Client\Api\ApiClient;
use JTL\SCX\Client\Api\Auth\Request\AuthRequest;
use JTL\SCX\Client\Api\Auth\Response\AuthResponse;
use JTL\SCX\Client\ApiResponseDeserializer;
use JTL\SCX\Client\Exception\RequestFailedException;
use JTL\SCX\Client\Model\AuthToken;
use JTL\SCX\Client\ObjectSerializer;
use JTL\SCX\Client\ResponseDeserializer;

class AuthApi
{
    private ApiClient $apiClient;
    private ResponseDeserializer $objectSerializer;

    public function __construct(ApiClient $apiClient, ResponseDeserializer $objectSerializer)
    {
        $this->apiClient = $apiClient;
        $this->objectSerializer = $objectSerializer;
    }

    /**
     * @param AuthRequest $request
     * @return AuthResponse
     * @throws RequestFailedException
     * @throws GuzzleException
     */
    public function auth(AuthRequest $request): AuthResponse
    {
        $response = $this->apiClient->request($request);
        $model = $this->objectSerializer->deserialize($response, AuthToken::class);
        return new AuthResponse($model, $response->getStatusCode());
    }
}
