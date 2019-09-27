<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/27
 */

namespace JTL\SCX\Client\Request;

use JTL\SCX\Client\AbstractTestCase;
use JTL\SCX\Client\Exception\RequestValidationFailedException;
use JTL\SCX\Client\Model\ModelInterface;
use Mockery;

/**
 * Class AbstractRequestTest
 * @package JTL\SCX\Client\Request
 *
 * @covers \JTL\SCX\Client\Request\AbstractRequest
 */
class AbstractRequestTest extends AbstractTestCase
{
    public function testCanNotValidate(): void
    {
        $testRequest = new TestInvalidRequest();

        $this->expectException(RequestValidationFailedException::class);
        $testRequest->validate();
    }

    public function testCanValidate(): void
    {
        $testRequest = new TestValidRequest();

        $testRequest->validate();
        $this->assertTrue(true);
    }
}

class TestInvalidRequest extends AbstractRequest
{
    public function validate(): void
    {
        $model = Mockery::mock(ModelInterface::class);

        $model->shouldReceive('valid')
            ->once()
            ->andReturnFalse();

        $model->shouldReceive('listInvalidProperties')
            ->once()
            ->andReturn([]);

        $this->validateModel($model);
    }
}

class TestValidRequest extends AbstractRequest
{
    public function validate(): void
    {
        $model = Mockery::mock(ModelInterface::class);

        $model->shouldReceive('valid')
            ->once()
            ->andReturnTrue();

        $this->validateModel($model);
    }
}
