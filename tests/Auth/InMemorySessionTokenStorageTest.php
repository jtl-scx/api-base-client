<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/27
 */

namespace JTL\SCX\Client\Auth;

use JTL\SCX\Client\Auth\Model\SessionToken;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class InMemorySessionTokenStorageTest
 * @package JTL\SCX\Client\Auth
 *
 * @covers \JTL\SCX\Client\Auth\InMemorySessionTokenStorage
 */
class InMemorySessionTokenStorageTest extends TestCase
{
    public function testCanSaveAndLoadSessionToken(): void
    {
        $sessionToken = Mockery::mock(SessionToken::class);
        $storage = new InMemorySessionTokenStorage();

        $storage->save('foo', $sessionToken);
        $this->assertSame($sessionToken, $storage->load('foo'));
    }
}
