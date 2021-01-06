<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: rherrgesell
 * Date: 1/6/21
 */

namespace JTL\SCX\Client\Request\Multipart;

interface MultipartFormDataRequest
{
    /**
     * @return array<MultipartParameter>
     */
    public function buildMultipartBody(): array;
}
