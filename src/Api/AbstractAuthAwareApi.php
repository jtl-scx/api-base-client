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
use JTL\SCX\Client\Auth\InMemorySessionTokenStorage;
use JTL\SCX\Client\Auth\Model\SessionToken;
use JTL\SCX\Client\Auth\SessionTokenStorage;
use JTL\SCX\Client\Exception\RequestFailedException;
use JTL\SCX\Client\Request\RequestFactory;
use JTL\SCX\Client\Request\UrlFactory;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractAuthAwareApi extends AbstractApi
{
    /**
     * @var AuthApi
     */
    private $authApi;

    /**
     * @var SessionTokenStorage
     */
    private $tokenStorage;

    /**
     * @var SessionToken
     */
    private $sessionToken;

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
        $this->authApi = $authApi ?? new AuthApi($configuration, $client);
        $this->tokenStorage = $tokenStorage ?? new InMemorySessionTokenStorage();
    }


    /**
     * @param string|null $body
     * @param array $params
     * @return ResponseInterface
     * @throws GuzzleException
     * @throws RequestFailedException
     * @throws \Exception
     */
    protected function request(string $body = null, array $params = []): ResponseInterface
    {
        $this->sessionToken = $this->tokenStorage->load($this->configuration->getHost());

        if ($this->isSessionTokenExpired()) {
            $this->refreshSessionToken();
        }

        try {
            return parent::request($body, $params);
        } catch (RequestFailedException $e) {
            if ($this->isUnauthorized($e)) {
                $this->refreshSessionToken();
                return parent::request($body, $params);
            }
            throw $e;
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function isSessionTokenExpired(): bool
    {
        if ($this->sessionToken === null) {
            return true;
        }

        return $this->sessionToken->getExpiresAt() < new \DateTimeImmutable();
    }

    /**
     * @throws RequestFailedException
     * @throws GuzzleException
     * @throws \Exception
     */
    private function refreshSessionToken(): void
    {
        $request = new AuthRequest((string)$this->configuration->getRefreshToken());
        $response = $this->authApi->auth($request);
        $this->sessionToken = new SessionToken(
            (string)$response->getAuthToken()->getAuthToken(),
            new \DateTimeImmutable('+' . ($response->getAuthToken()->getExpiresIn() - 2) . ' seconds')
        );
        $this->tokenStorage->save($this->configuration->getHost(), $this->sessionToken);
    }

    /**
     * @return array
     */
    protected function createHeaders(): array
    {
        $headers = parent::createHeaders();
        $headers['Authorization'] = 'Bearer ' . (string)$this->sessionToken->getSessionToken();

        return $headers;
    }

    /**
     * @param RequestFailedException $exception
     * @return bool
     */
    private function isUnauthorized(RequestFailedException $exception): bool
    {
        return $exception->getCode() === 401;
    }
}
