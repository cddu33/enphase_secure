<?php
try {
	require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";
	//controle APIKEY
	if (!jeedom::apiAccess(init('apikey'), 'enphasesecur')) { //remplacez template par l'id de votre plugin
		echo __('Vous n\'etes pas autorisé à effectuer cette action', __FILE__);
		die();
	}
	$result = file_get_contents("php://input");

    	if ($result == '') {
			//test
        	echo 'OK';
		log::add('enphasesecur', 'debug', 'Test OK');
        	die();
	}//arret du démon pour avec message personnalisé
		elseif ($result == '"error serveur"') {
			log::add('enphasesecur', 'error', 'Erreur de connexion, vérifier les log du daemon et vos identifiants');
		die();
	}
		elseif ($result == '"error check"') {
			log::add('enphasesecur', 'info', 'Erreur contole token, nouvelle tentative dans 60s');
		die();
	}
	elseif ($result == '"error check bis"') {
		log::add('enphasesecur', 'info', 'Erreur contole token, rtenouvellement token');
	die();
}
		elseif ($result == '"error inv"') {
			log::add('enphasesecur', 'info', 'Erreur lors de la récupération du matériel');
		die();
	}
		elseif ($result == '"error arret"') {
			log::add('enphasesecur', 'error', 'Arrêt du démon après 3 tentatives de connexion à la passerelle');
		die();
	}
	//décodage du json
	$enphasesecur_json = json_decode($result, true);
	
	//prod passerelle
	if (isset($enphasesecur_json['production']['0']['wNow'])) {
		log::add('enphasesecur', 'info', 'Réception mesures passerelle');
		if (isset($enphasesecur_json['production']['1']['whLifetime'])) {
			foreach (enphasesecur::byType('enphasesecur', true) as $eqLogic) {
				if ($eqLogic->getConfiguration('type') == 'combine' || $eqLogic->getConfiguration('type') == 'prod') {
					if (config::bykey('typereseau', 'enphasesecur') == 'tri'){
						//phase1
						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['whLifetime'];
						log::add('enphasesecur', 'debug', 'Production 1 depuis la mise en service: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursLifetime1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['whToday'];
						log::add('enphasesecur', 'debug', 'Production 1 totale du jour: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursToday1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['whLastSevenDays'];
						log::add('enphasesecur', 'debug', 'Production 1 totale de la semaine: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursSevenDays1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['wNow'];
						log::add('enphasesecur', 'debug', 'Production 1 instantannée: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattsNow1', $enphasesecur_info);	

						//phase2
						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['whLifetime'];
						log::add('enphasesecur', 'debug', 'Production 2 depuis la mise en service: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursLifetime2', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['whToday'];
						log::add('enphasesecur', 'debug', 'Production 2 totale du jour: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursToday2', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['whLastSevenDays'];
						log::add('enphasesecur', 'debug', 'Production 2 totale de la semaine: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursSevenDays', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['wNow'];
						log::add('enphasesecur', 'debug', 'Production 2 instantannée: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattsNow2', $enphasesecur_info);	

						//phase3
						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['whLifetime'];
						log::add('enphasesecur', 'debug', 'Production 3 depuis la mise en service: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursLifetime3', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['whToday'];
						log::add('enphasesecur', 'debug', 'Production 3 totale du jour: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursToday3', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['whLastSevenDays'];
						log::add('enphasesecur', 'debug', 'Production 3 totale de la semaine: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursSevenDays', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['wNow'];
						log::add('enphasesecur', 'debug', 'Production 3 instantannée: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattsNow3', $enphasesecur_info);	
					}
					//total
					$enphasesecur_info = $enphasesecur_json['production']['1']['whLifetime'];
					log::add('enphasesecur', 'debug', 'Production depuis la mise en service: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('PwattHoursLifetime', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['production']['1']['whToday'];
					log::add('enphasesecur', 'debug', 'Production totale du jour: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('PwattHoursToday', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['production']['1']['whLastSevenDays'];
					log::add('enphasesecur', 'debug', 'Production totale de la semaine: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('PwattHoursSevenDays', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['production']['1']['wNow'];
					log::add('enphasesecur', 'debug', 'Production instantannée: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('PwattsNow', $enphasesecur_info);	
				}
				if ($eqLogic->getConfiguration('type') == 'combine' || $eqLogic->getConfiguration('type') == 'total') {
					if (config::bykey('typereseau', 'enphasesecur') == 'tri'){
						//phase1
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['whLifetime'];
						log::add('enphasesecur', 'debug', 'Consommation totale 1 depuis la mise en service: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursLifetime1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['whToday'];
						log::add('enphasesecur', 'debug', 'Consommation totale 1 du jour: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursToday1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['whLastSevenDays'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 1 de la semaine: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursSevenDays1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['wNow'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 1 instantannée: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattsNow1', $enphasesecur_info);	

						//phase2
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['whLifetime'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 2 depuis la mise en service: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursLifetime2', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['whToday'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 2 du jour: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursToday2', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['whLastSevenDays'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 2 de la semaine: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursSevenDays2', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['wNow'];
						log::add('enphasesecur', 'debug', 'Consommation net instantannée: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattsNow2', $enphasesecur_info);	

						//phase3
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['whLifetime'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 3 depuis la mise en service: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursLifetime3', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['whToday'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 3 du jour: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursToday3', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['whLastSevenDays'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 3 de la semaine: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursSevenDays3', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['wNow'];
						log::add('enphasesecur', 'debug', 'Consommation Totale instantannée: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattsNow3', $enphasesecur_info);	
					}
					//total
					$enphasesecur_info = $enphasesecur_json['consumption']['0']['whLifetime'];
					log::add('enphasesecur', 'debug', 'Consommation Totale depuis la mise en service: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattHoursLifetime', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['consumption']['0']['whToday'];
					log::add('enphasesecur', 'debug', 'Consommation Totale du jour: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattHoursToday', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['consumption']['0']['whLastSevenDays'];
					log::add('enphasesecur', 'debug', 'Consommation Totale de la semaine: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattHoursSevenDays', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['consumption']['0']['wNow'];
					log::add('enphasesecur', 'debug', 'Consommation Totale instantannée: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattsNow', $enphasesecur_info);	
				}
				if ($eqLogic->getConfiguration('type') != 'conv') {
					if (config::bykey('typereseau', 'enphasesecur') == 'tri'){
						//phase1
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['rmsVoltage'];
						log::add('enphasesecur', 'debug', 'Tension réseau 1: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('tension1', $enphasesecur_info);

						//phase2
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['rmsVoltage'];
						log::add('enphasesecur', 'debug', 'Tension réseau 2: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('tension2', $enphasesecur_info);

						//phase3
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['rmsVoltage'];
						log::add('enphasesecur', 'debug', 'Tension réseau 3: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('tension3', $enphasesecur_info);
					}
					//total
					$enphasesecur_info = $enphasesecur_json['consumption']['1']['rmsVoltage'];
					log::add('enphasesecur', 'debug', 'Tension réseau: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('tension', $enphasesecur_info);
				}
				if ($eqLogic->getConfiguration('type') == 'combine' || $eqLogic->getConfiguration('type') == 'net') {
					if (config::bykey('typereseau', 'enphasesecur') == 'tri'){
						//phase1
						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['whLifetime'];
						log::add('enphasesecur', 'debug', 'Consommation Net depuis la mise en service 1: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursLifetimeNet1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['whToday'];
						if ($enphasesecur_info == 0){
							//merci Bison
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['whToday']-$enphasesecur_json['production']['1']['lines']['0']['whToday'];
						}
						log::add('enphasesecur', 'debug', 'Consommation Net du jour 1: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursTodayNet1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['whLastSevenDays'];
						if ($enphasesecur_info == 0){
							//merci Bison
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['whLastSevenDays']-$enphasesecur_json['production']['1']['lines']['0']['whLastSevenDays'];
						}
						log::add('enphasesecur', 'debug', 'Consommation Net de la semaine 1: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursSevenDaysNet1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['wNow'];
						log::add('enphasesecur', 'debug', 'Consommation Net instantannée 1: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattsNowNet1', $enphasesecur_info);	

						if ($enphasesecur_info<0) {
							$eqLogic->checkAndUpdateCmd('Export1', ($enphasesecur_info*(-1)));	
							$eqLogic->checkAndUpdateCmd('Import1', 0);
						}
						else {
							$eqLogic->checkAndUpdateCmd('Import1', ($enphasesecur_info));
							$eqLogic->checkAndUpdateCmd('Export1', 0);
						}
					
						//phase2
						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['whLifetime'];
						log::add('enphasesecur', 'debug', 'Consommation Net depuis la mise en service 2: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursLifetimeNet2', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['whToday'];
						if ($enphasesecur_info == 0){
							//merci Bison
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['whToday']-$enphasesecur_json['production']['1']['lines']['1']['whToday'];
						}
						log::add('enphasesecur', 'debug', 'Consommation Net du jour 2: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursTodayNet2', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['whLastSevenDays'];
						if ($enphasesecur_info == 0){
							//merci Bison
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['whLastSevenDays']-$enphasesecur_json['production']['1']['lines']['1']['whLastSevenDays'];
						}
						log::add('enphasesecur', 'debug', 'Consommation Net de la semaine 2: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursSevenDaysNet2', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['wNow'];
						log::add('enphasesecur', 'debug', 'Consommation Net instantannée 2: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattsNowNet2', $enphasesecur_info);	

						if ($enphasesecur_info<0) {
							$eqLogic->checkAndUpdateCmd('Export2', ($enphasesecur_info*(-1)));	
							$eqLogic->checkAndUpdateCmd('Import2', 0);
						}
						else {
							$eqLogic->checkAndUpdateCmd('Import2', ($enphasesecur_info));
							$eqLogic->checkAndUpdateCmd('Export2', 0);
						}
						
						//phase3
						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['whLifetime'];
						log::add('enphasesecur', 'debug', 'Consommation Net depuis la mise en service 3: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursLifetimeNet3', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['whToday'];
						if ($enphasesecur_info == 0){
							//merci Bison
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['whToday']-$enphasesecur_json['production']['1']['lines']['2']['whToday'];
						}
						log::add('enphasesecur', 'debug', 'Consommation Net du jour 3: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursTodayNet3', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['whLastSevenDays'];
						if ($enphasesecur_info == 0){
							//merci Bison
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['whLastSevenDays']-$enphasesecur_json['production']['1']['lines']['2']['whLastSevenDays'];
						}
						log::add('enphasesecur', 'debug', 'Consommation Net de la semaine 3: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursSevenDaysNet3', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['wNow'];
						log::add('enphasesecur', 'debug', 'Consommation Net instantannée 3: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattsNowNet3', $enphasesecur_info);	

						if ($enphasesecur_info<0) {
							$eqLogic->checkAndUpdateCmd('Export3', ($enphasesecur_info*(-1)));	
							$eqLogic->checkAndUpdateCmd('Import3', 0);
						}
						else {
							$eqLogic->checkAndUpdateCmd('Import3', ($enphasesecur_info));
							$eqLogic->checkAndUpdateCmd('Export3', 0);
						}
					}
					//total
					$enphasesecur_info = $enphasesecur_json['consumption']['1']['whLifetime'];
					log::add('enphasesecur', 'debug', 'Consommation Net depuis la mise en service: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattHoursLifetimeNet', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['consumption']['1']['whToday'];
					if ($enphasesecur_info == 0){
						//merci Bison
						$enphasesecur_info = $enphasesecur_json['consumption'][0]['whToday']-$enphasesecur_json['production'][1]['whToday'];
					}
					log::add('enphasesecur', 'debug', 'Consommation Net du jour: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattHoursTodayNet', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['consumption']['1']['whLastSevenDays'];
					if ($enphasesecur_info == 0){
						//merci Bison
						$enphasesecur_info = $enphasesecur_json['consumption'][0]['whLastSevenDays']-$enphasesecur_json['production'][1]['whLastSevenDays'];
					}
					log::add('enphasesecur', 'debug', 'Consommation Net de la semaine: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattHoursSevenDaysNet', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['consumption']['1']['wNow'];
					log::add('enphasesecur', 'debug', 'Consommation Net instantannée: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattsNowNet', $enphasesecur_info);	

					if ($enphasesecur_info<0) {
						$eqLogic->checkAndUpdateCmd('Export', ($enphasesecur_info*(-1)));	
						$eqLogic->checkAndUpdateCmd('Import', 0);
					}
					else {
						$eqLogic->checkAndUpdateCmd('Import', ($enphasesecur_info));
						$eqLogic->checkAndUpdateCmd('Export', 0);
					}
				}
				//batteries
				if ($eqLogic->getConfiguration('type') == 'combine' || $eqLogic->getConfiguration('type') == 'bat') {
					log::add('enphasesecur', 'debug', 'Nombre de batteries: ' . $enphasesecur_json['storage']['0']['activeCount']);
					if ($enphasesecur_json['storage']['0']['activeCount'] > 0){
						$enphasesecur_info = $enphasesecur_json['storage']['0']['wNow'];
						log::add('enphasesecur', 'debug', 'Production batterie: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('batnow', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['storage']['0']['percentFull'];
						log::add('enphasesecur', 'debug', 'Pourcentage de charge de la batterie: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('batperc', $enphasesecur_info);	
					}
					elseif ($eqLogic->getConfiguration('type') == 'bat') {
						$eqLogic->setIsEnable(0);
						$eqLogic->save();
					}
				}
			}
		}
		else {
			foreach (enphasesecur::byType('enphasesecur', true) as $eqLogic) {
				if ($eqLogic->getConfiguration('type') == 'net' || $eqLogic->getConfiguration('type') == 'total' || $eqLogic->getConfiguration('type') == 'bat') {
					$eqLogic->setIsEnable(0);
					$eqLogic->save();
				}
				elseif ($eqLogic->getConfiguration('type') == 'prod' || $eqLogic->getConfiguration('type') == 'combine')  { 
                                        log::add('enphasesecur', 'debug', 'Envoy-S-Standard-EU');
					
					$enphasesecur_info = $enphasesecur_json['production']['0']['whLifetime'];
					log::add('enphasesecur', 'debug', 'Production depuis la mise en service: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('PwattHoursLifetime', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['production']['0']['wNow'];
					log::add('enphasesecur', 'debug', 'Production instantannée: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('PwattsNow', $enphasesecur_info);				

				}
			}
		}
	}
	//prod convertisseurs
	elseif (isset($enphasesecur_json['0']['serialNumber'])) {
		log::add('enphasesecur', 'info', 'Réception mesures des convertisseurs');

		foreach ($enphasesecur_json as $enphasesecur) {
			$eqLogic = eqLogic::byLogicalId($enphasesecur['serialNumber'], 'enphasesecur');
			if (is_object($eqLogic)) {
				log::add('enphasesecur', 'debug', 'Convertisseurs ' . $enphasesecur['serialNumber'] . ' Puissance: ' . $enphasesecur['lastReportWatts']);
				$eqLogic->checkAndUpdateCmd('Watt', $enphasesecur['lastReportWatts']);
				log::add('enphasesecur', 'debug', 'Convertisseurs ' . $enphasesecur['serialNumber'] . ' Puissance max: ' . $enphasesecur['maxReportWatts']);
				$eqLogic->checkAndUpdateCmd('maxWatt', $enphasesecur['maxReportWatts']);
			}
		}
	}
	//inventaire création des équipements
	elseif (isset($enphasesecur_json[0]['devices'])) {
		log::add('enphasesecur', 'info', 'Réception inventaire');
		foreach ($enphasesecur_json[0]['devices'] as $conv) {
			$newconv = eqLogic::byLogicalId($conv['serial_num'], 'enphasesecur', );
			if (!is_object($newconv)) {
				log::add('enphasesecur', 'info', 'Création convertisseur: '. $conv['serial_num']);
				$newconv = new eqLogic();
				$newconv->setEqType_name('enphasesecur');
				$newconv->setName($conv['serial_num']);
				$newconv->setLogicalId($conv['serial_num']);
				$newconv->setConfiguration('type', 'conv');
				$newconv->setIsVisible(1);
				$newconv->setIsEnable(1);
				$newconv->save();
			}
		}
		foreach (eqLogic::byType('enphasesecur') as $eqLogic) {
            $eqLogic->save();
            log::add('enphasesecur', 'debug', 'Mise à jour des commandes effectuée pour l\'équipement '. $eqLogic->getHumanName());
        }
	}
}
catch (Exception $e) {
	log::add('enphasesecur', 'error', displayException($e)); 
}
