<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/18
 */

namespace JTL\SCX\Client\Exception;

use JTL\SCX\Client\Channel\Model\ErrorResponse;
use JTL\SCX\Client\Model\ErrorList;

class RequestFailedException extends \Exception
{
    /**
     * @var ErrorList|null
     */
    private $errorList;

    /**
     * @var string|null
     */
    private $body;

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
            return $this->errorList->getErrorList();
        }

        return [];
    }
}
