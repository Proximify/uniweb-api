<?php

/**
 * Edit some of the information on the membership section. 
 * It is important to notice that if there is already an item with data in the 
 * section,  then only the field values that we send will modify existing values. 
 * In other words,  if, for example, we don't send a middle name, and the user 
 * had set a middle name, then the existing middle name will be unchanged.
 */
require_once __DIR__ . '/../src/UniwebClient.php';

use Proximify\Uniweb\API\UniwebClient;

$client = new UniwebClient(UniwebClient::loadCredentials());

// Set the login name of the user whose profile we want to write to.
$id = 'example@proximify.ca';

$resources = ['profile/membership_information' => [
	"first_name" => "Juan",
	"last_name" => "Pedro",
	"account_type" => 1,
	"position_title" => 1,
	"email" => "dmac"
]];

$params = ['id' => $id, 'resources' => $resources];
$response = $client->edit($params);

if ($response) {
	echo "The membership info of user '$id' was modified successfully";
} else {
	echo "Error: Could not modify the membership info of user '$id'";
}
