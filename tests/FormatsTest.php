<?php
declare(strict_types = 1);

namespace FlyCrud\Tests;

use FlyCrud\Formats;
use PHPUnit\Framework\TestCase;

class FormatsTest extends TestCase
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
        $format = new Formats\Serialize();

        $this->assertSame('txt', $format->getExtension());

        $result = $format->stringify([]);
        $this->assertSame('a:0:{}', $result);

        $result = $format->parse('');
        $this->assertSame([], $result);
    }
}
