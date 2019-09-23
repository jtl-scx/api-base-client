<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Request;

use PHPUnit\Framework\TestCase;

/**
 * Class RequestFactoryTest
 * @package JTL\SCX\Client\Request
 *
 * @covers \JTL\SCX\Client\Request\RequestFactory
 */
class RequestFactoryTest extends TestCase
{
    public function testCreateRequest()
    {
        $method = 'POST';
        $url = 'http://localhost';
        $headers = [];
        $body = uniqid('body', true);

        $factory = new RequestFactory();

        $request = $factory->create($method, $url, $headers, $body);

        $this->assertSame($method, $request->getMethod());
        $this->assertSame($url, $request->getUri()->getScheme() . '://' . $request->getUri()->getHost());
        $this->assertSame($body, $request->getBody()->getContents());
    }
}
