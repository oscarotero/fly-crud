<?php

namespace FlyCrud\Tests;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

abstract class Base extends \PHPUnit_Framework_TestCase
{
    protected static function createRepo($class, $dirname)
    {
        $path = __DIR__.'/assets/'.$dirname;

        if (!is_dir($path)) {
            mkdir($path, 0777);
        }

        $adapter = new Local($path);
        $filesystem = new Filesystem($adapter);

        return new $class($filesystem);
    }
}