<?php/*** AJAX JSON returning script. Returns data about what actions are currently possible.*/define ('LOGIN', true);require_once ('../includes/notextinc.php');$r = array ();// the character needs to be in a fight to be hereif ($Character->getMapData()->getDetail ('phase') != "fight") {	$r['status'] = "bad phase";} else {	$r['status'] = "success";	$r['actions'] = array ();	// go through each of the items the user has...	$Items = $Character->getInventory()->getItems();	foreach ($Items as $Il) {		$actions = $Il['Item']->getActions ();				foreach ($actions as $action) {			if ($action['in_fight']) {				$r_actions = array ();								$r_actions['string'] = $action['action_type']." ".$Il['Item']->getName (true)." (you're carrying ".$Il['qty'].")";								if ($Il['Item']->getDetail ('type') == 'Healing Potion')					$r_actions['string'] .= " (Heals for ".$action['modifier'].")";								$r_actions['item_id'] = $Il['Item']->getId();								$r_actions['action_type'] = $action['action_type'];								$r['actions'][] = $r_actions;			}		}	}}echo json_encode ($r);?>