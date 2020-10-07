<?php

/**
 * In this example we will add/edit the values of "select" fields. That is, fields that
 * offer a dropdown of options to the users. In this example, we will pass the options as
 * text instead of retrieving the IDs of the options first.
 */

// Set the login name of the user whose profile we want to write to.
$id = 'macrini@proximify.ca';
$resources = array(
	'cv/personal_information/identification' =>
	array('applied_for_permanent_residency' => array(1052, 'No')),
	'cv/education/degrees' => array('organization' => array(
		0, 'Aachen Technical University', 'Germany',
		'Not Required', 'Academic'
	)),
	'profile/research_interests' => array('interest' => array(
		0, 'Expert Systems', 'Artificial Intelligence',
		'Communication and Information Technologies'
	))
);

$params = array('id' => $id, 'resources' => $resources);

// Retrieve the data from the server
$response = $client->read($params);

echo ($response) ? 'Done!' : 'Error';
