<?php/*** Handles reaction to "flee"** Returns json.*/define ('LOGIN', true);require_once ('../includes/notextinc.php');$Fight = $Character->getFightData();$Mob = $Fight->getMob();// the reponse will be stored in here, and then json'd and echoed later$r = array ();$r['fight_stage'] = null;$outcome = $Fight->flee ();if ($outcome) {	$r['message'][] = array ('msg' => "You succeeded in escaping from a mob!", 'type' => "green");		$r['fight_stage'] = $Fight->getStage ();} else {	// we tried to flee, but failed, so we essentially miss our go	$r['message'][] = array ('msg' => "You tried to escape, but a mob blocked the way!", 'type' => "green");		// the mob still gets their go though!	include ('mob_action.php');}echo json_encode ($r);?>