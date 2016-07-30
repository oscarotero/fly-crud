# fly-crud

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/oscarotero/fly-crud/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/oscarotero/fly-crud/?branch=master)
[![Build Status](https://travis-ci.org/oscarotero/fly-crud.svg?branch=master)](https://travis-ci.org/oscarotero/fly-crud)

Sometimes you don't need a database, just some data in yaml/json/etc files to build a website.

This library provides a simple way to manage data stored in files using [flysystem](http://flysystem.thephpleague.com/) as engine.

## Installation

The library is compatible with PHP >= 5.5 and installable and autoloadable via Composer as [fly-crud/fly-crud](https://packagist.org/packages/fly-crud/fly-crud).

```
$ composer require fly-crud/fly-crud
```

## Usage example:

```php
use FlyCrud\Directory;
use FlyCrud\Formats\Json;

//Create a repository to store the data as json
$repo = Directory::make('/path/to/files', new Json());

//Create a new document
$document = new Document([
    'title' => 'Title post',
    'intro' => 'This is the new post'
]);

//Get/set/edit data
$document->title = 'The new title post';

//Save the document
$repo->saveDocument('first-post', $document);

//Get the document again
$document = $repo->getDocument('first-post');

//or delete it
$repo->deleteDocument('first-post');
```

## Working with directories

Let's say we have the following structure with yaml files:

```
_ site
  |_ posts
    |_ first-post.yml
    |_ second-post.yml
  |_ articles
    |_ first-article.yml
    |_ second-article.yml
    |_ third-article.yml
```

```php
use FlyCrud\Directory;
use FlyCrud\Document;
use FlyCrud\Formats\Yaml;

//Create a repository pointing to our site data using Yaml format:
$site = Directory::make(__DIR__.'/site', new Yaml());

//Get the posts directory
$posts = $site->getDirectory('posts');

//And the first post document
$post = $posts->getDocument('first-post');

//Or store a new document
$posts->saveDocument('third-post', new Document([
    'title' => 'My awesome third post',
    'intro' => 'This is the third post'
]));
```

## Array access and property access

To ease the work with documents and directories:

* Use properties to access to directories (ex: `$site->posts`)
* Use array-like syntax to access to documents (ex: `$posts['first-post']`)

Example with the same structure used previously:

```php
//Access to the first-article document
$article = $site->articles['first-article'];

//Save a new article
$site->articles['other-article'] = new Document([
    'title' => 'I like tomatoes'
    'intro' => 'Yes, they are red, rounded and tasty!'
]);
```

## API

Method | Description
-------|------------
`Directory::getDocument($id)` | Returns a document instance
`Directory::hasDocument($id)` | Check if a document exists
`Directory::saveDocument($id, $document)` | Saves a document in the directory. Override it if it does not exists
`Directory::deleteDocument($id)` | Removes a document in the directory
`Directory::getAllDocuments()` | Returns an array with all documents
`Directory::getDirectory($id)` | Returns an instance with a subdirectory
`Directory::hasDirectory($id)` | Check if a subdirectory exists
`Directory::createDirectory($id)` | Creates a new directory and return it
`Directory::deleteDirectory($id)` | Removes a directory in the directory
`Directory::getAllDirectories()` | Returns an array with all directories

## Working with documents

Documents are clases extending [ArrayObject](http://php.net/manual/en/class.arrayobject.php) with some additions:

* Implements the [JsonSerializable](http://php.net/manual/en/class.jsonserializable.php) interface, so you can convert the document to json easily `json_encode($document)`
* Implements the magic methods `__get()`, `__set()`, `__isset()` and `__unset()`, so you can manipulate the values like properties.
* The data is converted to `stdClass` objects, this allows to manipulate it easily. Example:

```php
use FlyCrud\Document;

//Create a document
$post = new Document([
    'title' => 'My post',
    'tags' => ['php', 'code'],
    'sections' => [
        [
            'title' => 'Section one',
            'body' => 'This is the first section of the document'
        ],[
            'title' => 'Section two',
            'body' => 'This is the second section of the document'
        ]
    ]
]);

//Use the properties to access to the data:
echo $post->title; // "My post"
echo $post->tags[0]; // "php"
echo $post->sections[0]->title; // "Section one"

//Modify the data
$post->section[1]->title = 'New title of the second section';


```


