<?php

namespace FlyCrud;

use JsonSerializable;

class Document implements JsonSerializable
{
    protected $data;

    /**
     * Constructor.
     * 
     * @param array $data
     * @param mixed $id
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param string $name
     * 
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * @see JsonSerializable
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * Returns the document data.
     * 
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Change the document data.
     * 
     * @param array $data
     * 
     * @return self
     */
    public function edit(array $data)
    {
        $this->data = $data + $this->data;

        return $this;
    }

    /**
     * Change the document data.
     * 
     * @param array $data
     * 
     * @return self
     */
    public function replace(array $data)
    {
        $this->data = $data;

        return $this;
    }
}
