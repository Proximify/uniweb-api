<?php

function makeTableItem($picture, $name, $title, $interests, $description)
{
	$numInterests = count($interests);
	$items = array();

	// Note that title and interests are LOVs so their values are arrays with an ID
	// ad the first element, and a display string as the second element. Additional
	// elements corresponds extra info, sich as the parent theme of teh interest, etc
	$title = $title[1];

	for ($i = 0; $i < $numInterests; $i++) {
		$classes = 'profiles-inline-cells profiles-td-nth-child-3-star';

		if ($i == $numInterests - 1)
			$classes .= ' profiles-td-li-nth-child-3-after';

		$items[] = sprintf('<li class="%s">%s</li>', $classes, $interests[$i]['interest'][1]);
	}

	$str = sprintf('
	<tr class="profile accordion-panel collapsed">
		<td class="profiles-td-nth-child-1">
			<img src="%1$s" width="65" alt=""></td>
			<td class="profiles-td-nth-child-2"><h6 class="profiles-inline-cells">%2$s</h6>, 
			<br>%3$s
		</td>
		<td class="profiles-td-nth-child-3">
			<h6 class="inline profiles-inline-cells profiles-td-nth-child-3-star">Research Interests</h6>: 
			<ul class="profiles-td-nth-child-3-star">
				%4$s
			</ul>
			<p class="profiles-td-nth-child-3-star">&nbsp;</p>
		</td>
	</tr>
	<tr style="display: none;">
		<td colspan="4" class="profiles-td-nth-child-1">
			<h6 class="inline">Complete Profile:</h6> <p class="inline"><a href="http://geography.utoronto.ca/profiles/christian-abizaid/">Including degrees, honours and awards, research interests, publications and more.</a></p><h6>Contact Information</h6><p><strong>Joint Appointment with School of the Environment</strong><br>
			Location: Department of Geography and Program in Planning<br>
			Room 5055, Sidney Smith Hall (100 St. George Street)<br>
			Toronto ON, M5S 3G3<br>
			Tel: 416.978.3373<br>
			Fax: 416.946.3886<br>
			Email: christian.abizaid@utoronto.ca</p>
			<h6>Home Campus:</h6>U of T St. George<h6>Call for Students</h6><h4>%5$s</h4>
			<h4></h4>
		</td>
	</tr>', $picture, $name, $title, implode('', $items), $description);

	return $str;
}
