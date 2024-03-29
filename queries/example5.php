<?php

/**
 * 1. Query member data with the filter "loginName".
 * 2. Iterate over the interests. Each interest is an array with ID, name the the
 * research there, name of the parent of the research them, name of the grand parent of
 * the research theme, and so on. Here I'm just using the base name of the interest.
 */

require_once __DIR__ . '/../src/UniwebClient.php';

use Proximify\Uniweb\API\UniwebClient;

$client = new UniwebClient(UniwebClient::loadCredentials());

// Prepare a 'read' request
$resources = ['profile/membership_information', 'profile/research_interests'];
$filter = ['loginName' => 'example@proximify.ca'];
$params = ['resources' => $resources, 'filter' => $filter];

// Retrieve the data from the server
// The read() function has a second parameter. If true, the response is an array instead
// of an object.
$response = $client->read($params, true);

if (!$response) {
	throw new Exception('Count not find the member');
}

// Since we requested one member, we know that the first key is the ID of the member
$ids = array_keys($response);
$memberId = $ids[0];
$memberData = $response[$memberId];

// Show the data
$client->printResponse($response, 'Member data obtained by using a filter (loginName)');

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
