<?php

/**
* Holds all the includes that are always needed, but only ones which *do not* output any text. This means that headers
* and things can be changed using information from the database.
*/

require_once ('config.php');

require_once ('StandardObject.class.php');

require_once ('database.php');
require_once ('common.php');
require_once ('iterable.class.php');

require_once ('user.class.php');

require_once ('fightmessage.class.php');

require_once ('item.class.php');

require_once ('character.class.php');
require_once ('character.map.class.php');
require_once ('character.fight.class.php');
require_once ('character.invent.class.php');

require_once ('basestats.class.php');

require_once ('mapgrid.class.php');

require_once ('mob.class.php');

// session must be started after we include (define) the user class (since we're storing a User in
// a session.)
session_start ();

if (isset ($_SESSION['user'])) $User = $_SESSION['user'];

require_once ('access.php');

if (isset ($Character) && defined ('FORCE_PHASE')) {
	// this is a good place to sort out 'phases' and to explain what they are. a 'phase' is just the stage of the game the
	// user is currently at: be it walking around the map, fighting a mob, or on a special page. A user can be standing on
	// a special page coordinate, but not actually have selected to view the page yet. Similarly, if a user is in a mob fight
	// buy then goes to map.php, they should be redirected back to the fight - can't escape that easily!
	$current_phase = $Character->getMapData()->getDetail ('phase');
	
	$current_page = basename ($_SERVER['PHP_SELF']);
	
	// this if branch will just get skipped over if they are on the correct page.
	if (($current_page != "map.php" && $current_page != "items.php") && $current_phase == 'map') {
		header ('Location: '.relroot.'/map.php');
	} elseif ($current_phase == 'fight') {
		// they're in some sort of fight page
		$Fight = $Character->getFightData();
	
		// there are two pages a user could be on in a fight.
		// there's the fight page, which they're on if the fight isn't set to complete
		if ($current_page != "fight.php" && $Fight->getDetail ('complete') == 0) {
			header ('Location: '.relroot.'/fight.php');
		} elseif ($current_page != "aftermath.php" && $Fight->getDetail ('complete') == 1) {
			// since the fight is complete but we're still in a fight, we're on the aftermath page.
			header ('Location: '.relroot.'/fight_scripts/aftermath.php');
		}
	} elseif ($current_phase == 'special') {
		// not developed yet.
	}
}
?>