<?php

namespace FlyCrud;

use Symfony\Component\Yaml\Yaml;

class YamlRepository extends Repository
{
    protected $extension = 'yml';

    /**
     * {@inheritdoc}
     */
    protected function stringify(array $data)
    {
        return Yaml::dump($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function parse($source)
    {
        return (array) Yaml::parse($source);
    }
}
