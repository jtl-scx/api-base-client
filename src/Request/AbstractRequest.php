<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/19
 */

namespace JTL\SCX\Client\Request;

use JTL\SCX\Client\Exception\RequestValidationFailedException;
use JTL\SCX\Client\Model\ModelInterface;

abstract class AbstractRequest
{
    /**
     * @param ModelInterface $model
     * @throws RequestValidationFailedException
     */
    protected function validateModel(object $model): void
    {
        if (!$model->valid()) {
            throw new RequestValidationFailedException($model->listInvalidProperties());
        }
    }

    abstract public function validate(): void;
}
