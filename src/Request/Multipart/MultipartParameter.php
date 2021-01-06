<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: rherrgesell
 * Date: 1/6/21
 */

namespace JTL\SCX\Client\Request\Multipart;

use Psr\Http\Message\StreamInterface;

class MultipartParameter
{
    private string $name;

    /**
     * @var string|StreamInterface|resource
     */
    private $content;

    /**
     * Parameter constructor.
     * @param string $name
     * @param string|StreamInterface|resource $content
     */
    public function __construct(string $name, $content)
    {
        $this->name = $name;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|StreamInterface|resource
     */
    public function getContent()
    {
        return $this->content;
    }
}
