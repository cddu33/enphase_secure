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
	}

	public function encrypt() {
		$this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
		$this->setConfiguration('serie', utils::encrypt($this->getConfiguration('serie')));
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
			$deps = array('bs4', 'PyJWT', 'asyncio', 'httpx', 'lxml', 'html5lib', 'html.parser');
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

	public static function dependancy_install() {
		log::remove(__CLASS__ . '_update');
		passthru('/bin/bash ' . dirname(__FILE__) . '/../../resources/install_apt.sh ' . jeedom::getTmpFolder(__CLASS__) . '/dependency > ' . log::getPathToLog(__CLASS__ . '_update') . ' 2>&1 &');
	}

	public static function dependancy_install_update() {
		log::remove(__CLASS__ . '_update');
	}


  /*     * ***********************Methode static*************************** */
  /*
  * Fonction exécutée automatiquement toutes les minutes par Jeedom  */
 
  	public static function cron() {
		foreach (self::byType('enphasesecur', true) as $eqLogic) {
			if ($eqLogic->getIsEnable() == 1){
				$eqLogic->refresh();
			}			
		}
	}

  /*     * *********************Méthodes d'instance************************* */

  // Fonction exécutée automatiquement avant la création de l'équipement
  	public function preInsert() {
  	}

  // Fonction exécutée automatiquement après la création de l'équipement
  	public function postInsert() {
  	}

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  	public function preUpdate() {
	 	if ($this->getConfiguration('user') == '') {
			throw new Exception('L\'identifiant ne peut pas être vide');
	 	}
	 	if ($this->getConfiguration('password') == '') {
			throw new Exception('Le mot de passe ne peut etre vide');
	 	}
		 if ($this->getConfiguration('ip') == '') {
			throw new Exception('L\'adresse IP ne peu pas être vide');
	 	}
		 if ($this->getConfiguration('serie') == '') {
			throw new Exception('Le numéro de série de la passerelle ne peu pas être vide');
	 	}
		 if ($this->getConfiguration('site') == '') {
			throw new Exception('Le numéro de site ne peu pas être vide');
	 	}
  	}
  	
  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  	public function postUpdate() {
  	}

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  	public function preSave() {
  	}

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
	public function postSave() {
		$enphasesecurCmd = $this->getCmd(null, 'refresh');
		if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Rafraichir', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('refresh');
	  	$enphasesecurCmd->setType('action');
	  	$enphasesecurCmd->setSubType('other');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'PwattHoursToday');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Prod Jour', __FILE__));
			
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setConfiguration('historizeRound', '2');
			$enphasesecurCmd->setGeneric_type('POWER'); 
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('PwattHoursToday');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('Wh');
		$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'PwattHoursSevenDays');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Prod Semaine', __FILE__));
			
			$enphasesecurCmd->setConfiguration('historizeRound', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setGeneric_type('POWER');
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('PwattHoursSevenDays');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('Wh');
		$enphasesecurCmd->save();
		
		$enphasesecurCmd = $this->getCmd(null, 'PwattHoursLifetime');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Prod MES', __FILE__));
			
			$enphasesecurCmd->setConfiguration('historizeRound', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('0');
			$enphasesecurCmd->setGeneric_type('POWER');
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('PwattHoursLifetime');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('Wh');
		$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'PwattsNow');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Prod Inst', __FILE__));
			//
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
			$enphasesecurCmd->setGeneric_type('POWER');
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('PwattsNow');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('W');
		$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'CwattHoursToday');
		if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Conso Total Jour', __FILE__));
			
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setConfiguration('historizeRound', '2'); 
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattHoursToday');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('Wh');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'CwattHoursSevenDays');
		if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Conso Total Semaine', __FILE__));
			
			$enphasesecurCmd->setConfiguration('historizeRound', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattHoursSevenDays');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('Wh');
	  	$enphasesecurCmd->save();
	  
	  	$enphasesecurCmd = $this->getCmd(null, 'CwattHoursLifetime');
		if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Conso Total MES', __FILE__));
			
			$enphasesecurCmd->setConfiguration('historizeRound', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('0');
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattHoursLifetime');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('Wh');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'CwattsNow');
		if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Conso Total Inst', __FILE__));
			
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setConfiguration('historizeRound', '3');
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattsNow');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('W');
	  	$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'tension');
		if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Tension', __FILE__));
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			
			$enphasesecurCmd->setConfiguration('historizeRound', '0'); 
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setGeneric_type('VOLTAGE');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('tension');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('V');
	  	$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'CwattHoursTodayNet');
		if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Conso Net Jour', __FILE__));
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setConfiguration('historizeRound', '2'); 
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattHoursTodayNet');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('Wh');
		$enphasesecurCmd->save();
  
		$enphasesecurCmd = $this->getCmd(null, 'CwattHoursSevenDaysNet');
		if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Conso Net Semaine', __FILE__));
			$enphasesecurCmd->setConfiguration('historizeRound', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattHoursSevenDaysNet');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('Wh');
		$enphasesecurCmd->save();
		
		$enphasesecurCmd = $this->getCmd(null, 'CwattHoursLifetimeNet');
		if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Conso Net MES', __FILE__));
			$enphasesecurCmd->setConfiguration('historizeRound', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('0');
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattHoursLifetimeNet');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('Wh');
		$enphasesecurCmd->save();
  
		$enphasesecurCmd = $this->getCmd(null, 'CwattsNowNet');
		if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Conso Net Inst', __FILE__));
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setConfiguration('historizeRound', '3');
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattsNowNet');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('W');
		$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'Export');
		if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Export Réseau', __FILE__));
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setConfiguration('historizeRound', '3');
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('Export');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('W');
		$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'Import');
		if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Import Réseau', __FILE__));
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setConfiguration('historizeRound', '3');
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('Import');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('W');
		$enphasesecurCmd->save();
  	}

	  
	  
  // Fonction exécutée automatiquement avant la suppression de l'équipement
  	public function preRemove() {
  	}

  // Fonction exécutée automatiquement après la suppression de l'équipement
  	public function postRemove() {
  	}


 	public function refresh() {
		$enphasesecur_path = realpath(dirname(__FILE__));

		$enphasesecur_pass = $this->getConfiguration('password');
		$enphasesecur_user = $this->getConfiguration('user');
		$enphasesecur_serie = $this->getConfiguration('serie');
		$enphasesecur_site = $this->getConfiguration('site');
		$enphasesecur_ip = $this->getConfiguration('ip');

		$enphasesecur_fichier = $enphasesecur_path .'/../../data/'. $enphasesecur_serie . '.json';
		$enphasesecur_cmd = 'python3 ' . $enphasesecur_path .'/../../resources/enphase.py';
		$enphasesecur_cmd .=' ' .  $enphasesecur_ip . ' ' . $enphasesecur_user . ' ' . $enphasesecur_pass . ' ' . $enphasesecur_site . ' ' . $enphasesecur_serie . ' ' . $enphasesecur_fichier;
		log::add('enphasesecur', 'debug', 'commande ' . $enphasesecur_cmd);
		exec($enphasesecur_cmd . ' >> ' . log::getPathToLog('enphasesecur') . ' 2>&1 &');
		sleep(5);
		$enphasesecur_json = json_decode(file_get_contents($enphasesecur_fichier), true);
		/*
		foreach ($enphasesecur_json as $key1=> $data1) {
			log::add('enphasesecur', 'debug', $key1 . ' : ' . $data1);
			foreach ($data1 as $key2 => $data2) {
				log::add('enphasesecur', 'debug', 'Enfants1: ' . $key2 . ' : ' . $data2);
				foreach ($data2 as $key3 => $data3) {
					log::add('enphasesecur', 'debug', 'Enfants2: ' . $key3 . ' : ' . $data3);
				}
			}
		}
*/

		if ($enphasesecur_json['production']['1']['whLifetime'] != "" && $enphasesecur_json['production']['1']['whLifetime'] != null)   {

			$enphasesecur_info = $enphasesecur_json['production']['1']['whLifetime'];
			log::add('enphasesecur', 'debug', 'Production depuis la mise en service: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('PwattHoursLifetime', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['production']['1']['whToday'];
			log::add('enphasesecur', 'debug', 'Production totale du jour: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('PwattHoursToday', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['production']['1']['whLastSevenDays'];
			log::add('enphasesecur', 'debug', 'Production totale de la semaine: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('PwattHoursSevenDays', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['production']['1']['wNow'];
			log::add('enphasesecur', 'debug', 'Production instantannée: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('PwattsNow', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['consumption']['0']['whLifetime'];
			log::add('enphasesecur', 'debug', 'Consommation totale depuis la mise en service: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('CwattHoursLifetime', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['consumption']['0']['whToday'];
		
			log::add('enphasesecur', 'debug', 'Consommation totale du jour: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('CwattHoursToday', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['consumption']['0']['whLastSevenDays'];
			log::add('enphasesecur', 'debug', 'Consommation Net de la semaine: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('CwattHoursSevenDays', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['consumption']['0']['wNow'];
			log::add('enphasesecur', 'debug', 'Consommation totale instantannée: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('CwattsNow', $enphasesecur_info);	
		
			$enphasesecur_info = $enphasesecur_json['consumption']['0']['rmsVoltage'];
			log::add('enphasesecur', 'debug', 'Tension réseau: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('tension', $enphasesecur_info);

			
			$enphasesecur_info = $enphasesecur_json['consumption']['1']['whLifetime'];
			log::add('enphasesecur', 'debug', 'Consommation Net depuis la mise en service: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('CwattHoursLifetimeNet', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['consumption']['1']['whToday'];
			if ($enphasesecur_info == 0){
				//merci Bison
				$enphasesecur_info = $enphasesecur_json['consumption'][0]['whToday']-$enphasesecur_json['production'][1]['whToday'];
			}
			log::add('enphasesecur', 'debug', 'Consommation Net du jour: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('CwattHoursTodayNet', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['consumption']['1']['whLastSevenDays'];
			if ($enphasesecur_info == 0){
				//merci Bison
				$enphasesecur_info = $enphasesecur_json['consumption'][0]['whLastSevenDays']-$enphasesecur_json['production'][1]['whLastSevenDays'];
			}
			log::add('enphasesecur', 'debug', 'Consommation Net de la semaine: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('CwattHoursSevenDaysNet', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['consumption']['1']['wNow'];
			log::add('enphasesecur', 'debug', 'Consommation Net instantannée: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('CwattsNowNet', $enphasesecur_info);	

			if ($enphasesecur_info<0) {
				$this->checkAndUpdateCmd('Export', ($enphasesecur_info*(-1)));	
				$this->checkAndUpdateCmd('Import', 0);
			}
			else {
				$this->checkAndUpdateCmd('Import', ($enphasesecur_info));
				$this->checkAndUpdateCmd('Export', 0);
			}
		}
		else {
			log::add('enphasesecur', 'debug', 'Envoy-S-Standard-EU');
			
			$enphasesecur_info = $enphasesecur_json['production']['0']['whLifetime'];
			log::add('enphasesecur', 'debug', 'Production depuis la mise en service: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('PwattHoursLifetime', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['production']['0']['wNow'];
			log::add('enphasesecur', 'debug', 'Production instantannée: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('PwattsNow', $enphasesecur_info);	
		}

  	}

	  public function toHtml($_version = 'dashboard') {
		
	
		$replace = $this->preToHtml($_version);
		if (!is_array($replace)) {
		  return $replace;
		}
		$version = jeedom::versionAlias($_version);
	
		foreach (($this->getCmd('info')) as $cmd) {
		  $logical = $cmd->getLogicalId();
		  $collectDate = $cmd->getCollectDate();
		
		  $replace['#' . $logical . '_id#'] = $cmd->getId();
		  $replace['#' . $logical . '#'] = $cmd->execCmd();
		  $replace['#' . $logical . '_unite#'] = $cmd->getUnite();
		  $replace['#' . $logical . '_collect#'] = $collectDate;
		}
		$replace['#refresh_id#'] = $this->getCmd('action', 'refresh')->getId();
	
		$html = template_replace($replace, getTemplate('core', $version, 'enphasesecur_dashboard', __CLASS__));
		cache::set('widgetHtml' . $_version . $this->getId(), $html, 0);
		return $html;
	  }
}

class enphasesecurCmd extends cmd {

  // Exécution d'une commande
  	public function execute($_options = array()) {
	  	$eqlogic = $this->getEqLogic();
		try {
			$eqlogic->refresh();
		} catch (Exception $exc) {
			log::add('enphasesecur', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
		}
  	}
}
?>
