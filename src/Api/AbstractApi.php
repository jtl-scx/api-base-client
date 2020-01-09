<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/18
 */

namespace JTL\SCX\Client\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use JTL\SCX\Client\Exception\RequestFailedException;
use JTL\SCX\Client\Model\ErrorList;
use JTL\SCX\Client\ObjectSerializer;
use JTL\SCX\Client\Request\RequestFactory;
use JTL\SCX\Client\Request\ScxApiRequest;
use JTL\SCX\Client\Request\UrlFactory;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractApi
{
    protected Configuration $configuration;
    private ClientInterface $client;
    private RequestFactory $requestFactory;
    private UrlFactory $urlFactory;

    /**
     * AbstractApi constructor.
     * @param ClientInterface $client
     * @param Configuration $configuration
     * @param RequestFactory $requestFactory
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        Configuration $configuration,
        ClientInterface $client = null,
        RequestFactory $requestFactory = null,
        UrlFactory $urlFactory = null
    ) {
        $this->configuration = $configuration;
        $this->client = $client ?? new Client();
        $this->requestFactory = $requestFactory ?? new RequestFactory();
        $this->urlFactory = $urlFactory ?? new UrlFactory();
    }

    /**
     * @param ScxApiRequest $request
     *
     * @return ResponseInterface
     *
     * @throws GuzzleException
     * @throws RequestFailedException
     */
    protected function request(ScxApiRequest $request): ResponseInterface
    {
        try {
            $url = $this->urlFactory->create(
                $this->configuration->getHost(),
                $request->getUrl(),
                $request->getParams()
            );
            $request = $this->requestFactory->create(
                $request->getHttpMethod(),
                $url,
                $this->createHeaders($request),
                $request->getBody()
            );
            return $this->client->send($request);
        } catch (ClientException|ServerException $exception) {
            $response = $exception->getResponse();
            $errorList = null;
            $responseBody = null;
            if ($response !== null) {
                $responseBody = $response->getBody()->getContents();
                /** @var ErrorList $errorList */
                $errorList = ObjectSerializer::deserialize($responseBody, ErrorList::class);
            }

            throw new RequestFailedException(
                $exception->getMessage(),
                $exception->getCode(),
                $errorList,
                $responseBody
            );
        }
    }

    /**
     * @param ScxApiRequest $request
     * @return array
     */
    protected function createHeaders(ScxApiRequest $request): array
    {
        $headers = $request->getAdditionalHeaders();
        $headers['Content-Type'] = $request->getContentType();

        return $headers;
    }
}
