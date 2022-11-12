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
	
    $enphasesecur_json = json_decode($result, true);

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
					log::add('enphasesecur', 'debug', 'Consommation totale instantannée: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CwattsNow', $enphasesecur_info);	
				}
				$enphasesecur_info = $enphasesecur_json['consumption']['0']['rmsVoltage'];
				log::add('enphasesecur', 'debug', 'Tension réseau: ' . $enphasesecur_info);
				$eqLogic->checkAndUpdateCmd('tension', $enphasesecur_info);

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
				if ($eqLogic->getConfiguration('type') == 'combine' || $eqLogic->getConfiguration('type') == 'reel') {
					
					$enphasesecur_info = $enphasesecur_json['consumption']['0']['wNow'] - $enphasesecur_json['production']['1']['wNow'];
					if ($enphasesecur_info <= 0) {
						$enphasesecur_info = 0;
					}
					log::add('enphasesecur', 'debug', 'Consommation Réelle instantannée: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CReelle', $enphasesecur_info);

					$enphasesecur_info = $enphasesecur_json['consumption']['0']['whToday'] - $enphasesecur_json['production']['1']['whToday'];
					if ($enphasesecur_info <= 0) {
						$enphasesecur_info = 0;
					}
					log::add('enphasesecur', 'debug', 'Consommation Réelle Jour: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CReelleday', $enphasesecur_info);

					$enphasesecur_info = $enphasesecur_json['consumption']['0']['whLastSevenDays'] - $enphasesecur_json['production']['1']['whLastSevenDays'];
					if ($enphasesecur_info <= 0) {
						$enphasesecur_info = 0;
					}
					log::add('enphasesecur', 'debug', 'Consommation Réelle Jour: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CReelle7day', $enphasesecur_info);

					$enphasesecur_info = $enphasesecur_json['consumption']['0']['whLifetime'] - $enphasesecur_json['production']['1']['whLifetime'];
					if ($enphasesecur_info <= 0) {
						$enphasesecur_info = 0;
					}
					log::add('enphasesecur', 'debug', 'Consommation Réelle MES: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('CReellemes', $enphasesecur_info);

				}
			}
		}
		else {
			foreach (enphasesecur::byType('enphasesecur', true) as $eqLogic) {
				if ($eqLogic->getConfiguration('type') == 'net' || $eqLogic->getConfiguration('type') == 'total') {
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
				}
			}
		}
				
}
catch (Exception $e) {
	log::add('enphasesecur', 'error', displayException($e)); //remplacez template par l'id de votre plugin
}