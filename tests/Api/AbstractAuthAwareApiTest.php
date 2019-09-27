<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/26
 */

namespace JTL\SCX\Client\Api;

use JTL\SCX\Client\AbstractTestCase;
use JTL\SCX\Client\Api\Auth\AuthApi;
use JTL\SCX\Client\Auth\Model\SessionToken;
use JTL\SCX\Client\Auth\SessionTokenStorage;
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
     * @var Configuration|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $configuration;

    /**
     * @var \GuzzleHttp\ClientInterface|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $client;

    /**
     * @var \JTL\SCX\Client\Request\RequestFactory|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $requestFactory;

    /**
     * @var \JTL\SCX\Client\Request\UrlFactory|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $urlFactory;

    /**
     * @var TestAuthApi
     */
    private $testAuthApi;

    /**
     * @var SessionToken|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $sessionToken;

    /**
     * @var \DateTimeImmutable
     */
    private $tokenExpiresAt;

    /**
     * @var string
     */
    private $refreshToken;


    public function setUp(): void
    {
        $this->tokenExpiresAt = new \DateTimeImmutable();
        $this->refreshToken = uniqid('refreshToken');

        $this->sessionToken = Mockery::mock(SessionToken::class);
        $this->sessionTokenStorage = Mockery::mock(SessionTokenStorage::class);
        $this->authApi = Mockery::mock(AuthApi::class);
        $this->response = Mockery::mock(ResponseInterface::class);
        $this->configuration = $this->createConfigurationMock();
        $this->client = $this->createClientMock($this->response);
        $this->requestFactory = $this->createRequestFactoryMock(AbstractApi::HTTP_METHOD_POST);
        $this->urlFactory = $this->createUrlFactoryMock('/foo');
        $this->testAuthApi = new TestAuthApi(
            $this->configuration,
            $this->sessionTokenStorage,
            $this->client,
            $this->authApi,
            $this->requestFactory,
            $this->urlFactory
        );
    }

    public function testCanRequestWithoutSessionToken(): void
    {
        $this->sessionTokenStorage->shouldReceive('load')
            ->once()
            ->andReturn($this->sessionToken);

        $this->sessionToken->shouldReceive('getExpiresAt')
            ->once()
            ->andReturn($this->tokenExpiresAt);

        $this->testAuthApi->call();
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
