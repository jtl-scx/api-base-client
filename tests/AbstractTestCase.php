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
            ->andReturn($host);

        return $configuration;
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
        return $urlFactory;
    }
}
