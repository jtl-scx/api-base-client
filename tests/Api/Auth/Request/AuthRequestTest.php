<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api\Auth\Request;

use JTL\SCX\Client\Request\ScxApiRequest;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class AuthRequestTest
 * @package JTL\SCX\Client\Api\Auth\Request
 */
#[CoversClass(AuthRequest::class)]
class AuthRequestTest extends TestCase
{
    #[Test]
    public function it_has_correct_refresh_token(): void
    {
        $refreshToken = uniqid('refreshToken', true);
        $request = new AuthRequest($refreshToken);
        $this->assertEquals($refreshToken, $request->getRefreshToken());
    }

    #[Test]
    public function it_has_correct_url(): void
    {
        $sut = new AuthRequest('foo');
        $this->assertEquals('/v1/auth', $sut->getUrl());
    }

    #[Test]
    public function it_has_correct_content_type(): void
    {
        $sut = new AuthRequest('foo');
        $this->assertEquals(ScxApiRequest::CONTENT_TYPE_FORM, $sut->getContentType());
    }

    #[Test]
    public function it_has_correct_http_method(): void
    {
        $sut = new AuthRequest('foo');
        $this->assertEquals(AuthRequest::HTTP_METHOD_POST, $sut->getHttpMethod());
    }

    #[Test]
    public function it_has_correct_http_parameters(): void
    {
        $token = 'a_refresh_token';
        $sut = new AuthRequest($token);
        $params = $sut->getParams();
        $this->assertArrayHasKey('refreshToken', $params);
        $this->assertEquals($token, $params['refreshToken']);
    }
}
