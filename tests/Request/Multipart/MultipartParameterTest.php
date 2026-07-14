<?php declare(strict_types=1);

namespace JTL\SCX\Client\Request\Multipart;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MultipartParameter::class)]
class MultipartParameterTest extends TestCase
{
    public function testCanBeCreatedWithStringContent(): void
    {
        $name = uniqid('name', true);
        $content = uniqid('content', true);

        $parameter = new MultipartParameter($name, $content);

        $this->assertSame($name, $parameter->getName());
        $this->assertSame($content, $parameter->getContent());
    }

    public function testCanBeCreatedWithStreamContent(): void
    {
        $name = uniqid('name', true);
        $content = $this->createStub(\Psr\Http\Message\StreamInterface::class);

        $parameter = new MultipartParameter($name, $content);

        $this->assertSame($name, $parameter->getName());
        $this->assertSame($content, $parameter->getContent());
    }
}
