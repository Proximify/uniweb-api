<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Proximify\Uniweb\API\UniwebClient;

// The query name is the first key of the GET parameters
if ($queryName = $_GET ? array_key_first($_GET) : false) {
    $params = [
        'rootDir' => __DIR__ . '/..',
        'queryName' => $queryName
    ];

    UniwebClient::outputQuery($params);
} else {
    include 'home.html';
}
