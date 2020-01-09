<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use JTL\SCX\Client\AbstractTestCase;
use JTL\SCX\Client\Exception\RequestFailedException;
use JTL\SCX\Client\Model\ErrorList;
use JTL\SCX\Client\ObjectSerializer;
use JTL\SCX\Client\Request\ScxApiRequest;
use Mockery;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractApiTest
 * @package JTL\SCX\Client\Api
 *
 * @covers \JTL\SCX\Client\Api\AbstractApi
 */
class AbstractApiTest extends AbstractTestCase
{
    public function testCanCall(): void
    {
        $response = Mockery::mock(ResponseInterface::class);

        $client = $this->createClientMock($response);
        $configuration = $this->createConfigurationMock();
        $requestFactory = $this->createRequestFactoryMock(ScxApiRequest::HTTP_METHOD_POST);
        $urlFactory = $this->createUrlFactoryMock('/foo');

        $api = new TestApi($configuration, $client, $requestFactory, $urlFactory);

        $requestMock = Mockery::mock(ScxApiRequest::class);
        $requestMock->shouldReceive('getUrl')->andReturn('/foo');
        $requestMock->shouldReceive('getParams')->andReturn([]);
        $requestMock->shouldReceive('getHttpMethod')->andReturn(ScxApiRequest::HTTP_METHOD_POST);
        $requestMock->shouldReceive('getAdditionalHeaders')->andReturn([]);
        $requestMock->shouldReceive('getContentType')->andReturn('bier');
        $requestMock->shouldReceive('getBody')->andReturnNull();
        $apiResponse = $api->call($requestMock);

        $this->assertSame($response, $apiResponse);
    }

    public function testCanThrowException(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $request = Mockery::mock(Request::class);

        $body = uniqid('body');

        $response->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(400);

        $response->shouldReceive('getBody->getContents')
            ->once()
            ->andReturn($body);

        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->once()
            ->with(Mockery::type(Request::class))
            ->andThrow(new ClientException('Error', $request, $response));

        $configuration = $this->createConfigurationMock();
        $requestFactory = $this->createRequestFactoryMock(ScxApiRequest::HTTP_METHOD_POST);
        $urlFactory = $this->createUrlFactoryMock('/foo');

        $errorList = Mockery::mock(ErrorList::class);

        $objectSerializer = Mockery::mock('alias:'. ObjectSerializer::class);
        $objectSerializer->shouldReceive('deserialize')
            ->once()
            ->with($body, ErrorList::class)
            ->andReturn($errorList);

        $api = new TestApi($configuration, $client, $requestFactory, $urlFactory);

        $requestMock = Mockery::mock(ScxApiRequest::class);
        $requestMock->shouldReceive('getUrl')->andReturn('/foo');
        $requestMock->shouldReceive('getParams')->andReturn([]);
        $requestMock->shouldReceive('getHttpMethod')->andReturn(ScxApiRequest::HTTP_METHOD_POST);
        $requestMock->shouldReceive('getAdditionalHeaders')->andReturn([]);
        $requestMock->shouldReceive('getContentType')->andReturn('bier');
        $requestMock->shouldReceive('getBody')->andReturnNull();

        $this->expectException(RequestFailedException::class);
        $api->call($requestMock);
    }
}

class TestApi extends AbstractApi
{
    public function call(ScxApiRequest $request): ResponseInterface
    {
        return $this->request($request);
    }
}
