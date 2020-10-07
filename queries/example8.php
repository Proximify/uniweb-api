<?php

/**
 * In this example we will edit user's profile section.
 */

$id = 'macrini@proximify.ca';
$resources = array('cv/user_profile');
$readParams = array('id' => $id, 'resources' => $resources);

$response = $client->read($readParams);
$client->printResponse($response, 'User profile in CV before "edit"');

$currentInterests = array();

// Create a bilingual string
$bilingualStr = array(
	'english' => 'Artificial intelligence',
	'french' => 'Intelligence artificielle'
);

$resources = array('cv/user_profile' => array('research_interests' => $bilingualStr));
$editParams = array('id' => $id, 'resources' => $resources);
$response = $client->edit($editParams);

if ($response) {
	echo "The CV user profile section of user '$id' was modified successfully";
} else {
	echo "Error: Could not modify the membership info of user '$id'";
}

$response = $client->read($readParams);
$client->printResponse($response, 'User profile in CV after "edit"');
