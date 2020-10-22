<?php

/**
 * 1. Query by ID (single result instead of array).
 * 2. Iterate over the interests.
 */

require_once __DIR__ . '/../src/UniwebClient.php';

use Proximify\Uniweb\API\UniwebClient;

$client = new UniwebClient(UniwebClient::loadCredentials());

// When selecting one member, you can use the property 'id' instead of a filter. In that
// case, the response won't be an array of members but just the member that you need
$id = 'macrini@proximify.ca';
$resources = ['profile/membership_information', 'profile/research_interests'];
$params = ['id' => $id, 'resources' => $resources];

// Retrieve the data from the server
$response = $client->read($params);

if (!$response) {
	throw new Exception('Count not find the member');
}

// It's now easy to get the member's data
$memberData = $response;
$client->printResponse($response, 'Member data requested by ID (the fastest method)');

$info = $memberData['profile/membership_information'];
$interests = $memberData['profile/research_interests'];

$firstName = $info['first_name'];
$lastName = $info['last_name'];

// We can iterate over the interests. Each interest is an array with ID, name the the
// research there, name of the parent of the research them, name of the grand parent of
// the research theme, and so on. Here I'm just using the base name of the interest.

$researchInterestsList = [];

foreach ($interests as $tuple) {
	$researchInterestsList[] = $tuple['interest'][1];
}

// Show the list
$client->printResponse($researchInterestsList, 'List of interests');
