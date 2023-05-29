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

class enphasesecur extends eqLogic {
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
	public static function dependancy_install() {
		log::remove(__CLASS__ . '_update');
		passthru('/bin/bash ' . dirname(__FILE__) . '/../../resources/install_apt.sh ' . jeedom::getTmpFolder(__CLASS__) . '/dependency > ' . log::getPathToLog(__CLASS__ . '_update') . ' 2>&1 &');
	}

	public static function dependancy_install_update() {
		log::remove(__CLASS__ . '_update');
	}

	//fonction pour la création des équipement suivant la configuration choisie
	public static function creationmaj() {
		foreach (self::byType('enphasesecur', true) as $eqLogic) {
			if (config::bykey('widget', __CLASS__) == 1){
				if ($eqLogic->getConfiguration('type') == 'net' || $eqLogic->getConfiguration('type') == 'total' || $eqLogic->getConfiguration('type') == 'bat' || $eqLogic->getConfiguration('type') == 'prod') {
					log::add('enphasesecur', 'info', 'Suppression équipement suite à changement de mode vers combiné');
					$eqLogic->remove();
				}
			}
			else {
				if ($eqLogic->getConfiguration('type') == 'combine') {
					log::add('enphasesecur', 'info', 'Suppression équipement suite à changement de mode vers divisé');
					$eqLogic->remove();
				}
			}
		}

		if (config::bykey('widget', __CLASS__) == 1){

			if (!is_object(eqLogic::byLogicalId('enphasesecur_combine', 'enphasesecur'))) {
				log::add('enphasesecur', 'debug', 'Création équipement combiné');
				$eqLogic = new self();
				$eqLogic->setLogicalId('enphasesecur_combine');
				$eqLogic->setName('Passerelle Enphase');
				$eqLogic->setCategory('energy', 1);
				$eqLogic->setEqType_name('enphasesecur');
				$eqLogic->setConfiguration('type', 'combine');
				$eqLogic->setIsVisible(1);
				$eqLogic->setIsEnable(1);
				$eqLogic->save();
			}
		}
		else {
			if(!is_object(eqLogic::byLogicalId('enphasesecur_prod', 'enphasesecur'))) {
				log::add('enphasesecur', 'debug', 'Création équipement Production');
				$eqLogic = new self();
				$eqLogic->setLogicalId('enphasesecur_prod');
				$eqLogic->setName('Enphase Production');
				$eqLogic->setCategory('energy', 1);
				$eqLogic->setEqType_name('enphasesecur');
				$eqLogic->setIsVisible(1);
				$eqLogic->setIsEnable(1);
				$eqLogic->setConfiguration('type', 'prod');
				$eqLogic->save();
			}
			if(!is_object(eqLogic::byLogicalId('enphasesecur_conso_net', 'enphasesecur'))) {
				log::add('enphasesecur', 'debug', 'Création équipement Consommation Net');
				$eqLogic = new self();
				$eqLogic->setLogicalId('enphasesecur_conso_net');
				$eqLogic->setName('Enphase Consommation Net');
				$eqLogic->setCategory('energy', 1);
				$eqLogic->setEqType_name('enphasesecur');
				$eqLogic->setConfiguration('type', 'net');
				$eqLogic->setIsVisible(1);
				$eqLogic->setIsEnable(1);
				$eqLogic->save();
			}
			if(!is_object(eqLogic::byLogicalId('enphasesecur_conso_total', 'enphasesecur'))) {
				log::add('enphasesecur', 'debug', 'Création équipement Consommation Total');
				$eqLogic = new self();
				$eqLogic->setLogicalId('enphasesecur_conso_total');
				$eqLogic->setName('Enphase Consommation Total');
				$eqLogic->setCategory('energy', 1);
				$eqLogic->setEqType_name('enphasesecur');
				$eqLogic->setConfiguration('type', 'total');
				$eqLogic->setIsVisible(1);
				$eqLogic->setIsEnable(1);
				$eqLogic->save();
			}
			if(!is_object(eqLogic::byLogicalId('enphasesecur_bat', 'enphasesecur'))) {
				log::add('enphasesecur', 'debug', 'Création équipement Stockage');
				$eqLogic = new self();
				$eqLogic->setLogicalId('enphasesecur_bat');
				$eqLogic->setName('Enphase Stockage');
				$eqLogic->setCategory('energy', 1);
				$eqLogic->setEqType_name('enphasesecur');
				$eqLogic->setConfiguration('type', 'bat');
				$eqLogic->setIsVisible(1);
				$eqLogic->setIsEnable(1);
				$eqLogic->save();
			}
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
		foreach (eqLogic::byType('enphasesecur', true) as $eqLogic) {
			//ajout valeur au wh onduleur
			if ($eqLogic->getConfiguration('type') == 'conv') {
				$ancienprod = $eqLogic->getCmd(null, 'calWH')->execCmd();
				$puissance = $eqLogic->getCmd(null, 'Watt')->execCmd();
				if ($puissance!=0) {
					$prod = $ancienprod + ($puissance*0.25);
					$eqLogic->checkAndUpdateCmd('calWH', $prod);
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
	  }
	//suppression des cron 
	public function removecron(){
		$cron = cron::byClassAndFunction(__CLASS__, 'enphasesecurCron15');
		if(is_object($cron)) {
		  $cron->remove();
		}
	  $cron = cron::byClassAndFunction(__CLASS__, 'enphasesecurCron1d');
	  if(is_object($cron)) {
		  $cron->remove();
	  }
	}

	// Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  	public function preSave() {}

	// Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
	public function postSave() {
		//récupération des anciennes configuration => si pas de typo renseigné on fait un combiné
		if ($this->getConfiguration('type') == '' || $this->getConfiguration('type') == null) {
			$this->setConfiguration('type', 'combine');
			$this->save();
		}
		//création des commandes communes pour équipement combiné ou production
		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'prod') {
			//total
			$enphasesecurCmd = $this->getCmd(null, 'PwattHoursToday');
	  		if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Prod Jour', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setGeneric_type('POWER'); 
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('PwattHoursToday');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('Wh');
				$enphasesecurCmd->save();
	  		}
	  		
			$enphasesecurCmd = $this->getCmd(null, 'PwattHoursSevenDays');
	  		if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Prod Semaine', __FILE__));
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setGeneric_type('POWER');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('PwattHoursSevenDays');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('Wh');
				$enphasesecurCmd->save();
	  		}

			$enphasesecurCmd = $this->getCmd(null, 'PwattHoursLifetime');
	  		if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Prod MES', __FILE__));
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('0');
				$enphasesecurCmd->setGeneric_type('POWER');
				$enphasesecurCmd->setEqLogic_id($this->getId());
	  			$enphasesecurCmd->setLogicalId('PwattHoursLifetime');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('Wh');
				$enphasesecurCmd->save();
	  		}
			  else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}

			$enphasesecurCmd = $this->getCmd(null, 'PwattsNow');
	  		if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Prod Inst', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
				$enphasesecurCmd->setGeneric_type('POWER');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('PwattsNow');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('W');
				$enphasesecurCmd->save();
	  		}

			//si triphasé
			if (config::bykey('typereseau', __CLASS__) == 'tri') 
			{
				//phase 1
				$enphasesecurCmd = $this->getCmd(null, 'PwattHoursToday1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Prod Jour 1', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('POWER'); 
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('PwattHoursToday1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
	  		
				$enphasesecurCmd = $this->getCmd(null, 'PwattHoursSevenDays1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Prod Semaine 1', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setGeneric_type('POWER');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('PwattHoursSevenDays1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}

				$enphasesecurCmd = $this->getCmd(null, 'PwattHoursLifetime1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Prod MES 1 ', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('0');
					$enphasesecurCmd->setGeneric_type('POWER');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('PwattHoursLifetime1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'PwattsNow1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Prod Inst 1', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
					$enphasesecurCmd->setGeneric_type('POWER');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('PwattsNow1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
				//phase 2
				$enphasesecurCmd = $this->getCmd(null, 'PwattHoursToday2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Prod Jour 2', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('POWER'); 
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('PwattHoursToday2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				
			  	$enphasesecurCmd = $this->getCmd(null, 'PwattHoursSevenDays2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
						$enphasesecurCmd->setName(__('Prod Semaine 2', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setGeneric_type('POWER');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('PwattHoursSevenDays2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
  
			  	$enphasesecurCmd = $this->getCmd(null, 'PwattHoursLifetime2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Prod MES 2', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('0');
					$enphasesecurCmd->setGeneric_type('POWER');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('PwattHoursLifetime2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
  
			  	$enphasesecurCmd = $this->getCmd(null, 'PwattsNow2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Prod Inst 2', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
					$enphasesecurCmd->setGeneric_type('POWER');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('PwattsNow2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
				
				//phase3
				$enphasesecurCmd = $this->getCmd(null, 'PwattHoursToday3');
	  			if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Prod Jour 3', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('POWER'); 
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('PwattHoursToday3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
	  			}
	  		
				$enphasesecurCmd = $this->getCmd(null, 'PwattHoursSevenDays3');
	  			if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Prod Semaine 3', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setGeneric_type('POWER');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('PwattHoursSevenDays3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
	  			}

				$enphasesecurCmd = $this->getCmd(null, 'PwattHoursLifetime3');
	  			if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Prod MES 3', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('0');
					$enphasesecurCmd->setGeneric_type('POWER');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('PwattHoursLifetime3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
	  			}
				  else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'PwattsNow3');
	  			if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Prod Inst 3', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
					$enphasesecurCmd->setGeneric_type('POWER');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('PwattsNow3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
			}
		}

		//création des commandes communes pour équipement combiné ou conso total
		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'total') {
			//total
			$enphasesecurCmd = $this->getCmd(null, 'CwattHoursToday');
			if (!is_object($enphasesecurCmd)) {
		  		$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Conso Total Jour', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('CwattHoursToday');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('Wh');
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}
			

	  		$enphasesecurCmd = $this->getCmd(null, 'CwattHoursSevenDays');
			if (!is_object($enphasesecurCmd)) {
		  		$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Conso Total Semaine', __FILE__));
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('CwattHoursSevenDays');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('Wh');
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}

	  		$enphasesecurCmd = $this->getCmd(null, 'CwattHoursLifetime');
			if (!is_object($enphasesecurCmd)) {
		  		$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Conso Total MES', __FILE__));
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('0');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('CwattHoursLifetime');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('Wh');
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}

	  		$enphasesecurCmd = $this->getCmd(null, 'CwattsNow');
			if (!is_object($enphasesecurCmd)) {
		  		$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Conso Total Inst', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('CwattsNow');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('W');
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}

			//si triphasé
			if (config::bykey('typereseau', __CLASS__) == 'tri') {
				//phase 1
				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursToday1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Total Jour 1 ', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursToday1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursSevenDays1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Total Semaine 1 ', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursSevenDays1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursLifetime1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Total MES 1', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('0');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursLifetime1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'CwattsNow1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Total Inst 1 ', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattsNow1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				//phase 2
				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursToday2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Total Jour 2', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursToday2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursSevenDays2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Total Semaine 2', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursSevenDays2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursLifetime2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Total MES 2', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('0');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursLifetime2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
				

				$enphasesecurCmd = $this->getCmd(null, 'CwattsNow2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Total Inst 2', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattsNow2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
				
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				//phase 3
				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursToday3');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Total Jour 3', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursToday3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursSevenDays3');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Total Semaine 3', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursSevenDays3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursLifetime3');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Total MES 3', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('0');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursLifetime3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
				

				$enphasesecurCmd = $this->getCmd(null, 'CwattsNow3');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Total Inst 3', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattsNow3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
			}
		}

		//création des commandes communes pour équipement combiné ou net/total conso
		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'net' || $this->getConfiguration('type') == 'total') {
			//total
			$enphasesecurCmd = $this->getCmd(null, 'tension');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Tension', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setGeneric_type('VOLTAGE');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('tension');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('V');
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}

			//si triphasé
			if (config::bykey('typereseau', __CLASS__) == 'tri') {
				//phase 1
				$enphasesecurCmd = $this->getCmd(null, 'tension1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Tension 1', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setGeneric_type('VOLTAGE');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('tension1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('V');
					$enphasesecurCmd->save();
				}
				//phase 2
				$enphasesecurCmd = $this->getCmd(null, 'tension2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Tension 2', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setGeneric_type('VOLTAGE');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('tension2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('V');
					$enphasesecurCmd->save();
				}
				//phase 3
				$enphasesecurCmd = $this->getCmd(null, 'tension3');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Tension 3', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setGeneric_type('VOLTAGE');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('tension3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('V');
					$enphasesecurCmd->save();
				}
			}
		}
		//création des commandes communes pour équipement combiné ou conso net	
		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'net') {
			//total
			$enphasesecurCmd = $this->getCmd(null, 'CwattHoursTodayNet');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Conso Net Jour', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('CwattHoursTodayNet');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('Wh'); 
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}
  
			$enphasesecurCmd = $this->getCmd(null, 'CwattHoursSevenDaysNet');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Conso Net Semaine', __FILE__));
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('CwattHoursSevenDaysNet');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('Wh');
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}
		
			$enphasesecurCmd = $this->getCmd(null, 'CwattHoursLifetimeNet');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Conso Net MES', __FILE__));
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('0');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('CwattHoursLifetimeNet');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('Wh');
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}
			
  
			$enphasesecurCmd = $this->getCmd(null, 'CwattsNowNet');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Conso Net Inst', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('CwattsNowNet');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('W');
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}
			
			$enphasesecurCmd = $this->getCmd(null, 'Export');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Export Réseau', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('Export');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('W');
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}

			$enphasesecurCmd = $this->getCmd(null, 'Import');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Import Réseau', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('Import');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('W');
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}

			$enphasesecurCmd = $this->getCmd(null, 'cumulimport');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Import Jour Réseau', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('cumulimport');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('Wh');
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}

			$enphasesecurCmd = $this->getCmd(null, 'cumulexport');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Export Jour Réseau', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('cumulexport');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('Wh');
				$enphasesecurCmd->save();
			}

			//si triphasé
			if (config::bykey('typereseau', __CLASS__) == 'tri') {
				//phase1
				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursTodayNet1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Net Jour 1 ', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursTodayNet1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh'); 
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
	
				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursSevenDaysNet1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Net Semaine 1 ', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursSevenDaysNet1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
			
				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursLifetimeNet1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Net MES 1 ', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('0');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursLifetimeNet1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
				
	
				$enphasesecurCmd = $this->getCmd(null, 'CwattsNowNet1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Net Inst 1 ', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattsNowNet1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
				
				$enphasesecurCmd = $this->getCmd(null, 'Export1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Export Réseau 1 ', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('Export1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'autoconso11');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Autoconso 1 phase1 ', __FILE__));
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('autoconso11');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('binary');
					$enphasesecurCmd->save();
				}
				// $enphasesecurCmd = $this->getCmd(null, 'autoconso21');
				// if (!is_object($enphasesecurCmd)) {
				// 	$enphasesecurCmd = new enphasesecurCmd();
				// 	$enphasesecurCmd->setName(__('Autoconso 2 phase1 ', __FILE__));
				// 	$enphasesecurCmd->setIsHistorized('1');
				// 	$enphasesecurCmd->setEqLogic_id($this->getId());
				// 	$enphasesecurCmd->setLogicalId('autoconso21');
				// 	$enphasesecurCmd->setType('info');
				// 	$enphasesecurCmd->setSubType('binary');
				// 	$enphasesecurCmd->save();
				// }

				$enphasesecurCmd = $this->getCmd(null, 'Import1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Import Réseau 1 ', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('Import1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}

				$enphasesecurCmd = $this->getCmd(null, 'cumulimport1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Import Jour Réseau 1', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('cumulimport1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}

				$enphasesecurCmd = $this->getCmd(null, 'cumulexport1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Export Jour Réseau 1', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('cumulexport1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}


				//phase 2
				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursTodayNet2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Net Jour 2', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursTodayNet2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh'); 
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
	
				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursSevenDaysNet2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Net Semaine 2', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursSevenDaysNet2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
			
				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursLifetimeNet2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Net MES 2', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('0');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursLifetimeNet2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
				
	
				$enphasesecurCmd = $this->getCmd(null, 'CwattsNowNet2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Net Inst 2', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattsNowNet2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
				
				$enphasesecurCmd = $this->getCmd(null, 'Export2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Export Réseau 2', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('Export2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'autoconso12');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Autoconso 1 phase2 ', __FILE__));
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('autoconso12');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('binary');
					$enphasesecurCmd->save();
				}
				// $enphasesecurCmd = $this->getCmd(null, 'autoconso22');
				// if (!is_object($enphasesecurCmd)) {
				// 	$enphasesecurCmd = new enphasesecurCmd();
				// 	$enphasesecurCmd->setName(__('Autoconso 2 phase2 ', __FILE__));
				// 	$enphasesecurCmd->setIsHistorized('1');
				// 	$enphasesecurCmd->setEqLogic_id($this->getId());
				// 	$enphasesecurCmd->setLogicalId('autoconso22');
				// 	$enphasesecurCmd->setType('info');
				// 	$enphasesecurCmd->setSubType('binary');
				// 	$enphasesecurCmd->save();
				// }


				$enphasesecurCmd = $this->getCmd(null, 'Import2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Import Réseau 2', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('Import2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'cumulimport2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Import Jour Réseau 2', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('cumulimport2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
	
				$enphasesecurCmd = $this->getCmd(null, 'cumulexport2');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Export Jour Réseau 2', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('cumulexport2');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}

				//phase 3
				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursTodayNet3');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Net Jour 3', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursTodayNet3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh'); 
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
	
				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursSevenDaysNet3');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Net Semaine 3', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursSevenDaysNet3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
			
				$enphasesecurCmd = $this->getCmd(null, 'CwattHoursLifetimeNet3');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Net MES 3', __FILE__));
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('0');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattHoursLifetimeNet3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'CwattsNowNet3');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Conso Net Inst 3', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('CwattsNowNet3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
				
				$enphasesecurCmd = $this->getCmd(null, 'Export3');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Export Réseau 3', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('Export3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('W');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}

				$enphasesecurCmd = $this->getCmd(null, 'autoconso13');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Autoconso 1 phase3 ', __FILE__));
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('autoconso13');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('binary');
					$enphasesecurCmd->save();
				}
				// // $enphasesecurCmd = $this->getCmd(null, 'autoconso23');
				// // if (!is_object($enphasesecurCmd)) {
				// // 	$enphasesecurCmd = new enphasesecurCmd();
				// // 	$enphasesecurCmd->setName(__('Autoconso 2 phase3 ', __FILE__));
				// // 	$enphasesecurCmd->setIsHistorized('1');
				// // 	$enphasesecurCmd->setEqLogic_id($this->getId());
				// // 	$enphasesecurCmd->setLogicalId('autoconso23');
				// // 	$enphasesecurCmd->setType('info');
				// // 	$enphasesecurCmd->setSubType('binary');
				// // 	$enphasesecurCmd->save();
				// // }


				 $enphasesecurCmd = $this->getCmd(null, 'Import3');
				 if (!is_object($enphasesecurCmd)) {
				 	$enphasesecurCmd = new enphasesecurCmd();
				 	$enphasesecurCmd->setName(__('Import Réseau 3', __FILE__));
				 	$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				 	$enphasesecurCmd->setIsHistorized('1');
				 	$enphasesecurCmd->setConfiguration('historizeRound', '3');
				 	$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				 	$enphasesecurCmd->setEqLogic_id($this->getId());
				 	$enphasesecurCmd->setLogicalId('Import3');
				 	$enphasesecurCmd->setType('info');
				 	$enphasesecurCmd->setSubType('numeric');
				 	$enphasesecurCmd->setUnite('W');
				 	$enphasesecurCmd->save();
				 }

				 $enphasesecurCmd = $this->getCmd(null, 'cumulimport3');
				 if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
				 	$enphasesecurCmd->setName(__('Import Jour Réseau 3', __FILE__));
				 	$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				 	$enphasesecurCmd->setIsHistorized('1');
				 	$enphasesecurCmd->setConfiguration('historizeRound', '3');
				 	$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				 	$enphasesecurCmd->setEqLogic_id($this->getId());
				 	$enphasesecurCmd->setLogicalId('cumulimport3');
				 	$enphasesecurCmd->setType('info');
				 	$enphasesecurCmd->setSubType('numeric');
				 	$enphasesecurCmd->setUnite('Wh');
				 	$enphasesecurCmd->save();
				 }
	
				$enphasesecurCmd = $this->getCmd(null, 'cumulexport3');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Export Jour Réseau 3', __FILE__));
					$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->setGeneric_type('CONSUMPTION');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('cumulexport3');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('numeric');
					$enphasesecurCmd->setUnite('Wh');
					$enphasesecurCmd->save();
				}
				else {
					if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
						$enphasesecurCmd->setConfiguration('historizeRound', '3');
						$enphasesecurCmd->save();
					}
				}
			}
			else {
				$enphasesecurCmd = $this->getCmd(null, 'autoconso1');
				if (!is_object($enphasesecurCmd)) {
					$enphasesecurCmd = new enphasesecurCmd();
					$enphasesecurCmd->setName(__('Autoconso 1', __FILE__));
					$enphasesecurCmd->setIsHistorized('1');
					$enphasesecurCmd->setEqLogic_id($this->getId());
					$enphasesecurCmd->setLogicalId('autoconso1');
					$enphasesecurCmd->setType('info');
					$enphasesecurCmd->setSubType('binary');
					$enphasesecurCmd->save();
				}
				// $enphasesecurCmd = $this->getCmd(null, 'autoconso2');
				// if (!is_object($enphasesecurCmd)) {
				// 	$enphasesecurCmd = new enphasesecurCmd();
				// 	$enphasesecurCmd->setName(__('Autoconso 2', __FILE__));
				// 	$enphasesecurCmd->setIsHistorized('1');
				// 	$enphasesecurCmd->setEqLogic_id($this->getId());
				// 	$enphasesecurCmd->setLogicalId('autoconso2');
				// 	$enphasesecurCmd->setType('info');
				// 	$enphasesecurCmd->setSubType('binary');
				// 	$enphasesecurCmd->save();
				// }
			}
		}
		//création des commandes communes pour équipement combiné ou batterie
		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'bat') {
			$enphasesecurCmd = $this->getCmd(null, 'batnow');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Puissance délivrée', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('batnow');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('W');
				$enphasesecurCmd->save();
			}
			else {
				if ($enphasesecurCmd->getConfiguration('historizeRound')<3 || $enphasesecurCmd->getConfiguration('historizeRound') != '') {
					$enphasesecurCmd->setConfiguration('historizeRound', '3');
					$enphasesecurCmd->save();
				}
			}

			$enphasesecurCmd = $this->getCmd(null, 'batperc');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Pourcentage charge', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				//$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setLogicalId('batperc');
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('%');
				$enphasesecurCmd->save();
			}
		}
		
		if ($this->getConfiguration('type') == 'conv') {
			$enphasesecurCmd = $this->getCmd(null, 'maxWatt');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Puissance Max', __FILE__));
				$enphasesecurCmd->setIsVisible(true);
				$enphasesecurCmd->setIsHistorized(true);
				$enphasesecurCmd->setLogicalId('maxWatt');
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('W');
				$enphasesecurCmd->save();
			}

			$enphasesecurCmd = $this->getCmd(null, 'Watt');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Puissance', __FILE__));
				$enphasesecurCmd->setIsVisible(true);
				$enphasesecurCmd->setIsHistorized(true);
				$enphasesecurCmd->setLogicalId('Watt');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
				$enphasesecurCmd->setEqLogic_id($this->getId());
				$enphasesecurCmd->setType('info');
				$enphasesecurCmd->setSubType('numeric');
				$enphasesecurCmd->setUnite('W');
				$enphasesecurCmd->save();
			}
			
			$enphasesecurCmd = $this->getCmd(null, 'calWH');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Production journalière', __FILE__));
				$enphasesecurCmd->setIsVisible(true);
				$enphasesecurCmd->setIsHistorized(true);
				$enphasesecurCmd->setLogicalId('calWH');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
        			$enphasesecurCmd->setEqLogic_id($this->getId());
        			$enphasesecurCmd->setType('info');
        			$enphasesecurCmd->setSubType('numeric');
        			$enphasesecurCmd->setUnite('Wh');
        			$enphasesecurCmd->save();
			}

			self::removecron();
			self::creacron();
		}
  	}
	// Fonction exécutée automatiquement avant la suppression de l'équipement
  	public function preRemove() {
		self::deamon_stop();
	}

	// Fonction exécutée automatiquement après la suppression de l'équipement
  	public function postRemove() {}

	// Fontion pour widget NU pour le moment
	// public function toHtml($_version = 'dashboard') {
	// 	if ($this->getConfiguration('widgetTemplate') != 1) {
	// 		return parent::toHtml($_version);
	// 	 }
	// 	$replace = $this->preToHtml($_version);
	// 	if (!is_array($replace)) {
	// 		return $replace;
	// 	}
	// 	$version = jeedom::versionAlias($_version);
	
	// 	foreach (($this->getCmd('info')) as $cmd) {
	// 		$logical = $cmd->getLogicalId();
	// 		$collectDate = $cmd->getCollectDate();
		
	// 		$replace['#' . $logical . '_id#'] = $cmd->getId();
	// 		$replace['#' . $logical . '#'] = $cmd->execCmd();
	// 		$replace['#' . $logical . '_unite#'] = $cmd->getUnite();
	// 		$replace['#' . $logical . '_name#'] = $cmd->getName();
	// 		$replace['#' . $logical . '_collect#'] = $collectDate;
	// 	}
	// 	$replace['#refresh_id#'] = $this->getCmd('action', 'refresh')->getId();
	
	// 	$html = template_replace($replace, getTemplate('core', $version, 'enphasesecur_dashboard', __CLASS__));
	// 	cache::set('widgetHtml' . $_version . $this->getId(), $html, 0);
	// 	return $html;
	// }

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
  	public function execute($_options = array()) {
	  	
  	}
}
