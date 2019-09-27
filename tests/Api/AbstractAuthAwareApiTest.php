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
use JTL\SCX\Client\Request\RequestFactory;
use JTL\SCX\Client\Request\UrlFactory;
use Mockery;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractAuthAwareApiTest
 * @package JTL\SCX\Client\Api
 *
 * @covers \JTL\SCX\Client\Api\AbstractAuthAwareApi
 */
class AbstractAuthAwareApiTest extends AbstractTestCase
{
    /**
     * @var SessionTokenStorage|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $sessionTokenStorage;

    /**
     * @var AuthApi|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $authApi;

    /**
     * @var Mockery\LegacyMockInterface|Mockery\MockInterface|ResponseInterface
     */
    private $response;

    /**
     * @var SessionToken|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $sessionTokenMock;

    /**
     * @var \DateTimeImmutable
     */
    private $tokenExpiresAt;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var int
     */
    private $expiresIn;

    /**
     * @var AuthResponse|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $authResponse;

    /**
     * @var string
     */
    private $sessionToken;


    public function setUp(): void
    {
        $this->tokenExpiresAt = new \DateTimeImmutable();
        $this->refreshToken = uniqid('refreshToken');
        $this->sessionToken = uniqid('sessionToken');
        $this->expiresIn = random_int(1, 200);

        $this->sessionTokenMock = Mockery::mock(SessionToken::class);
        $this->sessionTokenStorage = Mockery::mock(SessionTokenStorage::class);
        $this->authApi = Mockery::mock(AuthApi::class);
        $this->authResponse = Mockery::mock(AuthResponse::class);
        $this->response = Mockery::mock(ResponseInterface::class);
    }

    public function testCanRequestWithoutSessionToken(): void
    {
        $configuration = $this->createConfigurationMock();
        $client = $this->createClientMock($this->response);
        $requestFactory = $this->createRequestFactoryMock(AbstractApi::HTTP_METHOD_POST);
        $urlFactory = $this->createUrlFactoryMock('/foo');

        $testAuthApi = new TestAuthApi(
            $configuration,
            $this->sessionTokenStorage,
            $client,
            $this->authApi,
            $requestFactory,
            $urlFactory
        );

        $this->sessionTokenStorage->shouldReceive('load')
            ->once()
            ->andReturn($this->sessionTokenMock);

        $this->sessionTokenMock->shouldReceive('getExpiresAt')
            ->once()
            ->andReturn($this->tokenExpiresAt);

        $configuration->shouldReceive('getRefreshToken')
            ->once()
            ->andReturn($this->refreshToken);

        $this->authApi->shouldReceive('auth')
            ->with(Mockery::type(AuthRequest::class))
            ->once()
            ->andReturn($this->authResponse);

        $this->authResponse->shouldReceive('getAuthToken->getAuthToken')
            ->once()
            ->andReturn($this->sessionToken);

        $this->authResponse->shouldReceive('getAuthToken->getExpiresIn')
            ->once()
            ->andReturn($this->expiresIn);

        $this->sessionTokenStorage->shouldReceive('save')
            ->once()
            ->with(Mockery::type(SessionToken::class));

        $response = $testAuthApi->call();

        $this->assertSame($this->response, $response);
    }

    public function testCanRequestIfRequestFails(): void
    {
        $client = Mockery::mock(ClientInterface::class);

        $client->shouldReceive('send')
            ->once()
            ->with(Mockery::type(Request::class))
            ->andThrows(new RequestFailedException('FOO', 401, null, null));

        $client->shouldReceive('send')
            ->once()
            ->with(Mockery::type(Request::class))
            ->andReturn($this->response);

        $host = 'http://localhost';

        $configuration = Mockery::mock(Configuration::class);
        $configuration->shouldReceive('getHost')
            ->twice()
            ->andReturn($host);

        $this->sessionTokenStorage->shouldReceive('load')
            ->once()
            ->andReturn($this->sessionTokenMock);

        $this->sessionTokenMock->shouldReceive('getExpiresAt')
            ->once()
            ->andReturn(new \DateTimeImmutable('+1 day'));

        $configuration->shouldReceive('getRefreshToken')
            ->once()
            ->andReturn($this->refreshToken);

        $this->sessionTokenMock->shouldReceive('getSessionToken')
            ->once()
            ->andReturn($this->sessionToken);

        $this->authApi->shouldReceive('auth')
            ->with(Mockery::type(AuthRequest::class))
            ->once()
            ->andReturn($this->authResponse);

        $this->authResponse->shouldReceive('getAuthToken->getAuthToken')
            ->once()
            ->andReturn($this->sessionToken);

        $this->authResponse->shouldReceive('getAuthToken->getExpiresIn')
            ->once()
            ->andReturn($this->expiresIn);

        $this->sessionTokenStorage->shouldReceive('save')
            ->once()
            ->with(Mockery::type(SessionToken::class));

        $requestFactory = Mockery::mock(RequestFactory::class);
        $request = Mockery::mock(Request::class);

        $requestFactory->shouldReceive('create')
            ->with(AbstractApi::HTTP_METHOD_POST, Mockery::any(), Mockery::any(), null)
            ->twice()
            ->andReturn($request);

        $urlFactory = Mockery::mock(UrlFactory::class);

        $urlFactory->shouldReceive('create')
            ->with('http://localhost', '/foo', [])
            ->twice()
            ->andReturn(uniqid('url', true));

        $testAuthApi = new TestAuthApi(
            $configuration,
            $this->sessionTokenStorage,
            $client,
            $this->authApi,
            $requestFactory,
            $urlFactory
        );

        $response = $testAuthApi->call();
        $this->assertSame($this->response, $response);
    }
}

class TestAuthApi extends AbstractAuthAwareApi
{
    public function call(): ResponseInterface
    {
        return $this->request();
    }

    protected function getUrl(): string
    {
        return '/foo';
    }

    protected function getHttpMethod(): string
    {
        return AbstractApi::HTTP_METHOD_POST;
    }
}
