<?php

/**
 * In this example we will add two research interests to the profile of a user. We will
 * also check if the interests are already present in the profile before inserting them.
 *
 * We will find their ID of the interests to insert by requesting the option values
 * of the "interest" field. The interests are hierarchical and there can be duplicate names.
 * For example, the research theme "Ethics" exists under several parent research areas.
 * In this cases, it is recommended to specify at least one parent area to select
 * the proper theme.
 *
 * We assume that 'Artificial Intelligence' is unique and so we won't specify that it
 * must exist under "Communication and Information Technologies", which itself
 * exists under "Natural Sciences and Engineering". However, in a real scenario the full
 * hierarchy should be specified. For the second interest, 'Experts Systems', we will
 * require that it is the one under 'Artificial Intelligence'. In general, it is necessary
 * to specify the full "path" of a theme to be sure that we are selecting the right one.
 */

// Set the login name of the user whose profile we want to write to.
$id = 'macrini@proximify.ca';
$resources = array('profile/research_interests/_fields_/interest');
$params = array('resources' => $resources, 'id' => $id);

// Retrieve the current interests of the member
$result = $client->read($params);
var_dump($result);

$currentInterests = array();

foreach ($result->{'profile/research_interests'} as $item) {
	$currentInterests[] = $item->interest;
}

$interest1 = 'Artificial Intelligence';
$interest2 = array('Expert Systems', 'Artificial Intelligence');

// Use the client API helper function below to see if the user already have
// the interests that we want to add
$interestId1 = $client->findFieldOptionId($currentInterests, $interest1);
$interestId2 = $client->findFieldOptionId($currentInterests, $interest2);

if ($interestId1 || $interestId2) {
	echo "The user $id already have at least one of the two interests.";
}

// Get the value options for field 'interest' in section 'research_interests']
$resources = array('profile/research_interests/_fields_/interest');
$options = $client->getOptions($resources);
$options = $options->{'profile/research_interests'}->interest;


// Use the client API helper function below to find the ID of the interests that we
// want to add
$interestId1 = $client->findFieldOptionId($options, $interest1);
$interestId2 = $client->findFieldOptionId($options, $interest2);


if (!$interestId1 || !$interestId2) {
	$msg = 'Could not find an ID for: ';

	if (!$interestId1) {
		echo $msg . print_r($interest1, true);
	}

	if (!$interestId2) {
		echo $msg . print_r($interest2, true);
	}

	exit;
}

$resources = array('profile/research_interests' => array(
	array('interest' => $interestId1),
	array('interest' => $interestId2)
));
$params = array('resources' => $resources, 'id' => $id);
$response = $client->add($params);

if ($response) {
	echo "'$interest1' and '$interest2[0]' were added as new interests for user '$id'";
} else {
	echo "Error: Could not add as new interests for user '$id'";
}
