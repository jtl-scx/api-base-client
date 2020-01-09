<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: rherrgesell
 * Date: 1/8/20
 */

namespace JTL\SCX\Client\Request;

interface ScxApiRequest
{
    public const HTTP_METHOD_GET = 'GET';

    public const HTTP_METHOD_PATCH = 'PATCH';

    public const HTTP_METHOD_POST = 'POST';

    public const HTTP_METHOD_PUT = 'PUT';

    public const HTTP_METHOD_DELETE = 'DELETE';

    public const CONTENT_TYPE_JSON = 'application/json';

    public function getUrl(): string;

    public function getHttpMethod(): string;

    public function getContentType(): string;

    public function getAdditionalHeaders(): array;

    public function getParams(): array;

    public function getBody(): ?string;
}
