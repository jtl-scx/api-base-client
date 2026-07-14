<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client;

use GuzzleHttp\Psr7\Request;
use JTL\SCX\Client\Api\Configuration;
use JTL\SCX\Client\Request\RequestFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function createConfigurationMock(): Configuration&MockObject
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration->expects($this->once())
            ->method('getHost')
            ->willReturn('http://localhost');

        return $configuration;
    }

    protected function createRequestFactoryMock(string $method, ?string $body = null): RequestFactory&MockObject
    {
        $request = $this->createStub(Request::class);

        $requestFactory = $this->createMock(RequestFactory::class);
        $requestFactory->expects($this->once())
            ->method('create')
            ->with($method, $this->anything(), $this->anything(), $body)
            ->willReturn($request);

        return $requestFactory;
    }
}
