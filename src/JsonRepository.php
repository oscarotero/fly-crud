<?php

namespace FlyCrud;

class JsonRepository extends Repository
{
    protected $extension = 'json';

    /**
     * Transform the data to a string.
     * 
     * @param array $data
     * 
     * @return string
     */
    protected function stringify(array $data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
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
        return json_decode($source, true);
    }
}
