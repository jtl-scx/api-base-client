<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api\Auth\Request;

use PHPUnit\Framework\TestCase;

/**
 * Class AuthRequestTest
 * @package JTL\SCX\Client\Api\Auth\Request
 *
 * @covers \JTL\SCX\Client\Api\Auth\Request\AuthRequest
 */
class AuthRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_correct_refresh_token(): void
    {
        $refreshToken = uniqid('refreshToken', true);
        $request = new AuthRequest($refreshToken);
        $this->assertEquals($refreshToken, $request->getRefreshToken());
    }

    /**
     * @test
     */
    public function it_has_correct_url()
    {
        $sut = new AuthRequest('foo');
        $this->assertEquals('/auth{?refreshToken}', $sut->getUrl());
    }

    /**
     * @test
     */
    public function it_has_correct_http_method()
    {
        $sut = new AuthRequest('foo');
        $this->assertEquals(AuthRequest::HTTP_METHOD_POST, $sut->getHttpMethod());
    }

    /**
     * @test
     */
    public function it_has_correct_http_parameters()
    {
        $sut = new AuthRequest('a_refresh_token');
        $params = $sut->getParams();
        $this->assertArrayHasKey('refreshToken', $params);
        $this->assertEquals('a_refresh_token', $params['refreshToken']);
    }
}
