<?php

/**
* Ouputs XML document which draws the map which is loaded with javascript. Also handles movement.
*
* Concept:
* Each row in the map database is a coordinate of the world.
*
* `type`:
*   1 - normal passable square
*   2 - high risk area
*   4 - special square that can be entered. A URL to take the player to is in the map_special table
*   5 - impassable square
*	6 - low risk area
*
* It'll output a document like this
<?xml version="1.0" ?>
<root>
	<map_html>
		<!-- new map data that can just be injected overtop the current map -->
	</map_html>
	<navigation_data>
		<!-- html that's dropped overtop of the navigation section to the right of the map -->
	</navigation_data>
</root>
*/

define ('LOGIN', true);
require_once ('../includes/notextinc.php');

header ('Content-Type: text/xml');
echo "<?xml version=\"1.0\" ?>\n";

$CharMap = $Character->getMapData();

// Are we moving?
$old_x = $CharMap->getX();
$old_y = $CharMap->getY();
// Sort out user directions, if they're moving.
$moved = false;
if (isset ($_POST['move'])) {
	if ($_POST['move'] == "west") {
		// work out new location
		$new_x = $old_x - 1;
		$new_y = $old_y;
		$moved = true;
	} elseif ($_POST['move'] == "east") {
		$new_x = $old_x + 1;
		$new_y = $old_y;
		$moved = true;
	} elseif ($_POST['move'] == "north") {
		$new_x = $old_x;
		$new_y = $old_y - 1;
		$moved = true;
	} elseif ($_POST['move'] == "south") {
		$new_x = $old_x;
		$new_y = $old_y + 1;
		$moved = true;
	} elseif ($_POST['move'] == "nw") {
		$new_x = $old_x - 1;
		$new_y = $old_y - 1;
		$moved = true;
	} elseif ($_POST['move'] == "ne") {
		$new_x = $old_x + 1;
		$new_y = $old_y - 1;
		$moved = true;
	} elseif ($_POST['move'] == "sw") {
		$new_x = $old_x - 1;
		$new_y = $old_y + 1;
		$moved = true;
	} elseif ($_POST['move'] == "se") {
		$new_x = $old_x + 1;
		$new_y = $old_y + 1;
		$moved = true;
	}
}
// if user has moved
if ($moved) {
	// get map data for new location
	$new_MapGrid = MapGrid::byCoord ($CharMap->getId(), $new_x, $new_y);

	// check if new location is passable
	if ($new_MapGrid->getDetail ('type') != 5) {
		// move to new location
		$CharMap->setDetail ('x_co', $new_x);
		$CharMap->setDetail ('y_co', $new_y);
	} else {
		// the block they're trying to move into isn't passable, so don't move them.
		$impassable = true;
	}

	// first check if they're movement has caused a mob to spawn and attack
	if ($CharMap->rollSpawnMob()) {
		// fight started, redirect to fight page
		echo "<goto page=\"fight\"/>";
		exit;
	}
}

echo "<root>";

// inside here needs to be HTML that looks like the map. HTML! Not XML nodes, so the HTML chars need to be escaped.
// to keep the code looking pretty, and not litering it with &gt;'s and &lt;'s, we'll put all the output that's supposed
// to be in this node into a variable, and then run it through and escape function later at the end.
$map_data_output = "";
echo "<map_data>";

// what range do we want to be showing the user? this is also the width and height of the map. Because
// of that, it makes sense for it to be odd so that there's a centre point to the map, which we can put
// the user in.
// no object or method to get meta data yet...
$map_los = $Database->getSingleValue ("SELECT `sight_distance` FROM `map_data` WHERE `map_id` = ".$CharMap->getId());

// now we can work out what coordinates the user can see
$x_smallest = $CharMap->getX() - $map_los;
$x_largest = $CharMap->getX() + $map_los;
$y_smallest = $CharMap->getY() - $map_los;
$y_largest = $CharMap->getY() + $map_los;

// Make sure that the max/min coordinates don't go over the side of the possible map squares...
// what's the smallest x?
$map_x_min = $Database->getSingleValue ("SELECT `x_co` FROM `map` WHERE `map_id` = ".$CharMap->getId()." ORDER BY `x_co` ASC LIMIT 1");
$map_y_min = $Database->getSingleValue ("SELECT `y_co` FROM `map` WHERE `map_id` = ".$CharMap->getId()." ORDER BY `y_co` ASC LIMIT 1");
$map_x_max = $Database->getSingleValue ("SELECT `x_co` FROM `map` WHERE `map_id` = ".$CharMap->getId()." ORDER BY `x_co` DESC LIMIT 1");
$map_y_max = $Database->getSingleValue ("SELECT `y_co` FROM `map` WHERE `map_id` = ".$CharMap->getId()." ORDER BY `y_co` DESC LIMIT 1");

// check left boundary
if ($x_smallest < $map_x_min) {
	$x_smallest = $map_x_min;
	$x_largest = $map_x_min + ($map_los*2);
}

// check right boundary
if ($x_largest > $map_x_max) {
	$x_smallest = $map_x_max - ($map_los*2);
	$x_largest = $map_x_max;
}

// check top boundary
if ($y_smallest < $map_y_min) {
	$y_smallest = $map_y_min;
	$y_largest = $map_y_min + ($map_los*2);
}

// check bottom boundary
if ($y_largest > $map_y_max) {
	$y_smallest = $map_y_max;
	$y_largest = $map_y_max + ($map_los*2);
}

// nab all the coords that we've decided we can show on the map that the user is on
$qry_mapgrid = $Database->query ("SELECT * FROM `map` WHERE `x_co` >= ".$x_smallest." AND `x_co` <= ".$x_largest." AND `y_co` >= ".$y_smallest." AND `y_co` <= ".$y_largest." AND `map_id` = ".$CharMap->getId());

// for the first row of the map, put the border around it
$map_data_output = "<div><span><img src=\"".relroot."/images/map_ui/brd_tl.gif\"/></span>";
for ($i=0;$i <= ($map_los*2); $i++) $map_data_output .= "<span><img src=\"".relroot."/images/map_ui/brd_t.gif\"/></span>";
$map_data_output .= "<span><img src=\"".relroot."/images/map_ui/brd_tr.gif\"/></span></div>";

$map_data_output .= "<div><span><img src=\"".relroot."/images/map_ui/brd_l.gif\"/></span>";

// draw map
$in_row = 0; $rows = 0;
// loop through all the coords that we've been given. this data will include the image to show.
while ($map_array = mysql_fetch_array ($qry_mapgrid)) {
	// if the user is on this square then we just want to show the user's avatar here. Otherwise just show the actual
	// image that belongs to the coord.
	if ($CharMap->getX() == $map_array['x_co'] AND $CharMap->getY() == $map_array['y_co']) {
		// this is the coord the user is on - this data will be helpful later on in the navigation_data
		$user_MapGrid = $CharMap->getGrid ();
	
		$image = "lupe_".$map_array['image'];
	} else {
		$image = $map_array['image'];
	}

	$map_data_output .= "<span><img src=\"".relroot."/images/map_images/".$image."\" ";
	//  if this is a development server then show the grid's coordinants
	if (status == "dev") $map_data_output .= " title=\"x: ".$map_array['x_co'].", y: ".$map_array['y_co'].", grid_id: ".$map_array['grid_id']."\" ";
	$map_data_output .= "alt=\"Map square\"/></span>";

	// add one to count. this increases with each coord output so we know how many we've gone across the X axis.
	$in_row++;

	// check to see if it's time to drop down to a new row yet
	if ($in_row > $map_los*2) {
		$map_data_output .= "<span><img src=\"".relroot."/images/map_ui/brd_r.gif\"/></span></div>";
		// set the X axis back to zero
		$in_row = 0;
		
		// we need to count the number of rows we've output so far
		$rows++;
		
		// if we've not just output the last row then we'll need to start another
		if (($map_los*2)+1 != $rows) {
			$map_data_output .= "<div><span><img src=\"".relroot."/images/map_ui/brd_l.gif\"/></span>";
		} else {
			$map_data_output .= "<div><span><img src=\"".relroot."/images/map_ui/brd_bl.gif\"/></span>";
			for ($i=0;$i <= ($map_los*2); $i++) $map_data_output .= "<span><img src=\"".relroot."/images/map_ui/brd_b.gif\"/></span>";
			$map_data_output .= "<span><img src=\"".relroot."/images/map_ui/brd_br.gif\"/></span></div>";
		}
	}
}

echo htmlspecialchars ($map_data_output);
echo "</map_data>";

$nav_data_output = "";
echo "<navigation_data>";

if (isset ($impassable)) $nav_data_output .= "<p class=\"error\">You can't move here.</p>";

$nav_data_output .= "<p>You are in ".$user_MapGrid->getDetail ('locality').".</p>";

// sometimes there are actions that can be done whilst the user is on this coordinate, like enter a special place or town,
// or pick up an item lying on the floor, or talk to someone. (these special squares have the ID 4.)
if ($user_MapGrid->getDetail ('type') == 4) {
	// get the special map data, which includes the text to output as the link, and the URL the link should take them to
	$special_map_data = $Database->query ("SELECT * FROM `map_special` WHERE `grid_id` = ".$user_MapGrid->getId());

	// it might be special, but it might be lacking data
	if (!is_empty ($special_map_data['goto_uri'], $special_map_data['goto_name']))
		$nav_data_output .= "<p><a href=\"".relroot.$special_map_data['goto_uri']."\">".$special_map_data['goto_name']."</a></p>";
}
echo htmlspecialchars ($nav_data_output);
echo "</navigation_data>";

echo "</root>";

?>