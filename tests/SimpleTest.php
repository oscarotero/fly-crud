<?php

namespace FlyCrud\Tests;

use FlyCrud\Directory;
use FlyCrud\Document;
use FlyCrud\Formats;
use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testJson()
    {
        $repo = Directory::make(__DIR__, new Formats\Json());

        $this->commonTests($repo);
    }

    public function testYaml()
    {
        $repo = Directory::make(__DIR__, new Formats\Yaml());

        $this->commonTests($repo);
    }

    public function testSerialize()
    {
        $repo = Directory::make(__DIR__, new Formats\Serialize());

        $this->commonTests($repo);
    }

    private function commonTests(Directory $repo)
    {
        $document = new Document([
            'title' => 'Hello world',
            'text' => 'Lorem ipsum dolor sit amet',
        ]);

        $repo->saveDocument('hello', $document);
        $subdir = $repo->createDirectory('subdir');

        $this->assertTrue($repo->hasDirectory('subdir'));

        $subdir['hello2'] = new Document([
            'title' => 'Hello world2',
            'text' => 'Lorem ipsum dolor sit amet2',
        ]);

        $this->assertTrue($subdir->hasDocument('hello2'));
        $this->assertTrue($repo->hasDocument('subdir/hello2'));
        $this->assertTrue(isset($repo['hello']));

        $saved = $repo->getDocument('hello');

        $this->assertInstanceOf(Document::class, $saved);
        $this->assertSame($document, $saved);
        $this->assertSame($document, $repo['hello']);
        $this->assertEquals('Hello world', $saved->title);
        $this->assertEquals('Lorem ipsum dolor sit amet', $saved->text);

        $documents = $repo->getAllDocuments();

        $this->assertArrayHasKey('hello', $documents);
        $this->assertInstanceOf(Document::class, $documents['hello']);

        $repo->deleteDocument('hello');
        $repo->deleteDirectory('subdir');

        $this->assertFalse($repo->hasDocument('hello'));
    }
}
