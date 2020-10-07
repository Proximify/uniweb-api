<?php

// When selecting one member, you can use the property 'id' instead of a filter. In that
// case, the response won't be an array of members but just the member that you need
$id = 'macrini@proximify.ca';
$resources = array('profile/membership_information', 'profile/research_interests');
$params = array('id' => $id, 'resources' => $resources);

// Retrieve the data from the server
// For Bruno: I added a second parameter so that the response is an array instead of an
// object. I find that easier to deal with.

$response = $client->read($params, true);

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

$researchInterestsList = array();

foreach ($interests as $tuple) {
	$researchInterestsList[] = $tuple['interest'][1];
}

// Show the list
$client->printResponse($researchInterestsList, 'List of interests');
