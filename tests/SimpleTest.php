<?php

namespace FlyCrud\Tests;

use FlyCrud\Directory;
use FlyCrud\Document;
use FlyCrud\Formats;
use PHPUnit_Framework_TestCase;

class SimpleTest extends PHPUnit_Framework_TestCase
{
    public function testJson()
    {
        $repo = Directory::make(__DIR__.'/tmp', new Formats\Json());

        $this->commonTests($repo);
    }

    public function testYaml()
    {
        $repo = Directory::make(__DIR__.'/tmp', new Formats\Yaml());

        $this->commonTests($repo);
    }

    public function testSerialize()
    {
        $repo = Directory::make(__DIR__.'/tmp', new Formats\Serialize());

        $this->commonTests($repo);
    }

    private function commonTests(Directory $repo)
    {
        $document = new Document([
            'title' => 'Hello world',
            'text' => 'Lorem ipsum dolor sit amet',
        ]);

        $repo->saveDocument('hello', $document);

        $repo->hello2 = new Document([
            'title' => 'Hello world2',
            'text' => 'Lorem ipsum dolor sit amet2',
        ]);

        $this->assertTrue($repo->hasDocument('hello2'));
        $this->assertTrue(isset($repo->hello));

        $saved = $repo->getDocument('hello');

        $this->assertInstanceOf(Document::class, $saved);
        $this->assertSame($document, $saved);
        $this->assertSame($document, $repo->hello);
        $this->assertEquals('Hello world', $saved->title);
        $this->assertEquals('Lorem ipsum dolor sit amet', $saved->text);

        $all = $repo->getAll();

        $this->assertArrayHasKey('hello', $all);
        $this->assertInstanceOf(Document::class, $all['hello']);

        $repo->delete('hello');
        unset($repo->hello2);

        $this->assertFalse($repo->hasDocument('hello'));
        $this->assertFalse($repo->hasDocument('hello2'));
    }
}
