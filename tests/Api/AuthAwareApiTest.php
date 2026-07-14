<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/26
 */

namespace JTL\SCX\Client\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use JTL\SCX\Client\AbstractTestCase;
use JTL\SCX\Client\Api\Auth\AuthApi;
use JTL\SCX\Client\Api\Auth\Request\AuthRequest;
use JTL\SCX\Client\Api\Auth\Response\AuthResponse;
use JTL\SCX\Client\Auth\Model\SessionToken;
use JTL\SCX\Client\Auth\SessionTokenStorage;
use JTL\SCX\Client\Exception\RequestFailedException;
use JTL\SCX\Client\Model\AuthToken;
use JTL\SCX\Client\Request\RequestFactory;
use JTL\SCX\Client\Request\ScxApiRequest;
use JTL\SCX\Client\Request\UrlFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AuthAwareApiClient::class)]
class AuthAwareApiTest extends AbstractTestCase
{
    private SessionTokenStorage&MockObject $sessionTokenStorage;

    private AuthApi&MockObject $authApi;

    private ResponseInterface&Stub $response;

    private string $refreshToken;

    private int $expiresIn;

    private AuthResponse&MockObject $authResponse;

    private string $sessionToken;

    public function setUp(): void
    {
        $this->refreshToken = uniqid('refreshToken');
        $this->sessionToken = uniqid('sessionToken');
        $this->expiresIn = random_int(1, 200);

        $this->sessionTokenStorage = $this->createMock(SessionTokenStorage::class);
        $this->authApi = $this->createMock(AuthApi::class);
        $this->authResponse = $this->createMock(AuthResponse::class);
        $this->response = $this->createStub(ResponseInterface::class);
    }

    private function createAuthTokenMock(): AuthToken&MockObject
    {
        $authToken = $this->createMock(AuthToken::class);
        $authToken->expects($this->once())->method('getAuthToken')->willReturn($this->sessionToken);
        $authToken->expects($this->once())->method('getExpiresIn')->willReturn($this->expiresIn);

        return $authToken;
    }

    public function testCanRequestWithoutSessionToken(): void
    {
        $configuration = $this->createConfigurationMock();
        $storageKey = 'key';
        $configuration->method('hashConfiguration')->willReturn($storageKey);

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Request::class), [])
            ->willReturn($this->response);

        $requestFactory = $this->createRequestFactoryMock(ScxApiRequest::HTTP_METHOD_POST);

        $urlFactory = $this->createMock(UrlFactory::class);
        $urlFactory->expects($this->once())
            ->method('create')
            ->with('http://localhost', '/foo', [])
            ->willReturn(uniqid('url', true));

        $testAuthApi = new AuthAwareApiClient(
            $configuration,
            $this->sessionTokenStorage,
            $client,
            $this->authApi,
            $requestFactory,
            $urlFactory
        );

        $this->sessionTokenStorage->expects($this->once())
            ->method('load')
            ->willReturn(null);

        $configuration->expects($this->once())
            ->method('getRefreshToken')
            ->willReturn($this->refreshToken);

        $this->authApi->expects($this->once())
            ->method('auth')
            ->with($this->isInstanceOf(AuthRequest::class))
            ->willReturn($this->authResponse);

        $this->authResponse->expects($this->exactly(2))
            ->method('getAuthToken')
            ->willReturn($this->createAuthTokenMock());

        $this->sessionTokenStorage->expects($this->once())
            ->method('save')
            ->with($storageKey, $this->isInstanceOf(SessionToken::class));

        $requestMock = $this->createStub(ScxApiRequest::class);
        $requestMock->method('getUrl')->willReturn('/foo');
        $requestMock->method('getParams')->willReturn([]);
        $requestMock->method('getHttpMethod')->willReturn(ScxApiRequest::HTTP_METHOD_POST);
        $requestMock->method('getAdditionalHeaders')->willReturn([]);
        $requestMock->method('getContentType')->willReturn('bier');
        $requestMock->method('getBody')->willReturn(null);
        $response = $testAuthApi->request($requestMock);

        $this->assertSame($this->response, $response);
    }

    public function testCanRequestIfRequestFails(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $sendCallCount = 0;
        $client->expects($this->exactly(2))
            ->method('send')
            ->with($this->isInstanceOf(Request::class), [])
            ->willReturnCallback(function () use (&$sendCallCount) {
                $sendCallCount++;
                if ($sendCallCount === 1) {
                    throw new RequestFailedException('FOO', 401, null, null);
                }

                return $this->response;
            });

        $host = 'http://localhost';

        $configuration = $this->createMock(Configuration::class);
        $storageKey = 'key';
        $configuration->method('hashConfiguration')->willReturn($storageKey);
        $configuration->method('getHost')->willReturn($host);

        $sessionTokenMock = $this->createMock(SessionToken::class);
        $sessionTokenMock->expects($this->once())
            ->method('getExpiresAt')
            ->willReturn(new \DateTimeImmutable('+1 day'));
        $sessionTokenMock->expects($this->once())
            ->method('getSessionToken')
            ->willReturn($this->sessionToken);

        $this->sessionTokenStorage->expects($this->once())
            ->method('load')
            ->willReturn($sessionTokenMock);

        $configuration->expects($this->once())
            ->method('getRefreshToken')
            ->willReturn($this->refreshToken);

        $this->authApi->expects($this->once())
            ->method('auth')
            ->with($this->isInstanceOf(AuthRequest::class))
            ->willReturn($this->authResponse);

        $this->authResponse->expects($this->exactly(2))
            ->method('getAuthToken')
            ->willReturn($this->createAuthTokenMock());

        $this->sessionTokenStorage->expects($this->once())
            ->method('save')
            ->with($storageKey, $this->isInstanceOf(SessionToken::class));

        $requestFactory = $this->createMock(RequestFactory::class);
        $request = $this->createStub(Request::class);

        $requestFactory->expects($this->exactly(2))
            ->method('create')
            ->with(ScxApiRequest::HTTP_METHOD_POST, $this->anything(), $this->anything(), null)
            ->willReturn($request);

        $urlFactory = $this->createMock(UrlFactory::class);

        $urlFactory->expects($this->exactly(2))
            ->method('create')
            ->with('http://localhost', '/foo', [])
            ->willReturn(uniqid('url', true));

        $testAuthApi = new AuthAwareApiClient(
            $configuration,
            $this->sessionTokenStorage,
            $client,
            $this->authApi,
            $requestFactory,
            $urlFactory
        );

        $requestMock = $this->createStub(ScxApiRequest::class);
        $requestMock->method('getUrl')->willReturn('/foo');
        $requestMock->method('getParams')->willReturn([]);
        $requestMock->method('getHttpMethod')->willReturn(ScxApiRequest::HTTP_METHOD_POST);
        $requestMock->method('getAdditionalHeaders')->willReturn([]);
        $requestMock->method('getContentType')->willReturn('bier');
        $requestMock->method('getBody')->willReturn(null);

        $response = $testAuthApi->request($requestMock);
        $this->assertSame($this->response, $response);
    }
}
