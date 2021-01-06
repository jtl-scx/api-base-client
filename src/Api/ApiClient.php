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
use JTL\SCX\Client\Request\Multipart\MultipartFormDataRequest;
use JTL\SCX\Client\Request\RequestFactory;
use JTL\SCX\Client\Request\ScxApiRequest;
use JTL\SCX\Client\Request\UrlFactory;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    protected Configuration $configuration;
    private ClientInterface $client;
    private RequestFactory $requestFactory;
    private UrlFactory $urlFactory;

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
    public function request(ScxApiRequest $request): ResponseInterface
    {
        try {
            $url = $this->urlFactory->create(
                $this->configuration->getHost(),
                $request->getUrl(),
                $request->getParams()
            );
            $apiRequest = $this->requestFactory->create(
                $request->getHttpMethod(),
                $url,
                $this->createHeaders($request),
                $request->getBody()
            );

            $options = [];
            if ($request instanceof MultipartFormDataRequest) {
                $options['multipart'] = $this->buildMultipartBody($request);
            }

            if ($request->getContentType() === ScxApiRequest::CONTENT_TYPE_FORM) {
                $options['form_params'] = $request->getParams();
            }

            return $this->client->send($apiRequest, $options);
        } catch (ClientException | ServerException $exception) {
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
        if (!$request instanceof MultipartFormDataRequest) {
            $headers['Content-Type'] = $request->getContentType();
        }
        return $headers;
    }

    /**
     * @param MultipartFormDataRequest $request
     * @return array
     */
    private function buildMultipartBody(MultipartFormDataRequest $request): array
    {
        $parameters = [];
        foreach ($request->buildMultipartBody() as $parameter) {
            $parameters[] = ['name' => $parameter->getName(), 'contents' => $parameter->getContent()];
        }
        return $parameters;
    }
}
