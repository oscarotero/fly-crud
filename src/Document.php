<?php

namespace FlyCrud;

use JsonSerializable;

class Document implements JsonSerializable
{
    protected $id;
    protected $data;

    /**
     * Constructor
     * 
     * @param array $data
     * @param mixed $id
     */
    public function __construct(array $data = [], $id = null)
    {
        $this->setData($data);
        $this->setId($id ?: uniqid());
    }

    /**
     * Returns a value of the document
     * 
     * @param string $name
     * 
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Create/edit a value
     * 
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
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
     * Set a new id for the document
     * 
     * @param mixed $id
     * 
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Returns the document id
     * 
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the document data
     * 
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Change the document data
     * 
     * @param array $data
     * 
     * @return self
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
}
