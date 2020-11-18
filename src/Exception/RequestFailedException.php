<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/18
 */

namespace JTL\SCX\Client\Exception;

use JTL\SCX\Client\Model\ErrorList;
use JTL\SCX\Client\Model\ErrorResponse;

class RequestFailedException extends \Exception
{
    private ?ErrorList $errorList;
    private ?string $body;

    /**
     * RequestFailedException constructor.
     * @param string $message
     * @param int $code
     * @param ErrorList|null $errorList
     * @param string|null $body
     */
    public function __construct(string $message, int $code, ?ErrorList $errorList, ?string $body)
    {
        parent::__construct($message, $code);
        $this->errorList = $errorList;
        $this->body = $body;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @return ErrorResponse[]
     */
    public function getErrorResponseList(): array
    {
        if ($this->errorList instanceof ErrorList) {
            return $this->errorList->getErrorList() ?? [];
        }

        return [];
    }

    public function hasErrorCode(string $code): bool
    {
        foreach ($this->getErrorResponseList() as $error) {
            if ($error->getCode() === $code) {
                return true;
            }
        }
        return false;
    }
}
