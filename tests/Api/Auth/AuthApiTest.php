<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api\Auth;

use GuzzleHttp\ClientInterface;
use JTL\SCX\Client\AbstractTestCase;
use JTL\SCX\Client\Api\AbstractApi;
use JTL\SCX\Client\Api\Auth\Request\AuthRequest;
use JTL\SCX\Client\Model\AuthToken;
use JTL\SCX\Client\ObjectSerializer;
use JTL\SCX\Client\Request\RequestFactory;
use JTL\SCX\Client\Request\ScxApiRequest;
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
        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseBody = uniqid('body', true);
        $refreshToken = uniqid('refreshToken', true);

        $responseMock->shouldReceive('getBody->getContents')
            ->once()
            ->andReturn($responseBody);

        $responseMock->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())->method('send')->willReturn($responseMock);

        $configuration = $this->createConfigurationMock();
        $requestFactory = $this->createMock(RequestFactory::class);
        $urlFactory = $this->createUrlFactoryMock('/auth{?refreshToken}', ['refreshToken' => $refreshToken]);
        $objectSerializer = Mockery::mock('alias:'. ObjectSerializer::class);

        $authToken = Mockery::mock(AuthToken::class);

        $objectSerializer->shouldReceive('deserialize')
            ->with($responseBody, AuthToken::class)
            ->once()
            ->andReturn($authToken);

        $request = Mockery::spy(AuthRequest::class);
        $request->shouldReceive('getRefreshToken')->andReturn($refreshToken);
        $request->shouldReceive('getUrl')->andReturn('/auth{?refreshToken}');
        $request->shouldReceive('getParams')->andReturn(['refreshToken' => $refreshToken]);
        $request->shouldReceive('getMethod')->andReturn(ScxApiRequest::HTTP_METHOD_POST);


        $api = new AuthApi($configuration, $client, $requestFactory, $urlFactory);
        $apiResponse = $api->auth($request);

        $this->assertSame(200, $apiResponse->getStatusCode());
        $this->assertSame($authToken, $apiResponse->getAuthToken());
    }
}
