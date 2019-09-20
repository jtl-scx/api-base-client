<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/18
 */

namespace JTL\SCX\Client\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use JTL\SCX\Client\Exception\RequestFailedException;
use JTL\SCX\Client\Model\ErrorList;
use JTL\SCX\Client\Serializer\ObjectSerializer;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractApi
{
    const HTTP_METHOD_GET = 'GET';

    const HTTP_METHOD_PATCH = 'PATCH';

    const HTTP_METHOD_POST = 'POST';

    const HTTP_METHOD_PUT = 'PUT';

    const HTTP_METHOD_DELETE = 'DELETE';

    const CONTENT_TYPE_JSON = 'application/json';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * AbstractApi constructor.
     * @param ClientInterface $client
     * @param Configuration $configuration
     */
    public function __construct(ClientInterface $client, Configuration $configuration)
    {
        $this->client = $client;
        $this->configuration = $configuration;
    }

    private function createRequest(string $body = null, array $params = []): Request
    {
        return new Request(
            $this->getHttpMethod(),
            $this->createUrl($this->getUrl(), $params),
            $this->createHeaders(),
            $body
        );
    }

    private function createHeaders(): array
    {
        $headers = [];

        $authToken = $this->configuration->getAuthToken();
        if ($authToken !== null) {
            $headers['Authorization'] = 'Bearer ' . $authToken;
        }

        $headers['Content-Type'] = $this->getContentType();

        return $headers;
    }

    private function createUrl(string $url, array $params): string
    {
        return \GuzzleHttp\uri_template($this->configuration->getHost() . $url, $params);
    }

    /**
     * @param string|null $body
     * @param array $params
     * @return ResponseInterface
     * @throws RequestFailedException
     */
    protected function request(string $body = null, array $params = []): ResponseInterface
    {
        try {
            return $this->client->send($this->createRequest($body, $params));
        } catch (ClientException $exception) {
            $responseBody = $exception->getResponse()->getBody()->getContents();
            /** @var ErrorList $errorList */
            $errorList = ObjectSerializer::deserialize($responseBody, ErrorList::class);
            throw new RequestFailedException($exception->getMessage(), $exception->getCode(), $errorList);
        }
    }

    abstract protected function getUrl(): string;

    abstract protected function getHttpMethod(): string;

    protected function getContentType(): string
    {
        return self::CONTENT_TYPE_JSON;
    }
}
