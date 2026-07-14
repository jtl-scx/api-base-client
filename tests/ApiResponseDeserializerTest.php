<?php declare(strict_types=1);

namespace JTL\SCX\Client;

use JTL\SCX\Client\Model\AuthToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

#[CoversClass(ApiResponseDeserializer::class)]
class ApiResponseDeserializerTest extends TestCase
{
    public function testCanDeserializeResponse(): void
    {
        $body = json_encode(['authToken' => 'a_token', 'expiresIn' => 60]);

        $stream = $this->createStub(StreamInterface::class);
        $stream->method('getContents')->willReturn($body);

        $response = $this->createStub(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        $deserializer = new ApiResponseDeserializer();
        $authToken = $deserializer->deserialize($response, AuthToken::class);

        $this->assertInstanceOf(AuthToken::class, $authToken);
        $this->assertSame('a_token', $authToken->getAuthToken());
        $this->assertSame(60, $authToken->getExpiresIn());
    }

    public function testCanDeserializeObject(): void
    {
        $body = json_encode(['authToken' => 'a_token', 'expiresIn' => 60]);

        $deserializer = new ApiResponseDeserializer();
        $authToken = $deserializer->deserializeObject($body, AuthToken::class);

        $this->assertInstanceOf(AuthToken::class, $authToken);
        $this->assertSame('a_token', $authToken->getAuthToken());
        $this->assertSame(60, $authToken->getExpiresIn());
    }
}
