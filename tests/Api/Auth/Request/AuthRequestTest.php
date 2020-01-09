<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api\Auth\Request;

use JTL\SCX\Client\AbstractTestCase;

/**
 * Class AuthRequestTest
 * @package JTL\SCX\Client\Api\Auth\Request
 *
 * @covers \JTL\SCX\Client\Api\Auth\Request\AuthRequest
 */
class AuthRequestTest extends AbstractTestCase
{
    public function testCanReadRefreshToken(): void
    {
        $refreshToken = uniqid('refreshToken', true);

        $request = new AuthRequest($refreshToken);
        $this->assertSame($refreshToken, $request->getRefreshToken());
    }
}
