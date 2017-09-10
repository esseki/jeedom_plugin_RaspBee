<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

// c'est ici que le daemon transmet ses infos

require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";

if (!jeedom::apiAccess(init('apikey'), 'RaspBEE')) {
	echo __('Vous n\'etes pas autoris� � effectuer cette action', __FILE__);	
	die();
}
if (isset($_GET['test'])) {
	echo 'OK';
	die();
}

$results = json_decode(file_get_contents("php://input"));

if (!is_object($results)) {
	die();
}

if ($results->type == "sensors"){


	if (is_object($results->info))
	{
		
	foreach (eqLogic::byType('RaspBEE') as $equipement) {
			//$boucle=$boucle+1;
			echo "id:".$equipement->getId();
			echo "name:".$equipement->getName();
			
			foreach ($equipement->getCmd('info') as $cmd){
				echo "INFO ";
				echo "cmdid:".$cmd->getId();
				echo "cmdname:".$cmd->getName();
				echo "battery:".$results->info->battery;
				if ($cmd->getName()=='Bouton'){
					// on set le niveau de batterie de l'eqlogic
				$equipement->batteryStatus($results->info->battery);
			}
			//$equipement->save(); // ca fait un refresh du dashboard
			//echo "boucle ".$boucle;
			//$JeeOrangeTv->ActionInfo($JeeOrangeTv->getConfiguration('box_ip'));
		}	
		
		
	//echo "{'id':".$results->id.",'battery':".$results->info->battery."}";	
	}
}
	else
	if (is_object($results->action)){
		$boucle=0;
		foreach (eqLogic::byType('RaspBEE') as $equipement) {
			$boucle=$boucle+1;
			echo "id:".$equipement->getId();
			echo "name:".$equipement->getName();
			foreach ($equipement->getCmd('info') as $cmd){
						echo "ACTION ";

				echo "cmdid:".$cmd->getId();
				echo "cmdname:".$cmd->getName();
				if ($cmd->getName()=='Bouton'){
				$cmd->setValue($results->action->buttonevent);				
				$cmd->event($results->action->buttonevent);
				$cmd->save();}
				//echo "cmdvalue:".$cmd->getValue();
			}
			//$equipement->save(); // ca fait un refresh du dashboard
			echo "boucle ".$boucle;
			//$JeeOrangeTv->ActionInfo($JeeOrangeTv->getConfiguration('box_ip'));
		}
		echo "{'id':".$results->id.",'button':".$results->action->buttonevent."}";
		
	}
			
	
}
else
echo json_encode($results->params);


?>