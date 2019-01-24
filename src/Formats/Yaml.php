<?php
declare(strict_types = 1);

namespace FlyCrud\Formats;

use FlyCrud\FormatInterface;
use Symfony\Component\Yaml\Yaml as YamlConverter;

class Yaml implements FormatInterface
{
    public function getExtension(): string
    {
        return 'yml';
    }

    public function stringify(array $data): string
    {
        return YamlConverter::dump($data);
    }

    public function parse(string $source): array
    {
        return (array) YamlConverter::parse($source);
    }
}
