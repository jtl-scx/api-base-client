<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api\Auth\Response;

use JTL\SCX\Client\AbstractTestCase;
use JTL\SCX\Client\Model\AuthToken;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class AuthResponseTest
 * @package JTL\SCX\Client\Api\Auth\Response
 *
 * @covers \JTL\SCX\Client\Api\Auth\Response\AuthResponse
 */
class AuthResponseTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_a_authToken(): void
    {
        $authToken = $this->createStub(AuthToken::class);
        $response = new AuthResponse($authToken, 200);
        $this->assertSame($authToken, $response->getAuthToken());
    }

    /**
     * @test
     */
    public function request_is_considered_successful_one_http_status_200(): void
    {
        $statusSuccessful = 200;
        $response = new AuthResponse($this->createStub(AuthToken::class), $statusSuccessful);
        $this->assertTrue($response->isSuccessful());
    }
}
