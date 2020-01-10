<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/27
 */

namespace JTL\SCX\Client\Response;

use PHPUnit\Framework\TestCase;

/**
 * Class AbstractResponseTest
 * @package JTL\SCX\Client\Response
 *
 * @covers \JTL\SCX\Client\Response\AbstractResponse
 */
class AbstractResponseTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $statusCode = random_int(0, 100);
        $testResponse = new TestResponse($statusCode);

        $this->assertSame($statusCode, $testResponse->getStatusCode());
    }

    public function testCanCheckIfResponseIsSuccessful()
    {
        $testResponse = new TestResponse(1);
        $this->assertTrue($testResponse->isSuccessful());
    }
}

class TestResponse extends AbstractResponse
{
    /**
     * @inheritDoc
     */
    public function isSuccessful(): bool
    {
        return true;
    }
}
