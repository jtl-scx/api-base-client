<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/18
 */

namespace JTL\SCX\Client\Exception;

use JTL\SCX\Client\Model\ErrorList;

class RequestFailedException extends \Exception
{
    /**
     * @var ErrorList|null
     */
    private $errorList;

    public function __construct(string $message, int $code, ?ErrorList $errorList)
    {
        parent::__construct($message, $code);
        $this->errorList = $errorList;
    }

    /**
     * @return ErrorList|null
     */
    public function getErrorList(): ?ErrorList
    {
        return $this->errorList;
    }
}
