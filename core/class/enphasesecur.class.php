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

  /*
	public static $_widgetPossibility = array(
		'custom' => false,
		//'custom::layout' => false,
		'parameters' => array(),
	);*/

	public function decrypt() {
		$this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
		//$this->setConfiguration('vin', utils::decrypt($this->getConfiguration('vin')));
	}

	public function encrypt() {
		$this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
		//$this->setConfiguration('vin', utils::encrypt($this->getConfiguration('vin')));
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
		//return array('script' => dirname(__FILE__) . '/../../resources/install_apt.sh ' . jeedom::getTmpFolder(__CLASS__) . '/dependency', 'log' => log::getPathToLog(__CLASS__ . '_update'));
		    passthru('/bin/bash ' . dirname(__FILE__) . '/../../resources/install_apt.sh ' . jeedom::getTmpFolder(__CLASS__) . '/dependency > ' . log::getPathToLog(__CLASS__ . '_update') . ' 2>&1 &');
	}

	public static function dependancy_install_update() {
		log::remove(__CLASS__ . '_update');
		//return array('script' => dirname(__FILE__) . '/../../resources/install_apt.sh ' . jeedom::getTmpFolder(__CLASS__) . '/dependency', 'log' => log::getPathToLog(__CLASS__ . '_update'));
		  //  passthru('/bin/bash ' . dirname(__FILE__) . '/../../resources/install_apt_update.sh ' . jeedom::getTmpFolder(__CLASS__) . '/dependency > ' . log::getPathToLog(__CLASS__ . '_update') . ' 2>&1 &');
	}


  /*     * ***********************Methode static*************************** */
  /*
  * Fonction exécutée automatiquement toutes les minutes par Jeedom  */
 
  	public static function cron() {
		log::add('enphasesecur', 'debug', 'cron lancé');
		$dateRun = new DateTime();
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
		  	$enphasesecurCmd->setName(__('Production du jour', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('PwattHoursToday');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('kW');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
		$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
		$enphasesecurCmd->setTemplate('mobile', 'core::badge');
		$enphasesecurCmd->setIsHistorized('1');
		$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'PwattHoursSevenDays');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Production de la semaine', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('PwattHoursSevenDays');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('kW');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
		$enphasesecurCmd->setConfiguration('historizeRound', '0');
		$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
		$enphasesecurCmd->setTemplate('mobile', 'core::badge');
		$enphasesecurCmd->setIsHistorized('1');
		$enphasesecurCmd->save();
		
		$enphasesecurCmd = $this->getCmd(null, 'PwattHoursLifetime');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Production depuis la mise en service', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('PwattHoursLifetime');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('kW');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
		$enphasesecurCmd->setConfiguration('historizeRound', '0');
		$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
		$enphasesecurCmd->setTemplate('mobile', 'core::badge');
		$enphasesecurCmd->setIsHistorized('1');
		$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'PwattsNow');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Production instantannée', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('PwattsNow');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('kW');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
		$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
		$enphasesecurCmd->setTemplate('mobile', 'core::badge');
		$enphasesecurCmd->setIsHistorized('1');
		$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'CwattHoursToday');
		if (!is_object($enphasesecurCmd)) {
		  $enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Consommation du jour', __FILE__));
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattHoursToday');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('kW');
		$enphasesecurCmd->setConfiguration('minValue', '0');
		$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
		$enphasesecurCmd->setTemplate('mobile', 'core::badge');
		$enphasesecurCmd->setIsHistorized('1');
	  $enphasesecurCmd->save();

	  $enphasesecurCmd = $this->getCmd(null, 'CwattHoursSevenDays');
		if (!is_object($enphasesecurCmd)) {
		  $enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Consommation de la semaine', __FILE__));
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattHoursSevenDays');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('kW');
		$enphasesecurCmd->setConfiguration('minValue', '0');
		$enphasesecurCmd->setConfiguration('historizeRound', '0');
		$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
		$enphasesecurCmd->setTemplate('mobile', 'core::badge');
		$enphasesecurCmd->setIsHistorized('1');
	  $enphasesecurCmd->save();
	  
	  $enphasesecurCmd = $this->getCmd(null, 'CwattHoursLifetime');
		if (!is_object($enphasesecurCmd)) {
		  $enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Consommation depuis la mise en service', __FILE__));
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattHoursLifetime');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('kW');
		$enphasesecurCmd->setConfiguration('minValue', '0');
		$enphasesecurCmd->setConfiguration('historizeRound', '0');
		$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
		$enphasesecurCmd->setTemplate('mobile', 'core::badge');
		$enphasesecurCmd->setIsHistorized('1');
	  	$enphasesecurCmd->save();

	  $enphasesecurCmd = $this->getCmd(null, 'CwattsNow');
		if (!is_object($enphasesecurCmd)) {
		  $enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Consommation instantannée', __FILE__));
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattsNow');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('kW');
		$enphasesecurCmd->setConfiguration('minValue', '0');
		$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
		$enphasesecurCmd->setTemplate('mobile', 'core::badge');
		$enphasesecurCmd->setIsHistorized('1');

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
		foreach ($enphasesecur_json as $key=> $data1) {
			log::add('enphasesecur', 'debug', $key . " : " . $data1);
		}
/*
		$enphasesecur_info = $enphasesecur_json['wattHoursLifetime']/1000;
		log::add('enphasesecur', 'debug', 'Production depuis la mise en service: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('PwattHoursLifetime', $enphasesecur_info);	

		$enphasesecur_info = $enphasesecur_json['wattHoursToday']/1000;
		log::add('enphasesecur', 'debug', 'Production du jour: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('PwattHoursToday', $enphasesecur_info);	

		$enphasesecur_info = $enphasesecur_json['wattHoursSevenDays']/1000;
		log::add('enphasesecur', 'debug', 'Production de la semaine: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('PwattHoursSevenDays', $enphasesecur_info);	
*/
		$enphasesecur_info = $enphasesecur_json['production'];
		log::add('enphasesecur', 'debug', 'Production instantannée: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('PwattsNow', $enphasesecur_info);	
/*
		$enphasesecur_info = $enphasesecur_json['wattHoursLifetime']/1000;
		log::add('enphasesecur', 'debug', 'Consommation depuis la mise en service: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('CwattHoursLifetime', $enphasesecur_info);	

		$enphasesecur_info = $enphasesecur_json['wattHoursToday']/1000;
		log::add('enphasesecur', 'debug', 'Consommation du jour: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('CwattHoursToday', $enphasesecur_info);	

		$enphasesecur_info = $enphasesecur_json['wattHoursSevenDays']/1000;
		log::add('enphasesecur', 'debug', 'Consommation de la semaine: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('CwattHoursSevenDays', $enphasesecur_info);	

		$enphasesecur_info = $enphasesecur_json['wattsNow']/1000;
		log::add('enphasesecur', 'debug', 'Consommation instantannée: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('CwattsNow', $enphasesecur_info);	
		*/
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
