<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client\Api;

use JTL\SCX\Client\AbstractTestCase;

/**
 * Class ConfigurationTest
 * @package JTL\SCX\Client\Api
 *
 * @covers \JTL\SCX\Client\Api\Configuration
 */
class ConfigurationTest extends AbstractTestCase
{
    public function testCanBeCreated(): void
    {
        $host = uniqid('host', true);
        $authToken = uniqid('authToken', true);

        $configuration = new Configuration($host, $authToken);

        $this->assertSame($host, $configuration->getHost());
    }
}
