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

  
	public static $_widgetPossibility = array(
		'custom' => false,
		//'custom::layout' => false,
		'parameters' => array(),
	);

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
				$cmd = "$pip3 list | grep $list";
				unset($output);
				exec($cmd, $output, $return_var);
				if ($return_var || $output[0] == "") {
				  $return['state'] = 'nok';
				  log::add('enphasesecur', 'debug', 'Pakg nok: ' . $list);
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
		$dateRun = new DateTime();
		foreach (self::byType('enphasesecur', true) as $eqLogic) {
			$autorefresh = $eqLogic->getConfiguration('autorefresh');
			if ($eqLogic->getIsEnable() == 1){
				if ($autorefresh == '') {
					$autorefresh = '*/15 * * * *';
				}
				try {
					$c = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
					if ($c->isDue($dateRun)) {
						try {
							$eqLogic->refresh();
						} catch (Exception $exc) {
							log::add('enphasesecur', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
						}
					}
				} 
				catch (Exception $exc) {
					log::add('enphasesecur', 'error', __('Expression cron non valide pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $autorefresh);
				}
			}
		}
	}
/*
	public static function cronDaily() {
		foreach (self::byType('enphasesecur', true) as $eqLogic) {
			try {
				sleep(rand(0,15));
				$eqlogic->commandes("refresh"); 
			} catch (Exception $exc) {
				log::add('enphasesecur', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
			}
		}
	}
*/

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

	  	$enphasesecurCmd = $this->getCmd(null, 'wattHoursToday');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Production sur une heure', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('wattHoursToday');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('w');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
		$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'wattHoursSevenDays');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Production sur 1 semaine', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('wattHoursSevenDays');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('w');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
		$enphasesecurCmd->save();
		
		$enphasesecurCmd = $this->getCmd(null, 'wattHoursLifetime');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Production depuis la mise en service', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('wattHoursLifetime');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('w');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
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

		$enphasesecur_fichier = $enphasesecur_path .'/../../data/'. $enphasesecur_serie. '.json';

		$enphasesecur_cmd = 'python3 ' . $enphasesecur_path .'/../../resources/enphase.py';
		$enphasesecur_cmd .= $enphasesecure_ip . ' ' . $enphasesecur_user . ' ' . $enphasesecur_pass . ' ' . $enphasesecur_site . ' ' . $enphasesecur_serie . ' ' . $enphasesecur_fichier;
		log::add('enphasesecur', 'debug', 'commande ' . $enphasesecur_cmd);
		exec($enphasesecur_cmd . ' >> ' . log::getPathToLog('enphasesecur') . ' 2>&1 &');
		sleep(2);
		$enphasesecur_json = json_decode(file_get_contents($enphasesecur_fichier), true);

		$enphasesecur_info = $enphasesecur_json['wattHoursToday'];
		log::add('enphasesecur', 'debug', 'Production du jour: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('wattHoursToday', $enphasesecur_info);	

		$enphasesecur_info = $enphasesecur_json['wattHoursSevenDays '];
		log::add('enphasesecur ', 'debug', 'Production de la semaine: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('wattHoursSevenDays ', $enphasesecur_info);	

		$enphasesecur_info = $enphasesecur_json['wattsNow '];
		log::add('enphasesecur', 'debug', 'Production instantannée: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('wattsNow ', $enphasesecur_info);	

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
