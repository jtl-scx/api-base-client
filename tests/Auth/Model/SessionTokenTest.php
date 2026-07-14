<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/27
 */

namespace JTL\SCX\Client\Auth\Model;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class SessionTokenTest
 * @package JTL\SCX\Client\Auth\Model
 */
#[CoversClass(SessionToken::class)]
class SessionTokenTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $sessionToken = uniqid('sessionToken', true);
        $expiresAt = new \DateTimeImmutable();

        $sessionTokenModel = new SessionToken($sessionToken, $expiresAt);

        $this->assertSame($sessionToken, $sessionTokenModel->getSessionToken());
        $this->assertSame($expiresAt, $sessionTokenModel->getExpiresAt());
    }
}
