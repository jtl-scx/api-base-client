<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: rherrgesell
 * Date: 1/9/20
 */

namespace JTL\SCX\Client;

use Psr\Http\Message\ResponseInterface;

class ApiResponseDeserializer implements ResponseDeserializer
{
    public function deserialize(ResponseInterface $response, string $openApiModel): object
    {
        return $this->deserializeObject($response->getBody()->getContents(), $openApiModel);
    }

    public function deserializeObject($data, string $openApiModel): object {
        return ObjectSerializer::deserialize($data, $openApiModel);
    }
}
