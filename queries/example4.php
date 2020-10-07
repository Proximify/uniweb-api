<?php

/**
 * In this example we add new items into a CV section. In particular, we chose a
 * section that includes two kinds of special fields: bilingual fields and section fields.
 * This two types of fields are special because their values are arrays that must have
 * a particular structure. The bilingual are straight forward. They must have an 'english'
 * and a 'french' property (although it is valid to omit a language if there is no value
 * for it).
 * The fields that are of type 'section' represent subsections of the section to where
 * the field belongs. The value of a section field must be an array of items.
 *
 * In addition, the example below show that the API request can be give as a JSON string
 * or as a regular PHP array. Depending the context one method can be more convenient
 * that the other. Here the first request is defined as a JSON string and the other
 * ones as PHP arrays.
 */

$id = 'macrini@proximify.ca';
$sectionName = 'cv/contributions/presentations';
$subsectionName = $sectionName . '/funding_sources';

$resources = array($sectionName);
$params = array('id' => $id, 'resources' => $resources);
$response = $client->clear($params);

if (!$response) {
	echo "Error: Could not clear the section: $sectionName";
	exit;
}

/**
 * Steps: Each new item added to $sectionName will define a presentation_title, a
 * description_contribution_value and a number of funding_sources.
 *
 * The funding_sources is a subsection so it expects an array of subitems. We begin
 * by defined one funding source. Since the funding_sources subsection is composed of
 * an LOV field name funding_organization, we need to find a valid ID to assign to it.
 * In this example, we assume that the funding organization is 'Natural Sciences and
 * Engineering Research Council of Canada (NSERC)'.
 */

// Get the options for all fields in the section and subsection to which we will add
// new items

$resources = array($sectionName, $subsectionName);
$response = $client->getOptions($resources);
var_dump($response);

$organizations = $response->{$subsectionName}->funding_organization;
$org1 = 'Natural Sciences and Engineering Research Council of Canada (NSERC)';
$orgId1 = $client->findFieldOptionId($organizations, $org1);

// SUBITEM: Create one subitem to add to the funding sources of the main item.
$fundingItem1 = array(
	'funding_organization' => $orgId1,
	'funding_reference_number' => 1
);

// MAIN ITEMS: Defined items to insert
$sectionItem1 = array(
	'presentation_title' => 'TED Talk',
	'description_contribution_value' => array(
		'english' => 'Hi there',
		'french' => 'Alo'
	),
	'funding_sources' => array(
		$fundingItem1
	)
);

$sectionItem2 = array(
	'presentation_title' => 'UN Talk',
	'description_contribution_value' => array(
		'english' => 'There is only this English value'
	)
);

$resources = array($sectionName => array($sectionItem1, $sectionItem2));
$params = array('resources' => $resources, 'id' => $id);
$response = $client->add($params); // add items

if ($response) {
	echo "Items were added for user '$id'";
} else {
	echo "Error: Could not add  new items for user '$id'";
}
