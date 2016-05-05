<?php

namespace FlyCrud\tests;

use FlyCrud\Document;
use FlyCrud\Repository;
use FlyCrud\JsonRepository;
use FlyCrud\YamlRepository;

class SimpleTest extends Base
{
    public function testJson()
    {
        $repo = self::createRepo(JsonRepository::class, 'json');

        $this->assertInstanceOf(JsonRepository::class, $repo);

        $this->commonTests($repo);
    }

    public function testYaml()
    {
        $repo = self::createRepo(YamlRepository::class, 'yaml');

        $this->assertInstanceOf(YamlRepository::class, $repo);

        $this->commonTests($repo);
    }

    private function commonTests(Repository $repo)
    {
        $document = new Document([
            'title' => 'Hello world',
            'text' => 'Lorem ipsum dolor sit amet',
        ], 'hello');

        $repo->save($document);

        $repo['hello2'] = [
            'title' => 'Hello world2',
            'text' => 'Lorem ipsum dolor sit amet2',
        ];

        $this->assertTrue(isset($repo['hello']));
        $this->assertTrue($repo->has('hello2'));

        $saved = $repo->get('hello');

        $this->assertInstanceOf(Document::class, $saved);
        $this->assertSame($document, $saved);
        $this->assertSame($document, $repo['hello']);
        $this->assertEquals('Hello world', $saved->title);
        $this->assertEquals('Lorem ipsum dolor sit amet', $saved->text);

        $all = $repo->getAll();

        $this->assertArrayHasKey('hello', $all);
        $this->assertInstanceOf(Document::class, $all['hello']);

        $repo->delete($document);
        unset($repo['hello2']);

        $this->assertFalse($repo->has('hello'));
        $this->assertFalse($repo->has('hello2'));
    }
}
