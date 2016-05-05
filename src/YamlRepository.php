<?php

namespace FlyCrud;

use Symfony\Component\Yaml\Yaml;

class YamlRepository extends Repository
{
    protected $extension = 'yml';

    /**
     * Transform the data to a string.
     * 
     * @param array $data
     * 
     * @return string
     */
    protected function stringify(array $data)
    {
        return Yaml::dump($data);
    }

    /**
     * Transform the string to an array.
     * 
     * @param string $source
     * 
     * @return array
     */
    protected function parse($source)
    {
        return (array) Yaml::parse($source);
    }
}
