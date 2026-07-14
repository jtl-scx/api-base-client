<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: mbrandt
 * Date: 9/27/19
 */

namespace JTL\SCX\Client\Exception;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class RequestValidationFailedExceptionTest
 * @package JTL\SCX\Client\Exception
 */
#[CoversClass(RequestValidationFailedException::class)]
class RequestValidationFailedExceptionTest extends TestCase
{
    public function testCanGetValues(): void
    {
        $invalidPropertyList = ['invalid', 'value'];

        $exception = new RequestValidationFailedException($invalidPropertyList);
        $this->assertEquals($invalidPropertyList, $exception->getInvalidPropertyList());
        $this->assertEquals('invalid,value', $exception->getMessage());
    }
}
