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
 * Class UrlFactoryTest
 * @package JTL\SCX\Client\Request
 *
 * @covers \JTL\SCX\Client\Request\UrlFactory
 */
class UrlFactoryTest extends TestCase
{
    public function testCanCreateUrl()
    {
        $host = 'http://localhost';
        $url = '/foo{?bar}';
        $params = ['bar' => 'TEST'];

        $factory = new UrlFactory();

        $result = $factory->create($host, $url, $params);
        $this->assertSame('http://localhost/foo?bar=TEST', $result);
    }

    public function testCanCreateUrlWithMissingSlash()
    {
        $host = 'http://localhost';
        $url = 'foo{?bar}';
        $params = ['bar' => 'TEST'];

        $factory = new UrlFactory();

        $result = $factory->create($host, $url, $params);
        $this->assertSame('http://localhost/foo?bar=TEST', $result);
    }
}
