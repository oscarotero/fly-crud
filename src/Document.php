<?php

namespace FlyCrud;

use ArrayObject;
use JsonSerializable;

class Document extends ArrayObject implements JsonSerializable
{
    public function __construct(array $value)
    {
        parent::__construct(self::arrayToObject($value));
    }

    /**
     * @param string $name
     * 
     * @return mixed
     */
    public function &__get($name)
    {
        $value = $this->offsetGet($name);

        return $value;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * @param string $name
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    /**
     * @see JsonSerializable
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }

    /**
     * Returns the document data.
     * 
     * @return self
     */
    public function getArrayCopy()
    {
        return self::objectToArray(parent::getArrayCopy());
    }

    /**
     * Converts the associative arrays to stdClass object recursively.
     * 
     * @param array $array
     * 
     * @return array|stdClass
     */
    private static function arrayToObject(array $array)
    {
        $is_object = false;

        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = self::arrayToObject($value);
            }

            if (is_string($key)) {
                $is_object = true;
            }
        }

        return $is_object ? (object) $array : $array;
    }

    /**
     * Converts stdClass objects to arrays recursively.
     * 
     * @param array $array
     * 
     * @return array
     */
    private static function objectToArray(array $array)
    {
        foreach ($array as $key => &$value) {
            if (is_object($value) || is_array($value)) {
                $value = self::objectToArray((array) $value);
            }
        }

        return (array) $array;
    }
}
