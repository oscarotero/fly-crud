<?php
declare(strict_types = 1);

namespace FlyCrud\Tests;

use FlyCrud\Document;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testDocument()
    {
        $doc = new Document([
            'title' => 'hello world',
            'tags' => ['one', 'two'],
            'sections' => [
                ['title' => 'Section 1'],
                ['title' => 'Section 2'],
                ['title' => 'Section 3'],
            ],
        ]);

        $this->assertEquals('hello world', $doc['title']);
        $this->assertEquals($doc['title'], $doc->title);
        $this->assertEquals($doc['tags'], $doc->tags);
        $this->assertEquals('Section 1', $doc->sections[0]->title);

        $doc['sections'][1]->title = 'new title2';
        $this->assertEquals('new title2', $doc->sections[1]->title);
    }
}
