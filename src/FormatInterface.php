<?php

namespace FlyCrud;

interface FormatInterface
{
    /**
     * Returns the file extension of this format.
     * 
     * @return string
     */
    public function getExtension();

    /**
     * Transform the data to a string.
     * 
     * @param array $data
     * 
     * @return string
     */
    public function stringify(array $data);

    /**
     * Transform the string to an array.
     * 
     * @param string $source
     * 
     * @return array
     */
    public function parse($source);
}
