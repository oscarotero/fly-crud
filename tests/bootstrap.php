<?php

error_reporting(E_ALL);

include_once dirname(__DIR__).'/vendor/autoload.php';

PHPUnit_Framework_Error_Notice::$enabled = true;

$tmpdir = __DIR__.'/tmp';

if (!is_dir($tmpdir)) {
    mkdir($tmpdir);
}
