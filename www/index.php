<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Proximify\Uniweb\API\UniwebClient;

$rootDir = __DIR__ . '/..';

$json = file_get_contents("$rootDir/settings/credentials.json");
$credentials = $json ? json_decode($json, true) : [];

$client = new UniwebClient($credentials);

// The query name is the first key of the GET parameters
$queryName = $_GET ? array_key_first($_GET) : false;
$filename = 'home.html';

// Disallow paths with dots (i.e. no relative paths)
if ($queryName && strpos($queryName, '.') === false) {
    $prefix = "$rootDir/queries/$queryName";

    // Some queries are in a sub-folder and others are a single file
    if (is_dir($prefix)) {
        $prefix .= "/$queryName";
    }

    if (is_file("$prefix.php")) {
        $filename = "$prefix.php";
    }
}

include $filename;
