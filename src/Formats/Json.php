<?php

namespace FlyCrud\Formats;

use FlyCrud\FormatInterface;

class Json implements FormatInterface
{
    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return 'json';
    }

    /**
     * {@inheritdoc}
     */
    public function stringify(array $data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($source)
    {
        return json_decode($source, true) ?: [];
    }
}
