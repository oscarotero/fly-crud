<?php

namespace FlyCrud;

class JsonRepository extends Repository
{
    protected $extension = 'json';

    /**
     * {@inheritdoc}
     */
    protected function stringify(array $data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * {@inheritdoc}
     */
    protected function parse($source)
    {
        return json_decode($source, true);
    }
}
