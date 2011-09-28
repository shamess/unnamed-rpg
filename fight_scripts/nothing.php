<?php

/**
* Handles reaction to "do nothing." This is largely just the same as 'trigger mob action only'.
*
* Returns json.
*/

define ('LOGIN', true);
require_once ('../includes/notextinc.php');

$Fight = $Character->getFightData();
$Mob = $Fight->getMob();

// the reponse will be stored in here, and then json'd and echoed later
$r = array ();

$turn_id = FightMessage::createTurnId ($Fight->getId());

$r['fight_stage'] = null;

$Message = FightMessage::addMessage ($turn_id, 1);
$r['message'][] = $Message->getMessageArray ();

$Fight->doNothing ();

include ('mob_action.php');

echo json_encode ($r);

?>