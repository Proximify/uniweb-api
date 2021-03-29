<?php

/**
 * In this example we will edit user's profile section.
 */

require_once __DIR__ . '/../src/UniwebClient.php';

use Proximify\Uniweb\API\UniwebClient;

$client = new UniwebClient(UniwebClient::loadCredentials());

$id = 'example@proximify.ca';
$resources = ['cv/user_profile'];
$readParams = ['id' => $id, 'resources' => $resources];

$response = $client->read($readParams);
$client->printResponse($response, 'User profile in CV before "edit"');

$currentInterests = [];

// Create a bilingual string
$bilingualStr = [
	'english' => 'Artificial intelligence',
	'french' => 'Intelligence artificielle'
];

$resources = ['cv/user_profile' => ['research_interests' => $bilingualStr]];
$editParams = ['id' => $id, 'resources' => $resources];
$response = $client->edit($editParams);

if ($response) {
	echo "The CV user profile section of user '$id' was modified successfully";
} else {
	echo "Error: Could not modify the membership info of user '$id'";
}

$response = $client->read($readParams);
$client->printResponse($response, 'User profile in CV after "edit"');
