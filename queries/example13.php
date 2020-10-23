<?php

/**
 * Request a list of members from the DB table Members.
 * By not specifying "resources", the API interprets that the desired
 * response is simply a list of content-types items.
 */

require_once __DIR__ . '/../src/UniwebClient.php';

use Proximify\Uniweb\API\UniwebClient;

$client = new UniwebClient(UniwebClient::loadCredentials());

$units = $client->queryUnits(['sortBy' => 'memberCount']);
$unit = array_pop($units); // last unit

if (!$unit) {
    throw new Exception("Cannot find a unit");
}

$unitId = $unit['contentId'];


// If "recourses" is not given, the query will fetch from a DB table
// of the requested content type. That is the fastest type of query.
// Profile resources are comparatively slower.
$request = [
    'action' => 'read',
    'contentType' => 'members',
    'filter' => [
        'units' => [$unitId]
    ]
];

$response = $client->sendRequest($request);

$unitName = $unit['unitName'];
$client->printResponse($response, "List of members in unit '$unitName':");
