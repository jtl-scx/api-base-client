<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/26
 */

namespace JTL\SCX\Client\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use JTL\SCX\Client\Api\Auth\AuthApi;
use JTL\SCX\Client\Api\Auth\Request\AuthRequest;
use JTL\SCX\Client\ApiResponseDeserializer;
use JTL\SCX\Client\Auth\InMemorySessionTokenStorage;
use JTL\SCX\Client\Auth\Model\SessionToken;
use JTL\SCX\Client\Auth\SessionTokenStorage;
use JTL\SCX\Client\Exception\RequestFailedException;
use JTL\SCX\Client\Request\RequestFactory;
use JTL\SCX\Client\Request\ScxApiRequest;
use JTL\SCX\Client\Request\UrlFactory;
use Psr\Http\Message\ResponseInterface;

class AuthAwareApiClient extends ApiClient
{
    private AuthApi $authApi;
    private SessionTokenStorage $tokenStorage;
    private ?SessionToken $sessionToken;

    /**
     * AbstractAuthAwareApi constructor.
     * @param Configuration $configuration
     * @param SessionTokenStorage|null $tokenStorage
     * @param ClientInterface|null $client
     * @param AuthApi|null $authApi
     * @param RequestFactory|null $requestFactory
     * @param UrlFactory|null $urlFactory
     */
    public function __construct(
        Configuration $configuration,
        SessionTokenStorage $tokenStorage = null,
        ClientInterface $client = null,
        AuthApi $authApi = null,
        RequestFactory $requestFactory = null,
        UrlFactory $urlFactory = null
    ) {
        parent::__construct($configuration, $client, $requestFactory, $urlFactory);
        $this->authApi = $authApi ?? new AuthApi(
            new ApiClient(
                $configuration,
                $client,
                $requestFactory,
                $urlFactory
            ),
            new ApiResponseDeserializer()
        );
        $this->tokenStorage = $tokenStorage ?? new InMemorySessionTokenStorage();
    }

    /**
     * @param ScxApiRequest $request
     * @return ResponseInterface
     * @throws GuzzleException
     * @throws RequestFailedException
     */
    public function request(ScxApiRequest $request): ResponseInterface
    {
        $this->sessionToken = $this->tokenStorage->load($this->configuration->hashConfiguration());

        if ($this->isSessionTokenExpired()) {
            $this->refreshSessionToken();
        }

        try {
            return parent::request($request);
        } catch (RequestFailedException $e) {
            if ($this->isUnauthorized($e)) {
                $this->refreshSessionToken();
                return parent::request($request);
            }
            throw $e;
        }
    }

    private function isSessionTokenExpired(): bool
    {
        if ($this->sessionToken === null) {
            return true;
        }

        return $this->sessionToken->getExpiresAt() < new \DateTimeImmutable();
    }

    /**
     * @throws GuzzleException
     * @throws RequestFailedException
     */
    private function refreshSessionToken(): void
    {
        $request = new AuthRequest((string)$this->configuration->getRefreshToken());
        $response = $this->authApi->auth($request);
        $this->sessionToken = new SessionToken(
            (string)$response->getAuthToken()->getAuthToken(),
            new \DateTimeImmutable('+' . ($response->getAuthToken()->getExpiresIn() - 2) . ' seconds')
        );

        $storageKey = $this->configuration->hashConfiguration();
        $this->tokenStorage->save($storageKey, $this->sessionToken);
    }

    private function isUnauthorized(RequestFailedException $exception): bool
    {
        return $exception->getCode() === 401;
    }

    protected function createHeaders(ScxApiRequest $request): array
    {
        $headers = parent::createHeaders($request);
        $headers['Authorization'] = 'Bearer ' . (string)$this->sessionToken->getSessionToken();

        return $headers;
    }
}
