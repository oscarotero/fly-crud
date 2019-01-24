<?php

namespace FlyCrud\Formats;

use FlyCrud\FormatInterface;

class Serialize implements FormatInterface
{
    public function getExtension(): string
    {
        return 'txt';
    }

    public function stringify(array $data): string
    {
        return serialize($data);
    }

    public function parse($source): array
    {
        return unserialize($source) ?: [];
    }
}
