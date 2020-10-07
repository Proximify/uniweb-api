<?php

/**
 * This example is the same as example 1 except for the fact that instead of requesting
 * research interests we request publications.
 */


$filter = array('unit' => 'Engineering', 'title' => 'Professor');
$resources = array('profile/membership_information', 'profile/current_supervision', 'profile/selected_publications');
$params = array('filter' => $filter, 'resources' => $resources);

// Retrieve the data from the server
$response = $client->read($params);

echo '<html lang="en"><head><meta charset="utf-8"></head>';
echo '<body><pre>' . print_r($response, true) . '</pre></body>';
