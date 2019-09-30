<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api\Auth;

use JTL\SCX\Client\AbstractTestCase;
use JTL\SCX\Client\Api\AbstractApi;
use JTL\SCX\Client\Api\Auth\Request\AuthRequest;
use JTL\SCX\Client\Model\AuthToken;
use JTL\SCX\Client\ObjectSerializer;
use Mockery;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AuthApiTest
 * @package JTL\SCX\Client\Api\Auth
 *
 * @covers \JTL\SCX\Client\Api\Auth\AuthApi
 */
class AuthApiTest extends AbstractTestCase
{
    public function testCanAuthenticate()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $responseBody = uniqid('body', true);
        $request = Mockery::mock(AuthRequest::class);
        $refreshToken = uniqid('refreshToken', true);

        $response->shouldReceive('getBody->getContents')
            ->once()
            ->andReturn($responseBody);

        $response->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);

        $client = $this->createClientMock($response);
        $configuration = $this->createConfigurationMock();
        $requestFactory = $this->createRequestFactoryMock(AbstractApi::HTTP_METHOD_POST);
        $urlFactory = $this->createUrlFactoryMock('/auth{?refreshToken}', ['refreshToken' => $refreshToken]);
        $objectSerializer = Mockery::mock('alias:'. ObjectSerializer::class);

        $authToken = Mockery::mock(AuthToken::class);

        $objectSerializer->shouldReceive('deserialize')
            ->with($responseBody, AuthToken::class)
            ->once()
            ->andReturn($authToken);

        $request->shouldReceive('getRefreshToken')
            ->once()
            ->andReturn($refreshToken);

        $api = new AuthApi($configuration, $client, $requestFactory, $urlFactory);
        $apiResponse = $api->auth($request);

        $this->assertSame(200, $apiResponse->getStatusCode());
        $this->assertSame($authToken, $apiResponse->getAuthToken());
    }
}
