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

$r['fight_stage'] = null;

$Message = FightMessage::addMessage (1, $Character->getId(), $Mob->getId());
$r['message'][] = $Message->getMessageArray ();

$Fight->doNothing ();

include ('mob_action.php');

echo json_encode ($r);

?>