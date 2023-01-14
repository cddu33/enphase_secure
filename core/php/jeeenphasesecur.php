<?php

try {
    require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";

    if (!jeedom::apiAccess(init('apikey'), 'enphasesecur')) { //remplacez template par l'id de votre plugin
        echo __('Vous n\'etes pas autorisé à effectuer cette action', __FILE__);
        die();
    }
	$result = file_get_contents("php://input");

    if ($result == '') {
        echo 'OK';
		log::add('enphasesecur', 'debug', 'Test OK');
        die();
    }
	elseif ($result == '"error serveur"') {
		log::add('enphasesecur', 'error', 'Erreur de connexion, vérifier les log du daemon et vos identifiants');
        die();
    }
	elseif ($result == '"error check"') {
		log::add('enphasesecur', 'error', 'Mauvais token ou renouvellement');
        die();
    }
	elseif ($result == '"error inv"') {
		log::add('enphasesecur', 'error', 'Erreur lors de la récupération du matériel');
        die();
    }
	elseif ($result == '"error arret"') {
		log::add('enphasesecur', 'error', 'Arrêt du démoinn après 3 tentative de connexion à la');
        die();
    }
	
    $enphasesecur_json = json_decode($result, true);
	//prod passerelle
	if (isset($enphasesecur_json['production']['0']['wNow'])) {
		log::add('enphasesecur', 'info', 'Réception mesures passerelle');
		if (isset($enphasesecur_json['production']['1']['whLifetime'])) {
			foreach (enphasesecur::byType('enphasesecur', true) as $eqLogic) {
				if ($eqLogic->getConfiguration('type') == 'combine' || $eqLogic->getConfiguration('type') == 'prod') {
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
					$enphasesecur_info = $enphasesecur_json['consumption']['0']['whLifetime'];
					log::add('enphasesecur', 'debug', 'Consommation totale depuis la mise en service: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattHoursLifetime', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['consumption']['0']['whToday'];
					log::add('enphasesecur', 'debug', 'Consommation totale du jour: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattHoursToday', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['consumption']['0']['whLastSevenDays'];
					log::add('enphasesecur', 'debug', 'Consommation Net de la semaine: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattHoursSevenDays', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['consumption']['0']['wNow'];
					log::add('enphasesecur', 'debug', 'Consommation net instantannée: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattsNow', $enphasesecur_info);	
				}

				if ($eqLogic->getConfiguration('type') != 'conv') {
					$enphasesecur_info = $enphasesecur_json['consumption']['0']['rmsVoltage'];
					log::add('enphasesecur', 'debug', 'Tension réseau: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('tension', $enphasesecur_info);
				}

				if ($eqLogic->getConfiguration('type') == 'combine' || $eqLogic->getConfiguration('type') == 'net') {
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
				else {
					log::add('enphasesecur', 'debug', 'Envoy-S-Standard-EU');
			
					$enphasesecur_info = $enphasesecur_json['production']['0']['whLifetime'];
					log::add('enphasesecur', 'debug', 'Production depuis la mise en service: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('PwattHoursLifetime', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['production']['0']['wNow'];
					log::add('enphasesecur', 'debug', 'Production instantannée: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('PwattsNow', $enphasesecur_info);	
					die();
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
				
				$prodj = ($enphasesecur['lastReportWatts']*0.25) + $eqLogic->getCmd(null, 'calWH');
			
				log::add('enphasesecur', 'debug', 'Convertisseurs ' . $enphasesecur['serialNumber'] . ' Production: ' . $prodj);
				$eqLogic->checkAndUpdateCmd('calWH', $prodj);
			}
		}
	}
	//inventaire
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
	log::add('enphasesecur', 'error', displayException($e)); //remplacez template par l'id de votre plugin
}