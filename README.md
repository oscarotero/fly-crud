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

//Get the document id (autogenerated)
$id = $document->getId();

//or change it
$document->setId('first-post');

//Get/set more data
$document->title = 'The new title post';

//Save the document
$repo->save($document);

//Get the document again
$document = $repo->get('first-post');

//or delete it
$repo->delete($document);
```

## Working with directories

A subdirectory is like

```php
//Get a document
$document = $repo['first-post'];

//Create or update a document
$repo['new-post'] = [
    'title' => 'Other post',
    'intro' => 'This is the second post'
];

//Check whether a document exists
if (isset($repo['new-post'])) {
    echo 'The post exist';
}

//Delete a document
unset($repo['new-post']);
```
