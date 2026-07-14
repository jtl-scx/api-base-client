<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/27
 */

namespace JTL\SCX\Client\Auth;

use JTL\SCX\Client\Auth\Model\SessionToken;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class InMemorySessionTokenStorageTest
 * @package JTL\SCX\Client\Auth
 */
#[CoversClass(InMemorySessionTokenStorage::class)]
class InMemorySessionTokenStorageTest extends TestCase
{
    public function testCanSaveAndLoadSessionToken(): void
    {
        $sessionToken = $this->createStub(SessionToken::class);
        $storage = new InMemorySessionTokenStorage();

        $storage->save('foo', $sessionToken);
        $this->assertSame($sessionToken, $storage->load('foo'));
    }
}
