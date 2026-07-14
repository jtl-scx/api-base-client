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
use JTL\SCX\Client\Request\Multipart\MultipartFormDataRequest;
use JTL\SCX\Client\Request\Multipart\MultipartParameter;
use JTL\SCX\Client\Request\ScxApiRequest;
use JTL\SCX\Client\Request\UrlFactory;
use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ApiClient::class)]
class ApiClientTest extends AbstractTestCase
{
    public function testCanCall(): void
    {
        $response = $this->createStub(ResponseInterface::class);

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Request::class), [])
            ->willReturn($response);

        $configuration = $this->createConfigurationMock();
        $requestFactory = $this->createRequestFactoryMock(ScxApiRequest::HTTP_METHOD_POST);

        $urlFactory = $this->createMock(UrlFactory::class);
        $urlFactory->expects($this->once())
            ->method('create')
            ->with('http://localhost', '/foo', [])
            ->willReturn(uniqid('url', true));

        $api = new ApiClient($configuration, $client, $requestFactory, $urlFactory);

        $requestMock = $this->createStub(ScxApiRequest::class);
        $requestMock->method('getUrl')->willReturn('/foo');
        $requestMock->method('getParams')->willReturn([]);
        $requestMock->method('getHttpMethod')->willReturn(ScxApiRequest::HTTP_METHOD_POST);
        $requestMock->method('getAdditionalHeaders')->willReturn([]);
        $requestMock->method('getContentType')->willReturn('bier');
        $requestMock->method('getBody')->willReturn(null);
        $apiResponse = $api->request($requestMock);

        $this->assertSame($response, $apiResponse);
    }

    public function testCanSendWithFormDataEncoded(): void
    {
        $formParams = ['foo' => 'bar'];
        $response = $this->createStub(ResponseInterface::class);

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Request::class), ['form_params' => $formParams])
            ->willReturn($response);

        $configuration = $this->createConfigurationMock();
        $requestFactory = $this->createRequestFactoryMock(ScxApiRequest::HTTP_METHOD_POST);

        $urlFactory = $this->createMock(UrlFactory::class);
        $urlFactory->expects($this->once())
            ->method('create')
            ->with('http://localhost', '/foo', $formParams)
            ->willReturn(uniqid('url', true));

        $api = new ApiClient($configuration, $client, $requestFactory, $urlFactory);

        $requestMock = $this->createStub(ScxApiRequest::class);
        $requestMock->method('getUrl')->willReturn('/foo');
        $requestMock->method('getParams')->willReturn($formParams);
        $requestMock->method('getHttpMethod')->willReturn(ScxApiRequest::HTTP_METHOD_POST);
        $requestMock->method('getAdditionalHeaders')->willReturn([]);
        $requestMock->method('getContentType')->willReturn(ScxApiRequest::CONTENT_TYPE_FORM);
        $requestMock->method('getBody')->willReturn(null);
        $apiResponse = $api->request($requestMock);

        $this->assertSame($response, $apiResponse);
    }

    public function testCanSendMultipartFormData(): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $multipartParameter = new MultipartParameter('foo', 'bar');

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with(
                $this->isInstanceOf(Request::class),
                ['multipart' => [['name' => 'foo', 'contents' => 'bar']]]
            )
            ->willReturn($response);

        $configuration = $this->createConfigurationMock();
        $requestFactory = $this->createRequestFactoryMock(ScxApiRequest::HTTP_METHOD_POST);

        $urlFactory = $this->createMock(UrlFactory::class);
        $urlFactory->expects($this->once())
            ->method('create')
            ->with('http://localhost', '/foo', [])
            ->willReturn(uniqid('url', true));

        $api = new ApiClient($configuration, $client, $requestFactory, $urlFactory);

        $requestMock = $this->createStubForIntersectionOfInterfaces([ScxApiRequest::class, MultipartFormDataRequest::class]);
        $requestMock->method('getUrl')->willReturn('/foo');
        $requestMock->method('getParams')->willReturn([]);
        $requestMock->method('getHttpMethod')->willReturn(ScxApiRequest::HTTP_METHOD_POST);
        $requestMock->method('getAdditionalHeaders')->willReturn([]);
        $requestMock->method('getContentType')->willReturn('bier');
        $requestMock->method('getBody')->willReturn(null);
        $requestMock->method('buildMultipartBody')->willReturn([$multipartParameter]);
        $apiResponse = $api->request($requestMock);

        $this->assertSame($response, $apiResponse);
    }

    public function testCanThrowException(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createStub(Request::class);

        $body = json_encode(['errorList' => [['message' => 'a_message', 'code' => '1']]]);

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(400);

        $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $stream->expects($this->once())->method('getContents')->willReturn($body);
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Request::class), [])
            ->willThrowException(new ClientException('Error', $request, $response));

        $configuration = $this->createConfigurationMock();
        $requestFactory = $this->createRequestFactoryMock(ScxApiRequest::HTTP_METHOD_POST);

        $urlFactory = $this->createMock(UrlFactory::class);
        $urlFactory->expects($this->once())
            ->method('create')
            ->with('http://localhost', '/foo', [])
            ->willReturn(uniqid('url', true));

        $api = new ApiClient($configuration, $client, $requestFactory, $urlFactory);

        $requestMock = $this->createStub(ScxApiRequest::class);
        $requestMock->method('getUrl')->willReturn('/foo');
        $requestMock->method('getParams')->willReturn([]);
        $requestMock->method('getHttpMethod')->willReturn(ScxApiRequest::HTTP_METHOD_POST);
        $requestMock->method('getAdditionalHeaders')->willReturn([]);
        $requestMock->method('getContentType')->willReturn('bier');
        $requestMock->method('getBody')->willReturn(null);

        $this->expectException(RequestFailedException::class);
        $api->request($requestMock);
    }
}
