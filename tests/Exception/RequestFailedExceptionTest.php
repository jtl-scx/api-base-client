<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: mbrandt
 * Date: 9/27/19
 */

namespace JTL\SCX\Client\Exception;

use JTL\SCX\Client\AbstractTestCase;
use JTL\SCX\Client\Model\ErrorList;
use JTL\SCX\Client\Model\ErrorResponse;
use Mockery;

/**
 * Class RequestFailedExceptionTest
 * @package JTL\SCX\Client\Exception
 *
 * @covers \JTL\SCX\Client\Exception\RequestFailedException
 */
class RequestFailedExceptionTest extends AbstractTestCase
{
    public function testCanGetValues(): void
    {
        $message = uniqid('message', true);
        $code = random_int(1, 10000);
        $errorList = Mockery::mock(ErrorList::class);
        $body = uniqid('body', true);
        $errorListArray = ['error'];

        $errorList->shouldReceive('getErrorList')
            ->twice()
            ->andReturn($errorListArray);

        $exception = new RequestFailedException($message, $code, $errorList, $body);
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($errorListArray, $exception->getErrorResponseList());
        $this->assertEquals('error', $exception->getErrorResponseList()[0]);
        $this->assertEquals($body, $exception->getBody());
    }

    public function testCanReturnEmptyErrorResponseListIfErrorListIsNull(): void
    {
        $message = uniqid('message', true);
        $code = random_int(1, 10000);

        $exception = new RequestFailedException($message, $code, null, null);
        $this->assertEquals([], $exception->getErrorResponseList());
    }

    public function testCanCheckIfErrorResponseHasErrorCode()
    {
        $errorList = new ErrorList();
        $errorList->setErrorList([
            new ErrorResponse(['message' => 'a_message', 'code' => '1']),
            new ErrorResponse(['message' => 'a_message', 'code' => '2']),
            new ErrorResponse(['message' => 'a_message', 'code' => '3']),
        ]);
        $exception = new RequestFailedException('foo', 123, $errorList, 'dont care');

        $this->assertTrue($exception->hasErrorCode('1'));
        $this->assertFalse($exception->hasErrorCode('this code is not present'));
    }
}
