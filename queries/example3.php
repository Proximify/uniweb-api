<?php

/**
 * In this example we focus on the actions to request information about sections, fields
 * and the valid options for drop-down fields.
 */

require_once __DIR__ . '/../src/UniwebClient.php';

use Proximify\Uniweb\API\UniwebClient;

$client = new UniwebClient(UniwebClient::loadCredentials());

// Request the information about a section, its field names and the type of each field.
$resources = array('cv/contributions/presentations');
$response = $client->getInfo($resources);
$client->printResponse($response, 'Section info');

// Request the information for some specific fields in the section.
$resources = array('cv/contributions/presentations/_fields_/main_audience/invited');
$response = $client->getInfo($resources);
$client->printResponse($response, 'Fields info');

// Request the valid options for some LOV fields.
$resources = array('cv/contributions/presentations/_fields_/main_audience/invited');
$response = $client->getOptions($resources);
$client->printResponse($response, 'Fields options');
