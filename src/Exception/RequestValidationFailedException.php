<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/19
 */

namespace JTL\SCX\Client\Exception;

class RequestValidationFailedException extends \Exception
{
    /**
     * @var array
     */
    private $invalidPropertyList;

    /**
     * RequestValidationFailedException constructor.
     * @param array $invalidPropertyList
     */
    public function __construct(array $invalidPropertyList)
    {
        $this->invalidPropertyList = $invalidPropertyList;
        parent::__construct(implode(',', $invalidPropertyList));
    }

    /**
     * @return array
     */
    public function getInvalidPropertyList(): array
    {
        return $this->invalidPropertyList;
    }
}
