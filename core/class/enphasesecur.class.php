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

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class enphasesecur extends eqLogic 
{
	/*     * *************************Attributs****************************** */

	public function decrypt() {
		$this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
		$this->setConfiguration('serie', utils::decrypt($this->getConfiguration('serie')));
		$this->setConfiguration('token', utils::decrypt($this->getConfiguration('token')));
	}

	public function encrypt() {
		$this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
		$this->setConfiguration('serie', utils::encrypt($this->getConfiguration('serie')));
		$this->setConfiguration('token', utils::encrypt($this->getConfiguration('token')));
	}

	public static function dependancy_info() {
		$return = array();
        $return['log'] = log::getPathToLog(__CLASS__ . '_update');
        $return['progress_file'] = jeedom::getTmpFolder(__CLASS__) . '/dependency';
        if (file_exists(jeedom::getTmpFolder(__CLASS__) . '/dependency')) {
			$return['state'] = 'in_progress';
        }
		else 
		{
			$deps = array('PyJWT', 'asyncio', 'httpx', 'lxml', 'html5lib', 'html.parser', 'six');
        	$return['state'] = 'ok';
        	$output = array();
			foreach($deps as $list) {
				$cmd = "sudo pip3 list | grep ";
				$cmd .= $list;
				unset($output);
				exec($cmd, $output, $return_var);
				if ($return_var || $output[0] == "") {
					$return['state'] = 'nok';
					break;
				}
			}
		}
		return $return;
    }

	// isntallation des dépendances
	public static function dependancy_install() 
	{
		log::remove(__CLASS__ . '_update');
		passthru('/bin/bash ' . dirname(__FILE__) . '/../../resources/install_apt.sh ' . jeedom::getTmpFolder(__CLASS__) . '/dependency > ' . log::getPathToLog(__CLASS__ . '_update') . ' 2>&1 &');
	}

	public static function dependancy_install_update() 
	{
		log::remove(__CLASS__ . '_update');
	}

	public function CreaCmd($enphaselogic, $enphasename, $enphasedash, $enphasehisto, $enphasehistor, $enphasegtype, $enphasetype, $enphasesubtype, $enphaseunite, $enphasevisible) 
	{
		$enphasesecurCmd = $this->getCmd(null, $enphaselogic);
	  	if (!is_object($enphasesecurCmd)) 
		{
			$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__($enphasename, __FILE__));
			if (!empty($enphasedash)) {$enphasesecurCmd->setTemplate('dashboard', $enphasedash);}
			if (!empty($enphasehisto)) {$enphasesecurCmd->setIsHistorized($enphasehisto);}
			if (!empty($enphasehistor)) {$enphasesecurCmd->setConfiguration('historizeRound', $enphasehistor);}
			if (!empty($enphasegtype)) {$enphasesecurCmd->setGeneric_type($enphasegtype); }
			$enphasesecurCmd->setEqLogic_id($this->getId());
			$enphasesecurCmd->setLogicalId($enphaselogic);
			if (!empty($enphasetype)) {$enphasesecurCmd->setType($enphasetype);}
			if (!empty($enphasesubtype)) {$enphasesecurCmd->setSubType($enphasesubtype);}
			if (!empty($enphaseunite)) {$enphasesecurCmd->setUnite($enphaseunite);}
			$enphasesecurCmd->setIsVisible($enphasevisible);
			$enphasesecurCmd->save();
	  	}
	}

	public function CreaEquip($enphaselogic, $enphasename, $enphaseconf, $enphaseconfbis, $enphasevisible, $enphaseenable) 
	{
		if (!is_object(eqLogic::byLogicalId($enphaselogic, 'enphasesecur'))) 
		{
			log::add('enphasesecur', 'debug', 'Création équipement ' . $enphasename);
			$eqLogic = new self();
			$eqLogic->setLogicalId($enphaselogic);
			$eqLogic->setName($enphasename);
			$eqLogic->setCategory('energy', 1);
			$eqLogic->setEqType_name('enphasesecur');
			$eqLogic->setConfiguration($enphaseconf, $enphaseconfbis);
			$eqLogic->setIsVisible($enphasevisible);
			$eqLogic->setIsEnable($enphaseenable);
			$eqLogic->save();
		}
	}

	//fonction pour la création des équipement suivant la configuration choisie
	public static function creationmaj() {
		foreach (self::byType('enphasesecur', true) as $eqLogic) 
		{
			if (config::bykey('widget', __CLASS__) == 1)
			{
				if ($eqLogic->getConfiguration('type') == 'net' || $eqLogic->getConfiguration('type') == 'total' || $eqLogic->getConfiguration('type') == 'bat' || $eqLogic->getConfiguration('type') == 'prod') 
				{
					log::add('enphasesecur', 'info', 'Suppression équipement suite à changement de mode vers combiné');
					$eqLogic->remove();
				}
			}
			else 
			{
				if ($eqLogic->getConfiguration('type') == 'combine') 
				{
					log::add('enphasesecur', 'info', 'Suppression équipement suite à changement de mode vers divisé');
					$eqLogic->remove();
				}
			}
		}

		if (config::byKey('G1', __CLASS__) == true) { self::CreaEquip('enphasesecur_G1', 'Groupe 1', 'type', 'groupe', '1', 1);}
		if (config::byKey('G2', __CLASS__) == true) { self::CreaEquip('enphasesecur_G2', 'Groupe 2', 'type', 'groupe', '1', 1);}
		if (config::byKey('G3', __CLASS__) == true) { self::CreaEquip('enphasesecur_G3', 'Groupe 3', 'type', 'groupe', '1', 1);}
		if (config::byKey('G4', __CLASS__) == true) { self::CreaEquip('enphasesecur_G4', 'Groupe 4', 'type', 'groupe', '1', 1);}

		if (config::bykey('widget', __CLASS__) == 1) { $this->CreaEquip('enphasesecur_combine', 'Passerelle Enphase', 'type', 'combine', '1', 1);}
		else 
		{ 
			self::CreaEquip('enphasesecur_prod', 'Enphase Production', 'type', 'prod', '1', 1);

			self::CreaEquip('enphasesecur_conso_net', 'Enphase Consommation Net', 'type', 'net', '1', 1);

			self::CreaEquip('enphasesecur_conso_total', 'Enphase Consommation Total', 'type', 'total', '1', 1);

			self::CreaEquip('enphasesecur_bat', 'Enphase Stockage', 'type', 'bat', '1', 1);
		}
	}
	// Fonction exécutée automatiquement avant la création de l'équipement
	public function preInsert() {}

	// Fonction exécutée automatiquement après la création de l'équipement
	public function postInsert() {}

	// Fonction exécutée automatiquement avant la mise à jour de l'équipement
  	
	public function preUpdate() {}
  	
	// Fonction exécutée automatiquement après la mise à jour de l'équipement
  	public function postUpdate() {}

	public function enphasesecurCron15(){
		$prodgroupe1 = 0;
		$prodgroupe2 = 0;
		$prodgroupe3 = 0;
		$prodgroupe4 = 0;
		foreach (eqLogic::byType('enphasesecur', true) as $eqLogic) {
			//ajout valeur au wh onduleur
			if ($eqLogic->getConfiguration('type') == 'conv') {
				$ancienprod = $eqLogic->getCmd(null, 'calWH')->execCmd();
				$puissance = $eqLogic->getCmd(null, 'Watt')->execCmd();
				if ($puissance!=0) {
					$prod = $ancienprod + ($puissance*0.25);
					$eqLogic->checkAndUpdateCmd('calWH', $prod);
					if ($eqLogic->getConfiguration('groupement') == '1') {$prodgroupe1 += $prod;}
					elseif ($eqLogic->getConfiguration('groupement') == '2') {$prodgroupe2 += $prod;}
					elseif ($eqLogic->getConfiguration('groupement') == '3') {$prodgroupe3 += $prod;}
					elseif ($eqLogic->getConfiguration('groupement') == '4') {$prodgroupe4 += $prod;}
				}
			}
		}
		foreach (eqLogic::byType('enphasesecur', true) as $eqLogic) {
			if ($eqLogic->getConfiguration('type') == 'groupe') {
				if ($eqLogic->getLogicalId() == 'enphasesecur_G1') { 
					$eqLogic->checkAndUpdateCmd('calWH', $prodgroupe1);
				}
				elseif ($eqLogic->getLogicalId() == 'enphasesecur_G2') { 
					$eqLogic->checkAndUpdateCmd('calWH', $prodgroupe2);
				}
				elseif ($eqLogic->getLogicalId() == 'enphasesecur_G3') { 
					$eqLogic->checkAndUpdateCmd('calWH', $prodgroupe3);
				}
				elseif ($eqLogic->getLogicalId() == 'enphasesecur_G4') { 
					$eqLogic->checkAndUpdateCmd('calWH', $prodgroupe4);
				}
			}
		}
	}

	public function enphasesecurCron1d(){
		foreach (eqLogic::byType('enphasesecur', true) as $eqLogic) {
			//init wh onduleur
			if ($eqLogic->getConfiguration('type') == 'conv') {
				$eqLogic->checkAndUpdateCmd('calWH', 0);
			}
			//init cumul import et export à minuit
			if ($eqLogic->getConfiguration('type') == 'combine' || $eqLogic->getConfiguration('type') == 'net') {
				$eqLogic->checkAndUpdateCmd('cumulimport', 0);
				$eqLogic->checkAndUpdateCmd('cumulexport', 0);
				$oldCwattHoursLifetimeNet = $eqLogic->getCmd(null, 'CwattHoursLifetimeNet')->execCmd();
				$eqLogic->setConfiguration('oldCwattHoursLifetimeNet', $oldCwattHoursLifetimeNet);
				$eqLogic->save();
				$eqLogic->checkAndUpdateCmd('cumulimport1', 0);
				$eqLogic->checkAndUpdateCmd('cumulexport1', 0);
				$eqLogic->checkAndUpdateCmd('cumulimport2', 0);
				$eqLogic->checkAndUpdateCmd('cumulexport2', 0);
				$eqLogic->checkAndUpdateCmd('cumulimport3', 0);
				$eqLogic->checkAndUpdateCmd('cumulexport3', 0);
			}
		}
	}

	public function enphasesecurCron1drapport(){
		
		$cumul1 =0;
		$cumul1b =0;
		$cumul2 =0;
		$cumul2b =0;
		$cumul3 =0;
		$cumul3b =0;
		$cumul4 =0;
		$cumul4b =0;
		$rapport = "Problème de production sur le/les panneaux: ";

		foreach (eqLogic::byType('enphasesecur', true) as $eqLogic) {
			if ($eqLogic->getConfiguration('type') == 'conv') {
				switch ($eqLogic->getConfiguration('groupement')) {
					case '1':
						$cumul1 = $cumul1 + $eqLogic->getCmd(null, 'calWH')->execCmd();
						$cumulb1 = $cumulb1 + 1;
						break;
						
					case '2':
						$cumul2 = $cumul2 + $eqLogic->getCmd(null, 'calWH')->execCmd();
						$cumul2b = $cumul3b +1;
						break;
						
					case '3':
						$cumul3 = $cumul3 + $eqLogic->getCmd(null, 'calWH')->execCmd();
						$cumul3b = $cumul3b + 1;
						break;
						
					case '4':
						$cumul4 = $cumul4 + $eqLogic->getCmd(null, 'calWH')->execCmd();
						$cumul4b = $cumul4b +1;
						break;
					default:
				
						break;
				}
			}
		}
		
		$cumul1 = $cumul1/$cumulb1;
		$cumul1 = $cumul1-$cumul1*0.10;
		$cumul2 = $cumul2/$cumulb2;
		$cumul2 = $cumul2-$cumul2*0.10;
		$cumul3 = $cumul3/$cumulb3;
		$cumul3 = $cumul3-$cumul3*0.10;
		$cumul4 = $cumul4/$cumulb4;
		$cumul4 = $cumul4-$cumul4*0.10;
		$g1 = true;
		$g2 = true;
		$g3 = true;
		$g4 = true;

		foreach (eqLogic::byType('enphasesecur', true) as $eqLogic) {
			if ($eqLogic->getConfiguration('type') == 'conv') {
				switch ($eqLogic->getConfiguration('groupement')) {
					case '1':
						if($eqLogic->getCmd(null, 'calWH')->execCmd()<$cumul1) {
							$rapport = $rapport . ' ' . $eqLogic->getName();
							$g1 = false;
						}
						break;
						
					case '2':
						if($eqLogic->getCmd(null, 'calWH')->execCmd()<$cumul2) {
							$rapport = $rapport . ' ' . $eqLogic->getName();
							$g2 = false;
						}
						break;
						
					case '3':
						if($eqLogic->getCmd(null, 'calWH')->execCmd()<$cumul3) {
							$rapport = $rapport . ' ' . $eqLogic->getName();
							$g3 = false;
						}
						break;
						
					case '4':
						if($eqLogic->getCmd(null, 'calWH')->execCmd()<$cumul4) {
							$rapport = $rapport . ' ' . $eqLogic->getName();
							$g4 = false;
						}
						break;
					default:
						
						break;
				}
			}
		}
		if ($rapport == "Problème de production sur le/les panneaux: ") {
			$rapport = "Pas d'anomalie de production détectée, seuil ligne 1: ". $cumul1 . ", seuil ligne 2: ". $cumul2 . ", seuil ligne 3: ". $cumul3 . ", seuil ligne 4: ". $cumul4;
			log::add('enphasesecur', 'info', $rapport);
		}
		else {
			$rapport = $rapport . ". seuil ligne 1: ". $cumul1 . ", seuil ligne 2: ". $cumul2 . ", seuil ligne 3: ". $cumul3 . ", seuil ligne 4: ". $cumul4;
			log::add('enphasesecur', 'error', $rapport);
		}
		
		foreach (eqLogic::byType('enphasesecur', true) as $eqLogic) {
			if ($eqLogic->getConfiguration('type') == 'groupe') {
				if ($eqLogic->getLogicalId() == 'enphasesecur_G1') { $eqLogic->checkAndUpdateCmd('alarme', $g1);}
				elseif ($eqLogic->getLogicalId() == 'enphasesecur_G2') { $eqLogic->checkAndUpdateCmd('alarme', $g2);}
				elseif ($eqLogic->getLogicalId() == 'enphasesecur_G3') { $eqLogic->checkAndUpdateCmd('alarme', $g3);}
				elseif ($eqLogic->getLogicalId() == 'enphasesecur_G4') { $eqLogic->checkAndUpdateCmd('alarme', $g4);}
			}

		}
	}

	//création des crons pour les onduleurs WH et init cumul export import
	public function creacron(){
		$enphasesecurCron15 = cron::byClassAndFunction(__CLASS__, 'enphasesecurCron15');
        if (!is_object($enphasesecurCron15)) {
            $enphasesecurCron15 = new cron();
            $enphasesecurCron15->setClass('enphasesecur');
            $enphasesecurCron15->setFunction('enphasesecurCron15');
            $enphasesecurCron15->setEnable(1);
			$enphasesecurCron15->setSchedule('*/15 * * * *');
            $enphasesecurCron15->setTimeout('1');
            $enphasesecurCron15->save();
        }
		$enphasesecurCron1d = cron::byClassAndFunction(__CLASS__, 'enphasesecurCron1d');
        if (!is_object($enphasesecurCron1d)) {
            $enphasesecurCron1d = new cron();
            $enphasesecurCron1d->setClass('enphasesecur');
            $enphasesecurCron1d->setFunction('enphasesecurCron1d');
            $enphasesecurCron1d->setEnable(1);
           	$enphasesecurCron1d->setSchedule('0 0 * * *');
            $enphasesecurCron1d->setTimeout('1');
            $enphasesecurCron1d->save();
        }

		$enphasesecurCron1drapport = cron::byClassAndFunction(__CLASS__, 'enphasesecurCron1drapport');
        if (!is_object($enphasesecurCron1drapport)) {
            $enphasesecurCron1d = new cron();
            $enphasesecurCron1d->setClass('enphasesecur');
            $enphasesecurCron1d->setFunction('enphasesecurCron1drapport');
            $enphasesecurCron1d->setEnable(1);
           	$enphasesecurCron1d->setSchedule('0 22 * * *');
            $enphasesecurCron1d->setTimeout('2');
            $enphasesecurCron1d->save();
        }
	  }
	//suppression des cron 
	public function removecron()
	{
		$cron = cron::byClassAndFunction(__CLASS__, 'enphasesecurCron15');
		if(is_object($cron)) {
		  $cron->remove();
		}
	  $cron = cron::byClassAndFunction(__CLASS__, 'enphasesecurCron1d');
	  if(is_object($cron)) 
	  {
		  $cron->remove();
	  }
	  $cron = cron::byClassAndFunction(__CLASS__, 'enphasesecurCron1drapport');
	  if(is_object($cron)) 
	  {
		  $cron->remove();
	  }
	}

	// Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  	public function preSave() {}

	// Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
	public function postSave() {
		//récupération des anciennes configuration => si pas de typo renseigné on fait un combiné
		if ($this->getConfiguration('type') == '' || $this->getConfiguration('type') == null) 
		{
			$this->setConfiguration('type', 'combine');
			$this->save();
		}
		//création des commandes communes pour équipement combiné ou production
		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'prod') 
		{
			//total
			$this->CreaCmd('PwattHoursToday', 'Prod Jour', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'Wh', '1');

			$this->CreaCmd('PwattHoursSevenDays', 'Prod Semaine', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'Wh', '1');
	  		
			$this->CreaCmd('PwattHoursLifetime', 'Prod MES', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'Wh', '1');

			$this->CreaCmd('PwattsNow', 'Prod Inst', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'W', '1');

			//si triphasé
			if (config::bykey('typereseau', __CLASS__) == 'tri') 
			{
				$this->CreaCmd('PwattHoursToday1', 'Prod Jour 1', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('PwattHoursToday2', 'Prod Jour 2', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('PwattHoursToday3', 'Prod Jour 3', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('PwattHoursSevenDays1', 'Prod Semaine 1', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'Wh', '1');
	
				$this->CreaCmd('PwattHoursSevenDays2', 'Prod Semaine 2', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'Wh', '1');
	
				$this->CreaCmd('PwattHoursSevenDays3', 'Prod Semaine 3', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'Wh', '1');
	
				$this->CreaCmd('PwattHoursLifetime1', 'Prod MES 1', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'Wh', '1');
	
				$this->CreaCmd('PwattHoursLifetime2', 'Prod MES 2', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('PwattHoursLifetime3', 'Prod MES 3', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('PwattsNow1', 'Prod Inst 1', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'W', '1');

				$this->CreaCmd('PwattsNow2', 'Prod Inst 2', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'W', '1');

				$this->CreaCmd('PwattsNow3', 'Prod Inst 3', 'core::badge', '1', '3', 'POWER','info', 'numeric', 'W', '1');
			}
		}

		//création des commandes communes pour équipement combiné ou conso total
		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'total') 
		{
			//total
			$this->CreaCmd('CwattHoursToday', 'Conso Total Jour', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

			$this->CreaCmd('CwattHoursSevenDays', 'Conso Total Semaine', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

			$this->CreaCmd('CwattHoursLifetime', 'Conso Total MES', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

			$this->CreaCmd('CwattsNow', 'Conso Total Inst', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

			//si triphasé
			if (config::bykey('typereseau', __CLASS__) == 'tri') 
			{
				$this->CreaCmd('CwattHoursToday1', 'Conso Total Jour1', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursToday2', 'Conso Total Jour2', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursToday3', 'Conso Total Jour3', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursSevenDays1', 'Conso Total Semaine1', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursSevenDays2', 'Conso Total Semaine2', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursSevenDays3', 'Conso Total Semaine3', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursLifetime1', 'Conso Total MES1', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursLifetime2', 'Conso Total MES5', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursLifetime3', 'Conso Total MES3', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattsNow1', 'Conso Total Inst1', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

				$this->CreaCmd('CwattsNow2', 'Conso Total Inst2', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

				$this->CreaCmd('CwattsNow3', 'Conso Total Inst3', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');
			}
		}

		//création des commandes communes pour équipement combiné ou net/total conso
		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'net' || $this->getConfiguration('type') == 'total') 
		{
			//total
			$this->CreaCmd('tension', 'Tension', 'core::badge', '1', '0', 'VOLTAGE','info', 'numeric', 'V', '1');
			
			if (config::bykey('typereseau', __CLASS__) == 'tri') 
			{
				//si triphasé
				$this->CreaCmd('tension1', 'Tension 1', 'core::badge', '1', '0', 'VOLTAGE','info', 'numeric', 'V', '1');

				$this->CreaCmd('tension2', 'Tension 2', 'core::badge', '1', '0', 'VOLTAGE','info', 'numeric', 'V', '1');

				$this->CreaCmd('tension3', 'Tension 3', 'core::badge', '1', '0', 'VOLTAGE','info', 'numeric', 'V', '1');

			}
		}
		//création des commandes communes pour équipement combiné ou conso net	
		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'net') 
		{
			$this->CreaCmd('CwattHoursTodayNet', 'Conso Net Jour', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

			$this->CreaCmd('CwattHoursSevenDaysNet', 'Conso Net Semaine', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');
  
			$this->CreaCmd('CwattHoursLifetimeNet', 'Conso Net MES', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');
			
			$this->CreaCmd('CwattsNowNet', 'Conso Net Inst', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

			$this->CreaCmd('Export', 'Export Réseau', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

			$this->CreaCmd('Import', 'Impor Réseau', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

			$this->CreaCmd('cumulexport', 'Export Jour Réseau', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

			$this->CreaCmd('cumulimport', 'Import Jour Réseau', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

			$this->CreaCmd('calculjour', 'Calcul Jour, ne pas toucher', 'core::badge', '1', '', 'CONSUMPTION','info', 'numeric', 'Wh', '0');

			//si triphasé
			if (config::bykey('typereseau', __CLASS__) == 'tri') 
			{
				$this->CreaCmd('CwattHoursTodayNet1', 'Conso Net Jour1', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursTodayNet2', 'Conso Net Jour2', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursTodayNet3', 'Conso Net Jour3', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursSevenDaysNet1', 'Conso Net Semaine1', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursSevenDaysNet2', 'Conso Net Semaine2', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursSevenDaysNet3', 'Conso Net Semaine3', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');
	
				$this->CreaCmd('CwattHoursLifetimeNet1', 'Conso Net MES1', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursLifetimeNet2', 'Conso Net MES2', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('CwattHoursLifetimeNet3', 'Conso Net MES3', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');
				
				$this->CreaCmd('CwattsNowNet1', 'Conso Net Inst1', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

				$this->CreaCmd('CwattsNowNet2', 'Conso Net Inst2', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

				$this->CreaCmd('CwattsNowNet3', 'Conso Net Inst3', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

				$this->CreaCmd('Export1', 'Export Réseau1', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

				$this->CreaCmd('Export2', 'Export Réseau2', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

				$this->CreaCmd('Export3', 'Export Réseau3', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

				$this->CreaCmd('Import1', 'Impor Réseau1', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

				$this->CreaCmd('Import2', 'Impor Réseau2', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

				$this->CreaCmd('Import3', 'Impor Réseau3', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

				$this->CreaCmd('cumulexport1', 'Export Jour Réseau1', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('cumulexport2', 'Export Jour Réseau2', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('cumulexport3', 'Export Jour Réseau3', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('cumulimport1', 'Import Jour Réseau1', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('cumulimport2', 'Import Jour Réseau2', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('cumulimport3', 'Import Jour Réseau3', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

				$this->CreaCmd('calculjour1', 'Calcul Jour, ne pas toucher1', 'core::badge', '1', '', 'CONSUMPTION','info', 'numeric', 'Wh', '0');

				$this->CreaCmd('calculjour2', 'Calcul Jour, ne pas toucher2', 'core::badge', '1', '', 'CONSUMPTION','info', 'numeric', 'Wh', '0');

				$this->CreaCmd('calculjour3', 'Calcul Jour, ne pas toucher3', 'core::badge', '1', '', 'CONSUMPTION','info', 'numeric', 'Wh', '0');

				$this->CreaCmd('autoconso11', 'Autoconso 1 phase 1', '', '1', '', '','info', 'binary', '', '1');

				$this->CreaCmd('autoconso12', 'Autoconso 1 phase 2', '', '1', '', '','info', 'binary', '', '1');

				$this->CreaCmd('autoconso13', 'Autoconso 1 phase 3', '', '1', '', '','info', 'binary', '', '1');
			
			}
			else { $this->CreaCmd('autoconso1', 'Autoconso 1', '', '1', '', '','info', 'binary', '', '1'); }
		}
		//création des commandes communes pour équipement combiné ou batterie
		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'bat') 
		{
			$this->CreaCmd('batnow', 'Puissance délivrée', 'core::badge', '1', '3', 'CONSUMPTION','info', 'numeric', 'W', '1');

			$this->CreaCmd('batperc', 'Pourcentage charge', 'core::badge', '1', '', 'CONSUMPTION','info', 'numeric', '%', '1');
		}
		
		if ($this->getConfiguration('type') == 'groupe') 
		{
			$this->CreaCmd('maxWatt', 'Puissance Max', 'core::badge', '1', '', 'CONSUMPTION','info', 'numeric', 'W', '1');

			$this->CreaCmd('Watt', 'Puissance', 'core::badge', '1', '', 'CONSUMPTION','info', 'numeric', 'W', '1');

			$this->CreaCmd('calWH', 'Production journalière', 'core::badge', '1', '', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

			$this->CreaCmd('alarme', 'Alarme Groupe', 'core::alert', '1', '', 'CONSUMPTION','info', 'binary', '', '1');
		}

		if ($this->getConfiguration('type') == 'conv') 
		{
			$this->CreaCmd('maxWatt', 'Puissance Max', 'core::badge', '1', '', 'CONSUMPTION','info', 'numeric', 'W', '1');

			$this->CreaCmd('Watt', 'Puissance', 'core::badge', '1', '', 'CONSUMPTION','info', 'numeric', 'W', '1');

			$this->CreaCmd('calWH', 'Production journalière', 'core::badge', '1', '', 'CONSUMPTION','info', 'numeric', 'Wh', '1');

			self::removecron();
			self::creacron();
		}
  	}
	// Fonction exécutée automatiquement avant la suppression de l'équipement
  	public function preRemove() 
	{
		self::deamon_stop();
	}

	// Fonction exécutée automatiquement après la suppression de l'équipement
  	public function postRemove() {}
	
	//Fonction pour trouver un port free
	public static function getFreePort() {
		$freePortFound = false;
		while (!$freePortFound) {
			$port = mt_rand(1024, 65535);
			exec('sudo fuser '.$port.'/tcp',$out,$return);
			if ($return==1) {
				$freePortFound = true;
			}
		}
		config::save('socketport',$port,'enphasesecur');
		return $port;
	}

	public static function deamon_info() {
        $return = array();
        $return['log'] = __CLASS__;
        $return['state'] = 'nok';
        $pid_file = jeedom::getTmpFolder(__CLASS__) . '/deamon.pid';
        if (file_exists($pid_file)) {
            if (@posix_getsid(trim(file_get_contents($pid_file)))) {
                $return['state'] = 'ok';
            } else {
                shell_exec(system::getCmdSudo() . 'rm -rf ' . $pid_file . ' 2>&1 > /dev/null');
            }
        }
        $return['launchable'] = 'ok';

		if (config::byKey('ctoken', __CLASS__) == 'auto'){
        	//if ((config::byKey('user', __CLASS__) == '') || (config::byKey('password', __CLASS__) == '') || (config::byKey('site', __CLASS__) == '') || (config::byKey('ip', __CLASS__) == '') || (config::byKey('serie', __CLASS__) == '')) {
			if ((config::byKey('user', __CLASS__) == '') || (config::byKey('password', __CLASS__) == '') || (config::byKey('ip', __CLASS__) == '') || (config::byKey('serie', __CLASS__) == '')) {
	
				$return['launchable'] = 'nok';
            	$return['launchable_message'] = __('Toutes les informations obligatoires ne sont pas remplies', __FILE__);
			}
		}
		else {
			if ((config::byKey('token', __CLASS__) == '') || (config::byKey('ip', __CLASS__) == '')) {
            	$return['launchable'] = 'nok';
            	$return['launchable_message'] = __('Toutes les informations obligatoires ne sont pas remplies', __FILE__);
			}
		}
        return $return;
    }

	public static function deamon_start() {
        self::deamon_stop();
		//self::creationmaj();
		//trouver un port de libre pour la communication daemon
		self::getFreePort();

		//delais par défaut de 60s pour l'interrogation de la passerelle
		if (config::byKey('delais', __CLASS__) == ''){
			config::save('delais','60','enphasesecur');
		}
		
       $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != 'ok') {
            throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
        }

		if (config::byKey('delais', __CLASS__) == ''){
			config::save('delais','60','enphasesecur');
		}
		//création des équipements suivant la configuration
		enphasesecur::creationmaj();
		
		$path = realpath(dirname(__FILE__) . '/../../resources/enphasesecurd'); // répertoire du démon
		$cmd = 'python3 ' . $path . '/enphasesecurd.py'; // nom du démon
		$cmd .= ' --renew "' . trim(str_replace('"', '\"', config::byKey('ctoken', __CLASS__))) . '"'; 
		$cmd .= ' --loglevel ' . log::convertLogLevel(log::getLogLevel(__CLASS__));
		$cmd .= ' --socketport ' . config::byKey('socketport', __CLASS__); // port par défaut
		$cmd .= ' --callback ' . network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/enphasesecur/core/php/jeeenphasesecur.php'; // chemin de la callback url à modifier (voir ci-dessous)
		$cmd .= ' --user "' . trim(str_replace('"', '\"', config::byKey('user', __CLASS__))) . '"'; 
		$cmd .= ' --password "' . trim(str_replace('"', '\"', config::byKey('password', __CLASS__))) . '"'; 
		$cmd .= ' --serie "' . trim(str_replace('"', '\"', config::byKey('serie', __CLASS__))) . '"'; 
		$cmd .= ' --token "' . trim(str_replace('"', '\"', config::byKey('token', __CLASS__))) . '"'; 
		$cmd .= ' --ip "' . trim(str_replace('"', '\"', config::byKey('ip', __CLASS__))) . '"'; 
		$cmd .= ' --apikey ' . jeedom::getApiKey(__CLASS__); // l'apikey pour authentifier les échanges suivants
		$cmd .= ' --pid ' . jeedom::getTmpFolder(__CLASS__) . '/deamon.pid'; // et on précise le chemin vers le pid file (ne pas modifier)
		$cmd .= ' --delais '  . config::byKey('delais', __CLASS__); // delais actualisation
		log::add(__CLASS__, 'info', $cmd);
        log::add(__CLASS__, 'info', 'Lancement démon');
        $result = exec($cmd . ' >> ' . log::getPathToLog('enphasesecur_daemon') . ' 2>&1 &'); // 'template_daemon' est le nom du log pour votre démon, vous devez nommer votre log en commençant par le pluginid pour que le fichier apparaisse dans la page de config
        $i = 0;
        while ($i < 20) {
            $deamon_info = self::deamon_info();
            if ($deamon_info['state'] == 'ok') {
                break;
            }
            sleep(1);
            $i++;
        }
        if ($i >= 30) {
            log::add(__CLASS__, 'error', __('Impossible de lancer le démon, vérifiez le log', __FILE__), 'unableStartDeamon');
            return false;
        }
        message::removeAll(__CLASS__, 'unableStartDeamon');
        return true;
    }
	
	public static function deamon_stop() {
        $pid_file = jeedom::getTmpFolder(__CLASS__) . '/deamon.pid'; // ne pas modifier
        if (file_exists($pid_file)) {
            $pid = intval(trim(file_get_contents($pid_file)));
            system::kill($pid);
        }
        system::kill('enphasesecurd.py'); // nom du démon à modifier
        sleep(1);
    }
}

class enphasesecurCmd extends cmd {
	// Exécution d'une commande
  	public function execute($_options = array()) {}
}