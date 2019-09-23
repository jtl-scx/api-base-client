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

/**
 * Class AuthResponseTest
 * @package JTL\SCX\Client\Api\Auth\Response
 *
 * @covers \JTL\SCX\Client\Api\Auth\Response\AuthResponse
 */
class AuthResponseTest extends AbstractTestCase
{
    public function testCanBeCreated(): void
    {
        $authToken = Mockery::mock(AuthToken::class);
        $statusCode = random_int(1, 100);

        $response = new AuthResponse($authToken, $statusCode);

        $this->assertSame($authToken, $response->getAuthToken());
        $this->assertSame($statusCode, $response->getStatusCode());
    }
}
