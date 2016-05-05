<?php

namespace FlyCrud;

class SerializeRepository extends Repository
{
    protected $extension = 'txt';

    /**
     * {@inheritdoc}
     */
    protected function stringify(array $data)
    {
        return serialize($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function parse($source)
    {
        return unserialize($source);
    }
}
