<?php/*** Lets a user attack, and handles the reaction to that.** Returns a json script, handled by the javascript on fight.php.*/define ('LOGIN', true);require_once ('../includes/notextinc.php');$Fight = $Character->getFightData();$Mob = $Fight->getMob();// the reponse will be stored in here, and then json'd and echoed later$r = array ();$r['fight_stage'] = null;// the character needs to be in a fight to be hereif ($Character->getMapData()->getDetail ('phase') != "fight") {	$r['status'] = "bad phase";} else {	$r['status'] = "success";	$r['char']['attack'] = $Fight->attack ();	if ($r['char']['attack']['hit']) {		$r['message'][] = array ('msg' => "You blast a mob for <strong>".$r['char']['attack']['hit_amount']."</strong> damage!", 'type' => "green");				// is the mob guy dead? don't bother giving him a turn if so		if ($Fight->getDetail ('mob_health') <= 0) {			$r['message'][] = array ('msg' => "<strong>You defeated a mob!</strong>", 'type' => "green");			$r['fight_stage'] = $Fight->getStage();		}	} else {		$r['message'][] = array ('msg' => "You attack a mob, but miss!", 'type' => "green");	}}if ($Fight->getDetail ('mob_health') > 0) include ('mob_action.php');echo json_encode ($r);?>