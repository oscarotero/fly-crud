<?php

namespace FlyCrud\Tests;

use FlyCrud\Formats;
use PHPUnit_Framework_TestCase;

class FormatsTest extends PHPUnit_Framework_TestCase
{
    public function testYaml()
    {
        $format = new Formats\Yaml();

        $this->assertSame('yml', $format->getExtension());

        $result = $format->stringify([]);
        $this->assertSame('{  }', $result);

        $result = $format->parse('');
        $this->assertSame([], $result);
    }

    public function testJson()
    {
        $format = new Formats\Json();

        $this->assertSame('json', $format->getExtension());

        $result = $format->stringify([]);
        $this->assertSame('[]', $result);

        $result = $format->parse('');
        $this->assertSame([], $result);
    }

    public function testSerialize()
    {
        $format = new Formats\serialize();

        $this->assertSame('txt', $format->getExtension());

        $result = $format->stringify([]);
        $this->assertSame('a:0:{}', $result);

        $result = $format->parse('');
        $this->assertSame([], $result);
    }
}
