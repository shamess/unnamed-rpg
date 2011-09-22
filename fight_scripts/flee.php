<?php/*** Handles reaction to "flee"** Returns json.*/define ('LOGIN', true);require_once ('../includes/notextinc.php');$Fight = $Character->getFightData();$Mob = $Fight->getMob();// the reponse will be stored in here, and then json'd and echoed later$r = array ();$r['fight_stage'] = null;$outcome = $Fight->flee ();// did they manage to escape, or not?if ($outcome) {	$Message = FightMessage::addMessage (8, $Character->getId(), $Mob->getId());	$r['message'][] = $Message->getMessageArray ();		$r['fight_stage'] = $Fight->getStage ();} else {	// we tried to flee, but failed, so we essentially miss our go	$Message = FightMessage::addMessage (5, $Character->getId(), $Mob->getId());	$r['message'][] = $Message->getMessageArray ();		// the mob still gets their go though!	include ('mob_action.php');}echo json_encode ($r);?>