<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: pkanngiesser
 * Date: 2019/09/19
 */

namespace JTL\SCX\Client\Serializer;

class JsonSerializer
{
    /**
     * @param array $data
     * @return string
     */
    public function serialize(array $data): string
    {
        return json_encode($data);
    }

    /**
     * @param string $json
     * @param bool $assoc
     * @return array|\stdClass
     */
    public function deserialize(string $json, bool $assoc = true)
    {
        return json_decode($json, $assoc);
    }
}
