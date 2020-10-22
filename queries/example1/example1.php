<?php

/**
 * In this example we build a Faculty webpage using profile information in the
 * uniweb profile pages. In this example, we select all professors in the Faculty
 * of Engineering.
 */

require_once __DIR__ . '/../../src/UniwebClient.php';
require_once 'markup_utils.php';

use Proximify\Uniweb\API\UniwebClient;

$client = new UniwebClient(UniwebClient::loadCredentials());


$request = [
	'contentType' => 'units',
	'resources' => ['profile/unit_information']
];

$response = $client->read($request, true);

if (isset($response['error'])) {
	throw new Exception($response['error']);
}

$unitType = 'Faculty';
$units = $client->queryUnits($unitType, 'en');
$unitName = $units[1]['name'] ?? false;

if (!$unitName) {
	throw new Exception("Cannot find a '$unitType' unit name");
}

// Get authorized API client
$filter = ['unit' => $unitName, 'title' => 'Professor'];

$resources = [
	'profile/membership_information',
	'profile/research_interests',
	'profile/research_description'
];

$params = ['resources' => $resources, 'filter' => $filter, 'language' => 'en'];

// Retrieve the data from the server (true makes it return an assoc array)
$response = $client->read($params, true);
$items = [];

if (isset($response['error'])) {
	throw new Exception($response['error']);
}

// Create the HTML of items in a table of faculty members
foreach ($response as $memberId => $member) {
	$identification = $member['profile/membership_information'] ?? [];
	$description = $member['profile/research_description'] ?? [];

	if ($description && !empty($description['research_description'])) {
		// Get the field value (has the same name than the section it belongs to
		$description = $description['research_description'];

		$frenchDescription = empty($description['fr']) ? '' : $description['fr'];
		$englishDescription = empty($description['en']) ? '' : $description['en'];

		// Give priority to the French description
		$description = ($frenchDescription) ? $frenchDescription : $englishDescription;
	} else {
		$description = '';
	}

	$name = ($identification['first_name'] ?? '') . ' ' . ($identification['last_name'] ?? '');
	$title = $identification['position_title'] ?? '';

	$interests = $member['profile/research_interests'] ?? [];

	$picture = sprintf(
		'%spicture.php?action=display&contentType=members&id=%d&quality=large',
		$client->getInstanceUrl(),
		$memberId
	);

	// Call the function in markup_utils.php that creates the HTML of a table item
	$items[] = makeTableItem($picture, $name, $title, $interests, $description);
}

// Joint all items in a single string value.
// Note that $tableData is referenced within the page_template.html
$tableData = implode('', $items);

// Include the full page HTML. In there, we echo the value of $tableData.
include 'page_template.html';
