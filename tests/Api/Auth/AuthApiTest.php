<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api\Auth;

use JTL\SCX\Client\Api\ApiClient;
use JTL\SCX\Client\Api\Auth\Request\AuthRequest;
use JTL\SCX\Client\Api\Auth\Response\AuthResponse;
use JTL\SCX\Client\Model\AuthToken;
use JTL\SCX\Client\ResponseDeserializer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AuthApiTest
 * @package JTL\SCX\Client\Api\Auth
 *
 * @covers \JTL\SCX\Client\Api\Auth\AuthApi
 */
class AuthApiTest extends TestCase
{
    public function testCanAuthenticate()
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(200);

        $requestMock = $this->createMock(AuthRequest::class);

        $apiClientMock = $this->createMock(ApiClient::class);
        $apiClientMock->expects($this->once())->method('request')->with($requestMock)->willReturn($responseMock);

        $serializerMock = $this->createMock(ResponseDeserializer::class);
        $serializerMock->method('deserialize')
            ->with($responseMock, AuthToken::class)
            ->willReturn($this->createStub(AuthToken::class));

        $api = new AuthApi($apiClientMock, $serializerMock);
        $authTokenResponse = $api->auth($requestMock);

        $this->assertInstanceOf(AuthResponse::class, $authTokenResponse);
    }
}
