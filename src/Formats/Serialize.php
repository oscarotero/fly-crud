<?php

namespace FlyCrud\Formats;

use FlyCrud\FormatInterface;

class Serialize implements FormatInterface
{
    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return 'txt';
    }

    /**
     * {@inheritdoc}
     */
    public function stringify(array $data)
    {
        return serialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($source)
    {
        return unserialize($source);
    }
}
