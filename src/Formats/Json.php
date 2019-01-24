<?php
declare(strict_types = 1);

namespace FlyCrud\Formats;

use FlyCrud\FormatInterface;

class Json implements FormatInterface
{
    public function getExtension(): string
    {
        return 'json';
    }

    public function stringify(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function parse(string $source): array
    {
        return json_decode($source, true) ?: [];
    }
}
