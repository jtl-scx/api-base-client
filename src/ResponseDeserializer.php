<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: rherrgesell
 * Date: 1/9/20
 */

namespace JTL\SCX\Client;

use Psr\Http\Message\ResponseInterface;

interface ResponseDeserializer
{
    public function deserialize(ResponseInterface $response, string $openApiModel): object;
    public function deserializeObject(\stdClass $data, string $openApiModel): object;
}
