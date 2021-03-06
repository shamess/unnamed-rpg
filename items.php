<?php

define ('LOGIN', 1);
define ('FORCE_PHASE', true);

require_once ('includes/notextinc.php');
$ext_title = "Inventory";
$ext_css[] = "items.css";
$ext_js[] = relroot."/js/items.js";
include_once ('includes/header.php');

include ('fight_scripts/stat_bar.php');

echo "<p style=\"text-align: center;\"><b>Your items</b> | <span class=\"link\" onclick=\"window.location = 'map.php'\">Back to map</span></p>\n";

echo "<div id=\"characters\">\n";
echo "<div class=\"player main\">\n";
echo "<div class=\"portrait\"><img src=\"".relroot."/images/fight/lupe_combat.gif\" /></div>\n";
echo "<p class=\"name\">".$User->getDetail ('username')."</p>\n";
// we still have $health_bar set up from the stat_bar we included!
echo "<p>Health: <strong class=\"char_health\">".$Character->getDetail ('remaining_hp')."</strong>/".$Character->getMaxHealth()." ".$health_bar."</p>\n";
echo "</div>\n";

echo "<div id=\"equipment\">\n";
// Show teh images of the equipped items in the relevant boxes
$ItemOnHead = $Character->getEquippedItem ("head");
echo "<div class=\"head equip-box\">";
if ($ItemOnHead) {
	echo "<img src=\"asdfd\" height=\"50px\" width=\"50px\" alt=\"".ucfirst ($ItemOnHead->getName ())."\" />";
} else {
	echo "<p>no item</p>";
}
echo "</div>\n";

$ItemInLeftHand = $Character->getEquippedItem ("lefthand");
echo "<div class=\"lefthand equip-box\">";
if ($ItemInLeftHand) {
	echo "<img src=\"asdfd\" height=\"50px\" width=\"50px\" alt=\"".ucfirst ($ItemInLeftHand->getName ())."\" />";
} else {
	echo "<p>no item</p>";
}
echo "</div>\n";

$ItemInRightHand = $Character->getEquippedItem ("righthand");
echo "<div class=\"righthand equip-box\">";
if ($ItemInRightHand) {
	echo "<img src=\"asdfd\" height=\"50px\" width=\"50px\" alt=\"".ucfirst ($ItemInRightHand->getName ())."\" />";
} else {
	echo "<p>no item</p>";
}
echo "</div>\n";
echo "</div>\n";

echo "</div>\n";

echo "<div class=\"clear\">&nbsp;</div>\n";

$Items = $Character->getInventory()->getItems();

if ($Items->count()) {
	echo "<div id=\"invent\">\n";

	echo "<div id=\"item-details\">\n";
	echo "<h2 class=\"item-name\">Inventory</h2>\n";
	echo "<div class=\"item-description\"><p>Click on an item to see more data about it.</p></div>\n";
	echo "<div class=\"item-actions\"></div>\n";
	echo "</div>\n";
	
	echo "<div id=\"items\"><ul id=\"items-list\">\n";
	foreach ($Items as $Il) {
		echo "<li class=\"item\" id=\"itemid".$Il['Item']->getId()."\">";
		echo "<img src=\"asdfd\" height=\"50px\" width=\"50px\" alt=\"".ucfirst ($Il['Item']->getName ())."\" />";
		echo "<span class=\"qty\">".$Il['qty']."</span>";
		echo "</li>\n";
	}
	echo "</ul>\n";
	echo "<div class=\"clear\">&nbsp;</div>\n";
	echo "</div>\n";
	
	echo "<div class=\"clear\">&nbsp;</div>\n";
	
	echo "</div>\n";
} else {
	echo "<p>You don't have any items to use.</p>\n";
}

include_once ('includes/footer.php');
?>