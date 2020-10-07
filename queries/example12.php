<?php

/**
 * In this example we will change the profile picture of a user with an image given as a
 * local file path. If you want to use a URL instead, please read the previous example file.
 */

// Set the login name of the user whose profile we want to write to.
$id = 'macrini@proximify.ca';

// We are editing some of the information on the membership section. It is important to
// notice that if there is already an item with data in the section, then only the field
// values that we send will modify existing values. In other words, if, for example,
// we don't send a middle name, and the user had set a middle name, then the existing
// middle name will be unchanged.

$imagePath = '/Users/Shared/EEM-small-size.png';
$mimeType = 'image/' . pathinfo($imagePath, PATHINFO_EXTENSION);
$fileName = 'profilePicture'; // A unique name for the file (with no periods in it)

$resources = array('profile/picture' => array('attachment' => $fileName));

$request = array('id' => $id, 'resources' => $resources);

$client->addFileAttachment($request, $fileName, $imagePath, $mimeType);

$response = $client->updatePicture($request);

if ($response) {
	echo "The membership info of user '$id' was modified successfully";
} else {
	echo "Error: Could not modify the membership info of user '$id'";
}
