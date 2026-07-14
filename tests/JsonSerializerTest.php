<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/23
 */

namespace JTL\SCX\Client;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class JsonSerializerTest
 * @package JTL\SCX\Client\Serializer
 */
#[CoversClass(JsonSerializer::class)]
class JsonSerializerTest extends AbstractTestCase
{
    public function testCanDeserialize(): void
    {
        $json = '{"foo": "bar"}';

        $serializer = new JsonSerializer();
        $data = $serializer->deserialize($json);

        $this->assertSame(['foo' => 'bar'], $data);
    }

    public function testCanSerialize(): void
    {
        $data = ['foo' => 'bar'];

        $serializer = new JsonSerializer();

        $this->assertSame('{"foo":"bar"}', $serializer->serialize($data));
    }
}
