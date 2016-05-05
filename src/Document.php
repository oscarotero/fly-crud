<?php

namespace FlyCrud;

class Document
{
    protected $id;
    protected $data;

    public function __construct(array $data = [], $id = null)
    {
        $this->setData($data);
        $this->setId($id ?: uniqid());
    }

    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
}
