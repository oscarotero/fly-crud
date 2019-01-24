<?php
declare(strict_types = 1);

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
     * @return mixed
     */
    public function &__get(string $name)
    {
        $value = $this->offsetGet($name);

        return $value;
    }

    /**
     * @param mixed  $value
     */
    public function __set(string $name, $value)
    {
        $this->offsetSet($name, $value);
    }

    public function __isset(string $name)
    {
        return $this->offsetExists($name);
    }

    public function __unset(string $name)
    {
        $this->offsetUnset($name);
    }

    /**
     * @see JsonSerializable
     */
    public function jsonSerialize(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Returns the document data.
     */
    public function getArrayCopy(): array
    {
        return self::objectToArray(parent::getArrayCopy());
    }

    /**
     * Converts the associative arrays to stdClass object recursively.
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
     */
    private static function objectToArray(array $array): array
    {
        foreach ($array as $key => &$value) {
            if (is_object($value) || is_array($value)) {
                $value = self::objectToArray((array) $value);
            }
        }

        return (array) $array;
    }
}
