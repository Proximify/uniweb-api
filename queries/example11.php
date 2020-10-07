<?php

/**
 * In this example we will change the profile picture of a user with an image given
 * as a URL. If you want to use a file instead, please read the next example file.
 */

// Set the login name of the user whose profile we want to write to.
$id = 'macrini@proximify.ca';

// We are editing some of the information on the membership section. It is important to
// notice that if there is already an item with data in the section, then only the field
// values that we send will modify existing values. In other words, if, for example,
// we don't send a middle name, and the user had set a middle name, then the existing
// middle name will be unchanged.

$imageUrl = 'http://socialsciences.uottawa.ca/sites/default/files/public/fss_dean-69111_new.png';

$resources = array('profile/picture' => array('url' => $imageUrl));

$request = array('id' => $id, 'resources' => $resources);

$response = $client->updatePicture($request);

if ($response) {
	echo "The membership info of user '$id' was modified successfully";
} else {
	echo "Error: Could not modify the membership info of user '$id'";
}
