<?php

namespace FlyCrud\Formats;

use FlyCrud\FormatInterface;
use Symfony\Component\Yaml\Yaml as YamlConverter;

class Yaml implements FormatInterface
{
    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return 'yml';
    }

    /**
     * {@inheritdoc}
     */
    public function stringify(array $data)
    {
        return YamlConverter::dump($data);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($source)
    {
        return (array) YamlConverter::parse($source);
    }
}
