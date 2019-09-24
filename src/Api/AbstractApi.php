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
use GuzzleHttp\Exception\GuzzleException;
use JTL\SCX\Client\Exception\RequestFailedException;
use JTL\SCX\Client\Model\ErrorList;
use JTL\SCX\Client\Request\RequestFactory;
use JTL\SCX\Client\Request\UrlFactory;
use JTL\SCX\Client\Serializer\ObjectSerializer;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractApi
{
    public const HTTP_METHOD_GET = 'GET';

    public const HTTP_METHOD_PATCH = 'PATCH';

    public const HTTP_METHOD_POST = 'POST';

    public const HTTP_METHOD_PUT = 'PUT';

    public const HTTP_METHOD_DELETE = 'DELETE';

    public const CONTENT_TYPE_JSON = 'application/json';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var UrlFactory
     */
    private $urlFactory;

    /**
     * AbstractApi constructor.
     * @param ClientInterface $client
     * @param Configuration $configuration
     * @param RequestFactory $requestFactory
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        ClientInterface $client,
        Configuration $configuration,
        RequestFactory $requestFactory = null,
        UrlFactory $urlFactory = null
    ) {
        $this->client = $client;
        $this->configuration = $configuration;
        $this->requestFactory = $requestFactory ?? new RequestFactory();
        $this->urlFactory = $urlFactory ?? new UrlFactory();
    }

    /**
     * @return array
     */
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

    /**
     * @param string|null $body
     * @param array $params
     * @return ResponseInterface
     * @throws RequestFailedException
     * @throws GuzzleException
     */
    protected function request(string $body = null, array $params = []): ResponseInterface
    {
        try {
            $url = $this->urlFactory->create($this->configuration->getHost(), $this->getUrl(), $params);
            $request = $this->requestFactory->create($this->getHttpMethod(), $url, $this->createHeaders(), $body);
            return $this->client->send($request);
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
            $errorList = null;
            if ($response !== null) {
                $responseBody = $response->getBody()->getContents();
                /** @var ErrorList $errorList */
                $errorList = ObjectSerializer::deserialize($responseBody, ErrorList::class);
            }

            throw new RequestFailedException($exception->getMessage(), $exception->getCode(), $errorList);
        }
    }

    /**
     * @return string
     */
    abstract protected function getUrl(): string;

    /**
     * @return string
     */
    abstract protected function getHttpMethod(): string;

    /**
     * @return string
     */
    protected function getContentType(): string
    {
        return self::CONTENT_TYPE_JSON;
    }
}
