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
		log::add('enphasesecur', 'info', 'Erreur contole token, renouvellement token');
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
		log::add('enphasesecur', 'debug', 'Réception mesures passerelle');
		if (isset($enphasesecur_json['production']['1']['whLifetime'])) {
			
			foreach (enphasesecur::byType('enphasesecur', true) as $eqLogic) {
				if ($eqLogic->getConfiguration('type') == 'combine' || $eqLogic->getConfiguration('type') == 'prod') {
					if (config::bykey('typereseau', 'enphasesecur') == 'tri'){
						//phase1
						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['whLifetime'];
						if ($enphasesecur_info != 0 && $enphasesecur_info != null) {
							log::add('enphasesecur', 'debug', 'Production 1 depuis la mise en service: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PwattHoursLifetime1', $enphasesecur_info);	
						}

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['whToday'];
						log::add('enphasesecur', 'debug', 'Production 1 totale du jour: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursToday1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['whLastSevenDays'];
						log::add('enphasesecur', 'debug', 'Production 1 totale de la semaine: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursSevenDays1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['wNow'];
						log::add('enphasesecur', 'debug', 'Production 1 instantannée: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattsNow1', $enphasesecur_info);	

						if (config::byKey('PA', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['apprntPwr'];
							log::add('enphasesecur', 'debug', 'Production Puissance Apparente 1: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PapprntPwr1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['apprntPwr'];
							log::add('enphasesecur', 'debug', 'Production Puissance Apparente 2: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PapprntPwr2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['apprntPwr'];
							log::add('enphasesecur', 'debug', 'Production Puissance Apparente 3: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PapprntPwr3', $enphasesecur_info);	
						}
						if (config::byKey('PF', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['pwrFactor'];
							log::add('enphasesecur', 'debug', 'Production Facteur de puissance 1: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PpwrFactor1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['pwrFactor'];
							log::add('enphasesecur', 'debug', 'Production Facteur de puissance 2: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PpwrFactor2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['pwrFactor'];
							log::add('enphasesecur', 'debug', 'Production Facteur de puissance 3: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PpwrFactor3', $enphasesecur_info);	
						}
						if (config::byKey('PR', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['reactPwr'];
							log::add('enphasesecur', 'debug', 'Production Puissance Réactive 1: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PreactPwr1', $enphasesecur_info);

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['reactPwr'];
							log::add('enphasesecur', 'debug', 'Production Puissance Réactive 2: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PreactPwr2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['reactPwr'];
							log::add('enphasesecur', 'debug', 'Production Puissance Réactive 3: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PreactPwr3', $enphasesecur_info);	
						}	
						if (config::byKey('PC', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['rmsCurrent'];
							log::add('enphasesecur', 'debug', 'Production Courant 1: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PrmsCurrent1', $enphasesecur_info);

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['rmsCurrent'];
							log::add('enphasesecur', 'debug', 'Production Courant 2: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PrmsCurrent2', $enphasesecur_info);
							
							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['rmsCurrent'];
							log::add('enphasesecur', 'debug', 'Production Courant 3: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PrmsCurrent3', $enphasesecur_info);	
							
						}
						if (config::byKey('PAH', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['vahLifetime'];
							log::add('enphasesecur', 'debug', 'Prod Energie apparentes MES1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvahLifetime1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['vahToday'];
							log::add('enphasesecur', 'debug', 'Prod Energie apparentes Jour1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvahToday1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['vahLifetime'];
							log::add('enphasesecur', 'debug', 'Prod Energie apparentes MES2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvahLifetime2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['vahToday'];
							log::add('enphasesecur', 'debug', 'Prod Energie apparentes Jour2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvahToday2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['vahLifetime'];
							log::add('enphasesecur', 'debug', 'Prod Energie apparentes MES3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvahLifetime3', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['vahToday'];
							log::add('enphasesecur', 'debug', 'Prod Energie apparentes Jour3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvahToday3', $enphasesecur_info);	
						}
						if (config::byKey('PRH', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['varhLagLifetime'];
							log::add('enphasesecur', 'debug', 'Prod Energie Réactive Inductive Mes1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvarhLagLifetime1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['varhLagToday'];
							log::add('enphasesecur', 'debug', 'Prod Energie Réactive Inductive Jour1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvarhLagToday1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['varhLeadLifetime'];
							log::add('enphasesecur', 'debug', 'Prod Energie Réactive Capacitive MES1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvarhLeadLifetime1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['0']['varhLeadToday'];
							log::add('enphasesecur', 'debug', 'Prod Energie Réactive Capacitive Jour1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvarhLeadToday1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['varhLagLifetime'];
							log::add('enphasesecur', 'debug', 'Prod Energie Réactive Inductive Mes2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvarhLagLifetime2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['varhLagToday'];
							log::add('enphasesecur', 'debug', 'Prod Energie Réactive Inductive Jour2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvarhLagToday2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['varhLeadLifetime'];
							log::add('enphasesecur', 'debug', 'Prod Energie Réactive Capacitive MES2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvarhLeadLifetime2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['varhLeadToday'];
							log::add('enphasesecur', 'debug', 'Prod Energie Réactive Capacitive Jour2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvarhLeadToday2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['varhLagLifetime'];
							log::add('enphasesecur', 'debug', 'Prod Energie Réactive Inductive Mes3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvarhLagLifetime3', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['varhLagToday'];
							log::add('enphasesecur', 'debug', 'Prod Energie Réactive Inductive Jour3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvarhLagToday3', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['varhLeadLifetime'];
							log::add('enphasesecur', 'debug', 'Prod Energie Réactive Capacitive MES3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvarhLeadLifetime3', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['varhLeadToday'];
							log::add('enphasesecur', 'debug', 'Prod Energie Réactive Capacitive Jour3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PvarhLeadToday3', $enphasesecur_info);	

						}

						//phase2
						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['whLifetime'];
						if ($enphasesecur_info != 0 && $enphasesecur_info != null) {
							log::add('enphasesecur', 'debug', 'Production 2 depuis la mise en service: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PwattHoursLifetime2', $enphasesecur_info);
						}	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['whToday'];
						log::add('enphasesecur', 'debug', 'Production 2 totale du jour: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursToday2', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['whLastSevenDays'];
						log::add('enphasesecur', 'debug', 'Production 2 totale de la semaine: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursSevenDays2', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['1']['wNow'];
						log::add('enphasesecur', 'debug', 'Production 2 instantannée: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattsNow2', $enphasesecur_info);							

						//phase3
						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['whLifetime'];
						if ($enphasesecur_info != 0 && $enphasesecur_info != null) {
							log::add('enphasesecur', 'debug', 'Production 3 depuis la mise en service: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('PwattHoursLifetime3', $enphasesecur_info);	
						}

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['whToday'];
						log::add('enphasesecur', 'debug', 'Production 3 totale du jour: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursToday3', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['whLastSevenDays'];
						log::add('enphasesecur', 'debug', 'Production 3 totale de la semaine: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursSevenDays3', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['lines']['2']['wNow'];
						log::add('enphasesecur', 'debug', 'Production 3 instantannée: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattsNow3', $enphasesecur_info);	

					}
					//total
					$enphasesecur_info = $enphasesecur_json['production']['1']['whLifetime'];
					if ($enphasesecur_info != 0 && $enphasesecur_info != null) {
						log::add('enphasesecur', 'debug', 'Production depuis la mise en service: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursLifetime', $enphasesecur_info);	
						
					}

					$enphasesecur_info = $enphasesecur_json['production']['1']['whToday'];
					log::add('enphasesecur', 'debug', 'Production totale du jour: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('PwattHoursToday', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['production']['1']['whLastSevenDays'];
					log::add('enphasesecur', 'debug', 'Production totale de la semaine: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('PwattHoursSevenDays', $enphasesecur_info);	

					$enphasesecur_info = $enphasesecur_json['production']['1']['wNow'];
					log::add('enphasesecur', 'debug', 'Production instantannée: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('PwattsNow', $enphasesecur_info);	

					if (config::byKey('PA', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['production']['1']['apprntPwr'];
						log::add('enphasesecur', 'debug', 'Production Energie Apparente: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PapprntPwr', $enphasesecur_info);
					}	
					if (config::byKey('PF', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['production']['1']['pwrFactor'];
						log::add('enphasesecur', 'debug', 'Production Facteur de puissance: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PpwrFactor', $enphasesecur_info);	
					}
					if (config::byKey('PR', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['production']['1']['reactPwr'];
						log::add('enphasesecur', 'debug', 'Production Puissance Réactive: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PreactPwr', $enphasesecur_info);	
					}
					if (config::byKey('PC', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['production']['1']['rmsCurrent'];
						log::add('enphasesecur', 'debug', 'Production Courant: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PrmsCurrent', $enphasesecur_info);	
					}
					if (config::byKey('PAH', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['production']['1']['vahLifetime'];
						log::add('enphasesecur', 'debug', 'Prod Energie apparentes MES ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PvahLifetime', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['vahToday'];
						log::add('enphasesecur', 'debug', 'Prod Energie apparentes Jour ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PvahToday', $enphasesecur_info);
					}	
					if (config::byKey('PRH', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['production']['1']['varhLagLifetime'];
						log::add('enphasesecur', 'debug', 'Prod Energie Réactive Inductive Mes ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PvarhLagLifetime', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['varhLagToday'];
						log::add('enphasesecur', 'debug', 'Prod Energie Réactive Inductive Jour ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PvarhLagToday', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['varhLeadLifetime'];
						log::add('enphasesecur', 'debug', 'Prod Energie Réactive Capacitive MES ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PvarhLeadLifetime', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['production']['1']['varhLeadToday'];
						log::add('enphasesecur', 'debug', 'Prod Energie Réactive Capacitive Jour ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PvarhLeadToday', $enphasesecur_info);	
					}

				}
				if ($eqLogic->getConfiguration('type') == 'combine' || $eqLogic->getConfiguration('type') == 'total') {
					if (config::bykey('typereseau', 'enphasesecur') == 'tri'){
						//phase1
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['whLifetime'];
						if ($enphasesecur_info != 0 && $enphasesecur_info != null) {
							log::add('enphasesecur', 'debug', 'Consommation totale 1 depuis la mise en service: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CwattHoursLifetime1', $enphasesecur_info);	
						}

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['whToday'];
						log::add('enphasesecur', 'debug', 'Consommation totale 1 du jour: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursToday1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['whLastSevenDays'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 1 de la semaine: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursSevenDays1', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['wNow'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 1 instantannée: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattsNow1', $enphasesecur_info);	
						
						if (config::byKey('CAT', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['apprntPwr'];
							log::add('enphasesecur', 'debug', 'Consommation totale Puissance Apparente 1: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CapprntPwr1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['apprntPwr'];
							log::add('enphasesecur', 'debug', 'Consommation totale Puissance Apparente 2: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CapprntPwr2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['apprntPwr'];
							log::add('enphasesecur', 'debug', 'Consommation totale Puissance Apparente 3: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CapprntPwr3', $enphasesecur_info);	

						
						}
						if (config::byKey('CFT', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['pwrFactor'];
							log::add('enphasesecur', 'debug', 'Consommation totale Facteur de puissance 1: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CpwrFactor1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['pwrFactor'];
							log::add('enphasesecur', 'debug', 'Consommation totale Facteur de puissance 2: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CpwrFactor2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['pwrFactor'];
							log::add('enphasesecur', 'debug', 'Consommation totale Facteur de puissance 3: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CpwrFactor3', $enphasesecur_info);	
						}
						if (config::byKey('CRT', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['reactPwr'];
							log::add('enphasesecur', 'debug', 'Consommation totale Puissance Réactive 1: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CreactPwr1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['reactPwr'];
							log::add('enphasesecur', 'debug', 'Consommation totale Puissance Réactive 2: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CreactPwr2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['reactPwr'];
							log::add('enphasesecur', 'debug', 'Consommation totale Puissance Réactive 3: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CreactPwr3', $enphasesecur_info);	
						}
						if (config::byKey('CCT', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['rmsCurrent'];
							log::add('enphasesecur', 'debug', 'Consommation totale Courant 1: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CrmsCurrent1', $enphasesecur_info);
							
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['rmsCurrent'];
							log::add('enphasesecur', 'debug', 'Consommation totale Courant 2: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CrmsCurrent2', $enphasesecur_info);

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['rmsCurrent'];
							log::add('enphasesecur', 'debug', 'Consommation totale Courant 3: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CrmsCurrent3', $enphasesecur_info);	
						}
						if (config::byKey('CATH', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['vahLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Energie Total apparentes Mes1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvahLifetime1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['vahToday'];
							log::add('enphasesecur', 'debug', 'Conso Energie Total apparentes Jour1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvahToday1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['vahLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Energie Total apparentes Mes2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvahLifetime2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['vahToday'];
							log::add('enphasesecur', 'debug', 'Conso Energie Total apparentes Jour2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvahToday2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['vahLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Energie Total apparentes Mes3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvahLifetime3', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['vahToday'];
							log::add('enphasesecur', 'debug', 'Conso Energie Total apparentes Jour3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvahToday3', $enphasesecur_info);
						}
						if (config::byKey('CRTH', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['varhLagLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Total Réactive Inductive Mes1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLagLifetime1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['varhLagToday'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Total Réactive Inductive Jour1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLagToday1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['varhLeadLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Total Réactive Capacitive MES1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLeadLifetime1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['varhLeadToday'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Total Réactive Capacitive Jour1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLeadToday1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['varhLagLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Total Réactive Inductive Mes2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLagLifetime2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['varhLagToday'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Total Réactive Inductive Jour2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLagToday2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['varhLeadLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Total Réactive Capacitive MES2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLeadLifetime2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['varhLeadToday'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Total Réactive Capacitive Jour2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLeadToday2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['varhLagLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Total Réactive Inductive Mes3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLagLifetime3', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['varhLagToday'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Total Réactive Inductive Jour3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLagToday3', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['varhLeadLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Total Réactive Capacitive MES3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLeadLifetime3', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['varhLeadToday'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Total Réactive Capacitive Jour3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLeadToday3', $enphasesecur_info);	
						}

						//phase2
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['whLifetime'];
						if ($enphasesecur_info != 0 && $enphasesecur_info != null) {
							log::add('enphasesecur', 'debug', 'Consommation Totale 2 depuis la mise en service: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CwattHoursLifetime2', $enphasesecur_info);	
						}

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['whToday'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 2 du jour: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursToday2', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['whLastSevenDays'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 2 de la semaine: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursSevenDays2', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['1']['wNow'];
						log::add('enphasesecur', 'debug', 'Consommation Totale instantannée 2: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattsNow2', $enphasesecur_info);	

						//phase3
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['whLifetime'];
						if ($enphasesecur_info != 0 && $enphasesecur_info != null) {
							log::add('enphasesecur', 'debug', 'Consommation Totale 3 depuis la mise en service: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CwattHoursLifetime3', $enphasesecur_info);	
						}
						

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['whToday'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 3 du jour: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursToday3', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['whLastSevenDays'];
						log::add('enphasesecur', 'debug', 'Consommation Totale 3 de la semaine: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursSevenDays3', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['2']['wNow'];
						log::add('enphasesecur', 'debug', 'Consommation Totale instantannée3: ' . $enphasesecur_info);
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

					if (config::byKey('CAT', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['apprntPwr'];
						log::add('enphasesecur', 'debug', 'Consommation totale Puissance Apparente : ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CapprntPwr', $enphasesecur_info);	
					}
					if (config::byKey('CFT', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['pwrFactor'];
						log::add('enphasesecur', 'debug', 'Consommation totale Facteur de puissance : ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CpwrFactor', $enphasesecur_info);
					}	
					if (config::byKey('CRT', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['reactPwr'];
						log::add('enphasesecur', 'debug', 'Consommation totale Puissance Réactive : ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CreactPwr', $enphasesecur_info);	
					}
					if (config::byKey('CCT', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['rmsCurrent'];
						log::add('enphasesecur', 'debug', 'Consommation totale Courant : ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CrmsCurrent', $enphasesecur_info);	
					}
					if (config::byKey('CATH', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['vahLifetime'];
						log::add('enphasesecur', 'debug', 'Conso Energie Total apparentes Mes ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CvahLifetime', $enphasesecur_info);	
					
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['vahToday'];
						log::add('enphasesecur', 'debug', 'Conso Energie Total apparentes Jour ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CvahToday', $enphasesecur_info);
					}	
					if (config::byKey('CRTH', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['consumption']['0']['varhLagLifetime'];
						log::add('enphasesecur', 'debug', 'Conso Energie Total Réactive Inductive Mes ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CvarhLagLifetime', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['varhLagToday'];
						log::add('enphasesecur', 'debug', 'Conso Energie Total Réactive Inductive Jour ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CvarhLagToday', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['varhLeadLifetime'];
						log::add('enphasesecur', 'debug', 'Conso Energie Net Réactive Capacitive MES ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CvarhLeadLifetime', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['0']['varhLeadToday'];
						log::add('enphasesecur', 'debug', 'Conso Energie Total Réactive Capacitive Jour ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CvarhLeadToday', $enphasesecur_info);	
					}
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
						if (config::byKey('CAN', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['apprntPwr'];
							log::add('enphasesecur', 'debug', 'Consommation Net Puissance Apparente 1: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CapprntPwrNet1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['apprntPwr'];
							log::add('enphasesecur', 'debug', 'Consommation Net Puissance Apparente 2: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CapprntPwrNet2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['apprntPwr'];
							log::add('enphasesecur', 'debug', 'Consommation Net Puissance Apparente 3: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CapprntPwrNet3', $enphasesecur_info);	
						}
						if (config::byKey('CFN', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['pwrFactor'];
							log::add('enphasesecur', 'debug', 'Consommation Net Facteur de puissance 1: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CpwrFactorNet1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['pwrFactor'];
							log::add('enphasesecur', 'debug', 'Consommation Net Facteur de puissance 2: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CpwrFactorNet2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['pwrFactor'];
							log::add('enphasesecur', 'debug', 'Consommation Net Facteur de puissance 3: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CpwrFactorNet3', $enphasesecur_info);	

						}
						if (config::byKey('CRN', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['reactPwr'];
							log::add('enphasesecur', 'debug', 'Consommation Net Puissance Réactive 1: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CreactPwrNet1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['reactPwr'];
							log::add('enphasesecur', 'debug', 'Consommation Net Puissance Réactive 2: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CreactPwrNet2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['reactPwr'];
							log::add('enphasesecur', 'debug', 'Consommation Net Puissance Réactive 3: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CreactPwrNet3', $enphasesecur_info);	
						}
						if (config::byKey('CCN', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['rmsCurrent'];
							log::add('enphasesecur', 'debug', 'Consommation Net Courant 1: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('rmsCurrentNet1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['rmsCurrent'];
							log::add('enphasesecur', 'debug', 'Consommation Net Courant 2: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('rmsCurrentNet2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['rmsCurrent'];
							log::add('enphasesecur', 'debug', 'Consommation Net Courant 3: ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('rmsCurrentNet3', $enphasesecur_info);
						}
						if (config::byKey('CANH', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['vahLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Energie Net apparentes Mes1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvahLifetimeNet1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['vahToday'];
							log::add('enphasesecur', 'debug', 'Conso Energie Net apparentes Jour1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvahTodayNet1', $enphasesecur_info);
							
							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['vahLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Energie Net apparentes Mes2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvahLifetimeNet2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['vahToday'];
							log::add('enphasesecur', 'debug', 'Conso Energie Net apparentes Jour2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvahTodayNet2', $enphasesecur_info);

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['vahLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Energie Net apparentes Mes3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvahLifetimeNet3', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['vahToday'];
							log::add('enphasesecur', 'debug', 'Conso Energie Net apparentes Jour3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvahTodayNet3', $enphasesecur_info);	
						}
						if (config::byKey('CRNH', enphasesecur) == true)
						{
							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['varhLagLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Net Réactive Inductive MES1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLagLifetimeNet1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['varhLagToday'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Net Réactive Inductive Jour1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLagTodayNet1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['varhLeadLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Net Réactive Capacitive MES1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLeadLifetimeNet1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['varhLeadToday'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Net Réactive Capacitive Jour1 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLeadTodayNet1', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['varhLagLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Net Réactive Inductive MES2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLagLifetimeNet2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['varhLagToday'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Net Réactive Inductive Jour2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLagTodayNet2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['varhLeadLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Net Réactive Capacitive MES2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLeadLifetimeNet2', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['1']['varhLeadToday'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Net Réactive Capacitive Jour2 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLeadTodayNet2', $enphasesecur_info);

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['varhLagLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Net Réactive Inductive MES3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLagLifetimeNet3', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['varhLagToday'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Net Réactive Inductive Jour3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLagTodayNet3', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['varhLeadLifetime'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Net Réactive Capacitive MES3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLeadLifetimeNet3', $enphasesecur_info);	

							$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['2']['varhLeadToday'];
							log::add('enphasesecur', 'debug', 'Conso Puissance Net Réactive Capacitive Jour3 ' . $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('CvarhLeadTodayNet3', $enphasesecur_info);
						}
						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['whLifetime'];
						log::add('enphasesecur', 'debug', 'Consommation Net depuis la mise en service 1: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursLifetimeNet1', $enphasesecur_info);	
						
						$enphatemp = $enphasesecur_json['production']['1']['lines']['0']['whLifetime'] + $enphasesecur_info - $enphasesecur_json['consumption']['0']['lines']['0']['whLifetime'];						
						$eqLogic->checkAndUpdateCmd('calculjour1', $enphatemp);
						$enphaexp = max($eqLogic->getCmd(null, 'calculjour1')->execCmd()-scenarioExpression::min($eqLogic->getCmd(null, 'calculjour1')->getId(),today),0);

						$eqLogic->checkAndUpdateCmd('cumulexport1', $enphaexp);
						log::add('enphasesecur', 'debug', 'Cumul Export1: ' . $enphaexp);

						$enphaimp = -($enphasesecur_json['production']['1']['lines']['0']['whToday'] - $enphasesecur_json['consumption']['0']['lines']['0']['whToday'] - $enphaexp);
						$eqLogic->checkAndUpdateCmd('cumulimport1', $enphaimp);
						log::add('enphasesecur', 'debug', 'Cumul Import1: ' . $enphaimp);

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['lines']['0']['whToday'];
						if ($enphasesecur_info == 0){
							//merci Bison
							$enphasesecur_info = $enphasesecur_json['consumption']['0']['lines']['0']['whToday']-$enphasesecur_json['production']['1']['lines']['1']['whToday'];
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
						
						$enphatemp = $enphasesecur_json['production']['1']['lines']['1']['whLifetime'] + $enphasesecur_info - $enphasesecur_json['consumption']['0']['lines']['1']['whLifetime'];						
						$eqLogic->checkAndUpdateCmd('calculjour2', $enphatemp);
						$enphaexp = max($eqLogic->getCmd(null, 'calculjour2')->execCmd()-scenarioExpression::min($eqLogic->getCmd(null, 'calculjour2')->getId(),today),0);

						$eqLogic->checkAndUpdateCmd('cumulexport2', $enphaexp);
						log::add('enphasesecur', 'debug', 'Cumul Export2: ' . $enphaexp);

						$enphaimp = -($enphasesecur_json['production']['1']['lines']['1']['whToday'] - $enphasesecur_json['consumption']['0']['lines']['1']['whToday'] - $enphaexp);
						$eqLogic->checkAndUpdateCmd('cumulimport2', $enphaimp);
						log::add('enphasesecur', 'debug', 'Cumul Import2: ' . $enphaimp);

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
						$eqLogic->checkAndUpdateCmd('CwattHoursLifetimeNet3', $enphasesecur_info);	

						$enphatemp = $enphasesecur_json['production']['1']['lines']['2']['whLifetime'] + $enphasesecur_info - $enphasesecur_json['consumption']['0']['lines']['2']['whLifetime'];						
						$eqLogic->checkAndUpdateCmd('calculjour3', $enphatemp);
						$enphaexp = max($eqLogic->getCmd(null, 'calculjour3')->execCmd()-scenarioExpression::min($eqLogic->getCmd(null, 'calculjour3')->getId(),today),0);

						$eqLogic->checkAndUpdateCmd('cumulexport3', $enphaexp);
						log::add('enphasesecur', 'debug', 'Cumul Export3: ' . $enphaexp);

						$enphaimp = -($enphasesecur_json['production']['1']['lines']['2']['whToday'] - $enphasesecur_json['consumption']['0']['lines']['2']['whToday'] - $enphaexp);
						$eqLogic->checkAndUpdateCmd('cumulimport3', $enphaimp);
						log::add('enphasesecur', 'debug', 'Cumul Import3: ' . $enphaimp);

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
					if (config::byKey('CAN', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['consumption']['1']['apprntPwr'];
						log::add('enphasesecur', 'debug', 'Consommation Net Puissance Apparente : ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CapprntPwrNet', $enphasesecur_info);	
					}
					if (config::byKey('CFN', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['consumption']['1']['pwrFactor'];
						log::add('enphasesecur', 'debug', 'Consommation Net Facteur de puissance : ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CpwrFactorNet', $enphasesecur_info);	
					}
					if (config::byKey('CRN', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['consumption']['1']['reactPwr'];
						log::add('enphasesecur', 'debug', 'Consommation Net Puissance Réactive : ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CreactPwrNet', $enphasesecur_info);	
					}
					if (config::byKey('CCN', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['consumption']['1']['rmsCurrent'];
						log::add('enphasesecur', 'debug', 'Consommation Net Courant : ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CrmsCurrentNet', $enphasesecur_info);	
					}
					if (config::byKey('CANH', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['consumption']['1']['vahLifetime'];
						log::add('enphasesecur', 'debug', 'Conso Energie Net apparentes Mes ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CvahLifetimeNet', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['vahToday'];
						log::add('enphasesecur', 'debug', 'Conso Energie Net apparentes Jour ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CvahTodayNet', $enphasesecur_info);	
					}
					if (config::byKey('CRNH', enphasesecur) == true)
					{
						$enphasesecur_info = $enphasesecur_json['consumption']['1']['varhLagLifetime'];
						log::add('enphasesecur', 'debug', 'Conso Energie Net Réactive Inductive MES ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CvarhLagLifetimeNet', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['varhLagToday'];
						log::add('enphasesecur', 'debug', 'Conso Energie Net Réactive Inductive Jour ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CvarhLagTodayNet', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['varhLeadLifetime'];
						log::add('enphasesecur', 'debug', 'Conso Energie Net Réactive Inductive MES  ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CvarhLeadLifetimeNet', $enphasesecur_info);	

						$enphasesecur_info = $enphasesecur_json['consumption']['1']['varhLeadToday'];
						log::add('enphasesecur', 'debug', 'Conso Energie Net Réactive Capacitive Jour ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CvarhLeadTodayNet', $enphasesecur_info);	
					}
				
					if (config::byKey('importexport', enphasesecur) == "Jour") 
					{
						$enphatemp = $eqLogic->getCmd(null, 'CwattHoursLifetimeNet')->execCmd();
						$enphasesecur_info = $enphasesecur_json['consumption']['1']['whLifetime'];
						log::add('enphasesecur', 'debug', 'Consommation Net depuis la mise en service: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursLifetimeNet', $enphasesecur_info);

						if (($enphatemp-$enphasesecur_info) > 0) 
						{
							$enphaexp = $eqLogic->getCmd(null, 'cumulexport')->execCmd();
							$enphaexp = abs($enphaexp + $enphatemp - $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('cumulexport', $enphaexp);
							log::add('enphasesecur', 'debug', 'Cumul Export: ' . $enphaexp);
						}
						else {
							$enphaimp = $eqLogic->getCmd(null, 'cumulimport')->execCmd();
							$enphaimp = abs($enphaimp + $enphatemp - $enphasesecur_info);
							$eqLogic->checkAndUpdateCmd('cumulimport', $enphaimp);
							log::add('enphasesecur', 'debug', 'Cumul Import: ' . $enphaimp);
						}	
					}
					else {
						$enphasesecur_info = $enphasesecur_json['consumption']['1']['whLifetime'];
						log::add('enphasesecur', 'debug', 'Consommation Net depuis la mise en service: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('CwattHoursLifetimeNet', $enphasesecur_info);

						$enphatemp = $enphasesecur_json['production']['1']['whLifetime'] + $enphasesecur_info - $enphasesecur_json['consumption']['0']['whLifetime'];
						$eqLogic->checkAndUpdateCmd('calculjour', $enphatemp);
						$enphaexp = max($eqLogic->getCmd(null, 'calculjour')->execCmd()-scenarioExpression::min($eqLogic->getCmd(null, 'calculjour')->getId(),today),0);

						$eqLogic->checkAndUpdateCmd('cumulexport', $enphaexp);
						log::add('enphasesecur', 'debug', 'Cumul Export: ' . $enphaexp);

						$enphaimp = -($enphasesecur_json['production']['1']['whToday'] - $enphasesecur_json['consumption']['0']['whToday'] - $enphaexp);
						$eqLogic->checkAndUpdateCmd('cumulimport', $enphaimp);
						log::add('enphasesecur', 'debug', 'Cumul Import: ' . $enphaimp);
					}

					$enphasesecur_info = $enphasesecur_json['consumption']['1']['whToday'];
					if ($enphasesecur_info == 0){
						//merci Bison
						$enphasesecur_info = $enphasesecur_json['consumption'][0]['whToday']-$enphasesecur_json['production']['1']['whToday'];
					}
					log::add('enphasesecur', 'debug', 'Consommation Net du jour: ' . $enphasesecur_info);
					$oldCwattHoursTodayNet = $eqLogic->getCmd(null, 'CwattHoursTodayNet')->execCmd();
					
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
					
					if (config::bykey('autoconso', 'enphasesecur') == 'oui')
					{
						if (config::bykey('typereseau', 'enphasesecur') == 'mono'){
							$etatauto = $eqLogic->getCmd(null, 'autoconso1')->execCmd();
							$export = $eqLogic->getCmd(null, 'Export')->execCmd();
							if ($etatauto == true)
							{
								if ($export < config::bykey('wattsautoconso1off', 'enphasesecur')) 
								{
									log::add('enphasesecur', 'debug', 'Arret autoconso seuil 1');
									$eqLogic->checkAndUpdateCmd('autoconso1', 0);
									$cmdOff1 = cmd::byId(str_replace('#','',config::byKey('cmdautoconso1off', 'enphasesecur', '')));
									if (is_object($cmdOff1)) {
										$cmdOff1->execute();
									}
								}
							}
							else {
								if ($export > config::bykey('wattsautoconso1on', 'enphasesecur')) 
								{
									$eqLogic->checkAndUpdateCmd('autoconso1', 1);
									log::add('enphasesecur', 'debug', 'Demarrage autoconso seuil 1');
									$cmdOn1 = cmd::byId(str_replace('#','',config::byKey('cmdautoconso1on', 'enphasesecur', '')));
									if (is_object($cmdOn1)) {
										$cmdOn1->execute();
									}
								}
							}
						}
						else {
							//phase1
							$etatauto = $eqLogic->getCmd(null, 'autoconso11')->execCmd();
							$export = $eqLogic->getCmd(null, 'Export')->execCmd();
							if ($etatauto == true)
							{
								if ($export < config::bykey('wattsautoconso1off1', 'enphasesecur')) 
								{
									log::add('enphasesecur', 'debug', 'Arret autoconso seuil 1 phase 1');
									$eqLogic->checkAndUpdateCmd('autoconso11', 0);
									$cmdOff1 = cmd::byId(str_replace('#','',config::byKey('cmdautoconso1off1', 'enphasesecur', '')));
									if (is_object($cmdOff1)) {
										$cmdOff1->execute();
									}
								}
							}
							else {
								if ($export > config::bykey('wattsautoconso1on1', 'enphasesecur')) 
								{
									$eqLogic->checkAndUpdateCmd('autoconso11', 1);
									log::add('enphasesecur', 'debug', 'Demarrage autoconso seuil 1 phase 1');
									$cmdOn1 = cmd::byId(str_replace('#','',config::byKey('cmdautoconso1on1', 'enphasesecur', '')));
									if (is_object($cmdOn1)) {
										$cmdOn1->execute();
									}
								}
							}
							//phase2
							$etatauto = $eqLogic->getCmd(null, 'autoconso12')->execCmd();
							$export = $eqLogic->getCmd(null, 'Export')->execCmd();
							if ($etatauto == true)
							{
								if ($export < config::bykey('wattsautoconso1off2', 'enphasesecur')) 
								{
									log::add('enphasesecur', 'debug', 'Arret autoconso seuil 1 phase 2');
									$eqLogic->checkAndUpdateCmd('autoconso12', 0);
									$cmdOff1 = cmd::byId(str_replace('#','',config::byKey('cmdautoconso1off2', 'enphasesecur', '')));
									if (is_object($cmdOff1)) {
										$cmdOff1->execute();
									}
								}
							}
							else {
								if ($export > config::bykey('wattsautoconso1on2', 'enphasesecur')) 
								{
									$eqLogic->checkAndUpdateCmd('autoconso12', 1);
									log::add('enphasesecur', 'debug', 'Demarrage autoconso seuil 1 phase 2');
									$cmdOn1 = cmd::byId(str_replace('#','',config::byKey('cmdautoconso1on1', 'enphasesecur', '')));
									if (is_object($cmdOn1)) {
										$cmdOn1->execute();
									}
								}
							}
							//phase3
							$etatauto = $eqLogic->getCmd(null, 'autoconso13')->execCmd();
							$export = $eqLogic->getCmd(null, 'Export')->execCmd();
							if ($etatauto == true)
							{
								if ($export < config::bykey('wattsautoconso1off3', 'enphasesecur')) 
								{
									log::add('enphasesecur', 'debug', 'Arret autoconso seuil 1 phase 3');
									$eqLogic->checkAndUpdateCmd('autoconso13', 0);
									$cmdOff1 = cmd::byId(str_replace('#','',config::byKey('cmdautoconso1off3', 'enphasesecur', '')));
									if (is_object($cmdOff1)) {
										$cmdOff1->execute();
									}
								}
							}
							else {
								if ($export > config::bykey('wattsautoconso1on3', 'enphasesecur')) 
								{
									$eqLogic->checkAndUpdateCmd('autoconso13', 1);
									log::add('enphasesecur', 'debug', 'Demarrage autoconso seuil 1 phase 3');
									$cmdOn1 = cmd::byId(str_replace('#','',config::byKey('cmdautoconso1on3', 'enphasesecur', '')));
									if (is_object($cmdOn1)) {
										$cmdOn1->execute();
									}
								}
							}			
						}
					}
					else {
						if (config::bykey('typereseau', 'enphasesecur') == 'mono'){
							$eqLogic->checkAndUpdateCmd('autoconso1', 0);
						}
						else {
							$eqLogic->checkAndUpdateCmd('autoconso13', 0);
							$eqLogic->checkAndUpdateCmd('autoconso12', 0);
							$eqLogic->checkAndUpdateCmd('autoconso11', 0);
						}
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
					if ($enphasesecur_info != '0' && $enphasesecur_info != null) {
						log::add('enphasesecur', 'debug', 'Production depuis la mise en service: ' . $enphasesecur_info);
						$eqLogic->checkAndUpdateCmd('PwattHoursLifetime', $enphasesecur_info);
					}	

					$enphasesecur_info = $enphasesecur_json['production']['0']['wNow'];
					log::add('enphasesecur', 'debug', 'Production instantannée: ' . $enphasesecur_info);
					$eqLogic->checkAndUpdateCmd('PwattsNow', $enphasesecur_info);				

				}
			}
		}
	}
	//prod convertisseurs
	elseif (isset($enphasesecur_json['0']['serialNumber']) && config::bykey('onduleur', 'enphasesecur') != 'non') {
		log::add('enphasesecur', 'debug', 'Réception mesures des convertisseurs');
		$enphasesecur_temp_max1 = 0;
		$enphasesecur_temp_max2 = 0;
		$enphasesecur_temp_max3 = 0;
		$enphasesecur_temp_max4 = 0;
		$enphasesecur_temp_1 = 0;
		$enphasesecur_temp_2 = 0;
		$enphasesecur_temp_3 = 0;
		$enphasesecur_temp_4 = 0;

		foreach ($enphasesecur_json as $enphasesecur) {
			$eqLogic = eqLogic::byLogicalId($enphasesecur['serialNumber'], 'enphasesecur');
			if (is_object($eqLogic)) {
				log::add('enphasesecur', 'debug', 'Convertisseurs ' . $enphasesecur['serialNumber'] . ' Puissance: ' . $enphasesecur['lastReportWatts']);
				$eqLogic->checkAndUpdateCmd('Watt', $enphasesecur['lastReportWatts']);
				if ($eqLogic->getConfiguration('groupement') == '1') {$enphasesecur_temp_1 += $enphasesecur['lastReportWatts'];}
				else if ($eqLogic->getConfiguration('groupement') == '2') {$enphasesecur_temp_2 += $enphasesecur['lastReportWatts'];}
				else if ($eqLogic->getConfiguration('groupement') == '3') {$enphasesecur_temp_3 += $enphasesecur['lastReportWatts'];}
				else if ($eqLogic->getConfiguration('groupement') == '4') {$enphasesecur_temp_4 += $enphasesecur['lastReportWatts'];}
				log::add('enphasesecur', 'debug', 'Convertisseurs ' . $enphasesecur['serialNumber'] . ' Puissance max: ' . $enphasesecur['maxReportWatts']);
				$eqLogic->checkAndUpdateCmd('maxWatt', $enphasesecur['maxReportWatts']);
				if ($eqLogic->getConfiguration('groupement') == '1') {$enphasesecur_temp_max1 += $enphasesecur['maxReportWatts'];}
				elseif ($eqLogic->getConfiguration('groupement') == '2') {$enphasesecur_temp_max2 += $enphasesecur['maxReportWatts'];}
				elseif ($eqLogic->getConfiguration('groupement') == '3') {$enphasesecur_temp_max3 += $enphasesecur['maxReportWatts'];}
				elseif ($eqLogic->getConfiguration('groupement') == '4') {$enphasesecur_temp_max4 += $enphasesecur['maxReportWatts'];}
			}
		}
		foreach (enphasesecur::byType('enphasesecur', true) as $eqLogic) {
			if ($eqLogic->getConfiguration('type') == 'groupe') {
			
			  if ($eqLogic->getLogicalId() == 'enphasesecur_G1') {
				  log::add('enphasesecur', 'debug', 'Groupe 1, Puissance max: ' . $enphasesecur_temp_max1);
				  $eqLogic->checkAndUpdateCmd('maxWatt', $enphasesecur_temp_max1);
				  log::add('enphasesecur', 'debug', 'Groupe 1, Puissance: ' . $enphasesecur_temp_1);
				  $eqLogic->checkAndUpdateCmd('Watt', $enphasesecur_temp_1);
			  }
			  elseif ($eqLogic->getLogicalId() == 'enphasesecur_G2') {
				  log::add('enphasesecur', 'debug', 'Groupe 2, Puissance max: ' . $enphasesecur_temp_max2);
				  $eqLogic->checkAndUpdateCmd('maxWatt', $enphasesecur_temp_max2);
				  log::add('enphasesecur', 'debug', 'Groupe 2, Puissance: ' . $enphasesecur_temp_2);
				  $eqLogic->checkAndUpdateCmd('Watt', $enphasesecur_temp_2);
			  }
			  elseif ($eqLogic->getLogicalId() == 'enphasesecur_G3') {
				  log::add('enphasesecur', 'debug', 'Groupe 3, Puissance max: ' . $enphasesecur_temp_max3);
				  $eqLogic->checkAndUpdateCmd('maxWatt', $enphasesecur_temp_max3);
				  log::add('enphasesecur', 'debug', 'Groupe 3, Puissance: ' . $enphasesecur_temp_3);
				  $eqLogic->checkAndUpdateCmd('Watt', $enphasesecur_temp_3);
			  }
			  elseif ($eqLogic->getLogicalId() == 'enphasesecur_G4') {
				  log::add('enphasesecur', 'debug', 'Groupe 4, Puissance max: ' . $enphasesecur_temp_max4);
				  $eqLogic->checkAndUpdateCmd('maxWatt', $enphasesecur_temp_max4);
				  log::add('enphasesecur', 'debug', 'Groupe 4, Puissance: ' . $enphasesecur_temp_4);
				  $eqLogic->checkAndUpdateCmd('Watt', $enphasesecur_temp_4);
			  }
			}
		  }
	  }
	//inventaire création des équipements
	elseif (isset($enphasesecur_json[0]['devices']) && config::bykey('onduleur', 'enphasesecur') != 'non') {
		log::add('enphasesecur', 'debug', 'Réception inventaire');
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