<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use JTL\SCX\Client\Api\Configuration;
use JTL\SCX\Client\Request\RequestFactory;
use JTL\SCX\Client\Request\UrlFactory;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractTestCase extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @return Configuration|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected function createConfigurationMock()
    {
        $host = 'http://localhost';

        $configuration = Mockery::mock(Configuration::class);
        $configuration->shouldReceive('getHost')
            ->once()
            ->andReturn($host);

        return $configuration;
    }

    /**
     * @param ResponseInterface $response
     * @return ClientInterface|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected function createClientMock(ResponseInterface $response)
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->once()
            ->with(Mockery::type(Request::class))
            ->andReturn($response);

        return $client;
    }

    /**
     * @param string $method
     * @param string|null $body
     * @return RequestFactory|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected function createRequestFactoryMock(string $method, string $body = null)
    {
        $requestFactory = Mockery::mock(RequestFactory::class);
        $request = Mockery::mock(Request::class);

        $requestFactory->shouldReceive('create')
            ->with($method, Mockery::any(), Mockery::any(), $body)
            ->once()
            ->andReturn($request);

        return $requestFactory;
    }

    /**
     * @param string $url
     * @param array $params
     * @return UrlFactory|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected function createUrlFactoryMock(string $url, array $params = [])
    {
        $urlFactory = Mockery::mock(UrlFactory::class);

        $urlFactory->shouldReceive('create')
            ->with('http://localhost', $url, $params)
            ->once()
            ->andReturn(uniqid('url', true));

        return $urlFactory;
    }
}
