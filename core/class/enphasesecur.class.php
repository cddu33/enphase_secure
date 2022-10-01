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
		'custom' => true,
		//'custom::layout' => false,
		'parameters' => array(),
	);

	public function decrypt() {
		$this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
		$this->setConfiguration('vin', utils::decrypt($this->getConfiguration('vin')));
	}

	public function encrypt() {
		$this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
		$this->setConfiguration('vin', utils::encrypt($this->getConfiguration('vin')));
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
			$return['state'] = 'nok';
			$output = array();
			$cmd = "pip3 list | grep wheel";
			unset($output);
			exec($cmd, $output, $return_var);
        
			if ($return_var || $output[0] == "") {
				$return['state'] = 'nok';	
			}
			else { 
				$return['state'] = 'ok'; 
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
							sleep(rand(0,15));
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
	 	if ($this->getConfiguration('vin') == '') {
			throw new Exception('Le VIN d\'identification du véhicule ne peut pas être vide');
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

	  	$enphasesecurCmd = $this->getCmd(null, 'frefresh');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Forcer le rafraichissement des données', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('frefresh');
	  	$enphasesecurCmd->setType('action');
	  	$enphasesecurCmd->setSubType('other');
	  	$enphasesecurCmd->save();
  
	  	$enphasesecurCmd = $this->getCmd(null, 'lock');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Vérouiller', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('lock');
	  	$enphasesecurCmd->setType('action');
	  	$enphasesecurCmd->setSubType('other');
	  	$enphasesecurCmd->save();
	  
	  	$enphasesecurCmd = $this->getCmd(null, 'unlock');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Dévérouiller', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('unlock');
	  	$enphasesecurCmd->setType('action');
	  	$enphasesecurCmd->setSubType('other');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'start');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Démarrer le moteur', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('start');
	  	$enphasesecurCmd->setType('action');
	  	$enphasesecurCmd->setSubType('other');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'stop');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Couper le moteur', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('stop');
	  	$enphasesecurCmd->setType('action');
	  	$enphasesecurCmd->setSubType('other');
	  	$enphasesecurCmd->save();
	  
	  	$enphasesecurCmd = $this->getCmd(null, 'etat');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Etat', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('etat');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

		  $enphasesecurCmd = $this->getCmd(null, 'vehicle_type');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Motorisation', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('vehicle_type');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'last');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Dernière actualisation', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('last');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'maj');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Mise à jour en cours', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('maj');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('binary');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'veille');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Veille profonde', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('veille');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('binary');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'km');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Kilométrage', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('km');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('km');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
		$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'lat');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Latitude', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('lat');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('°');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'long');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Longitude', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('long');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('°');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'hbat');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Etat batterie', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('hbat');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();
	  
	   	$enphasesecurCmd = $this->getCmd(null, 'latlong');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Latitude-Longitude', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('latlong');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'tbat');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Tension batterie', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('tbat');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('V');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
	  	$enphasesecurCmd->setConfiguration('maxValue', '20');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'ehuile');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Etat huile', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('ehuile');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'huile');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Pourcentage huile', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('huile');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('%');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
	  	$enphasesecurCmd->setConfiguration('maxValue', '100');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'pression');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Etat pression', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('pression');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'etpnavgh');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Etat pneu avant gauche', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('etpnavgh');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'etpnavdr');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Etat pneu avant droit', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('etpnavdr');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'etpnargh');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Etat pneu arrière gauche', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('etpnargh');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'etpnardr');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Etat pneu arrière droit', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('etpnardr');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
		$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'prpnavgh');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Pression pneu avant gauche', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('prpnavgh');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('bar');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
	  	$enphasesecurCmd->setConfiguration('maxValue', '400');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'prpnavdr');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Pression pneu avant droit', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('prpnavdr');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('bar');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
	  	$enphasesecurCmd->setConfiguration('maxValue', '400');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'prpnargh');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Pression pneu arrière gauche', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('prpnargh');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('bar');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
	  	$enphasesecurCmd->setConfiguration('maxValue', '400');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'prpnardr');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Pression pneu arrière droit', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('prpnardr');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('bar');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
	  	$enphasesecurCmd->setConfiguration('maxValue', '400');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'vicdav');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Vitre conducteur avant', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('vicdav');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'vicdar');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Vitre conducteur arrière', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('vicdar');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'vipsav');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Vitre passager avant', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('vipsav');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'vipsar');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Vitre passager arrière', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('vipsar');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'doorcd');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Porte conducteur', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('doorcd');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'doorps');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Porte passager', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('doorps');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'doorleft');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Porte arrière gauche', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('doorleft');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'doorright');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Porte arrière droite', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('doorright');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'hood');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Capot', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('hood');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	   	$enphasesecurCmd = $this->getCmd(null, 'tailgate');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Coffre Hayon', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('tailgate');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'innertailgate');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Coffre intérieur', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('innertailgate');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'elVehDTE');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Estimation kilométrage restant électrique', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('elVehDTE');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('km');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
	  	$enphasesecurCmd->setConfiguration('maxValue', '1000');
	  	$enphasesecurCmd->setConfiguration('historizeRound', '0');
		$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'batteryFillLevel');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Charge batterie', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('batteryFillLevel');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('%');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
	  	$enphasesecurCmd->setConfiguration('maxValue', '110');
	  	$enphasesecurCmd->save();
	  
		$enphasesecurCmd = $this->getCmd(null, 'qfuel');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Réservoir carburant restant', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('qfuel');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('%');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
	  	$enphasesecurCmd->setConfiguration('maxValue', '150');
	  	$enphasesecurCmd->setConfiguration('historizeRound', '0');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'kmfuel');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Estimation kilométrage restant', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('kmfuel');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('km');
	  	$enphasesecurCmd->setConfiguration('minValue', '0');
	  	$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'connectorStatus');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Véhicule branché', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('connectorStatus');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('binary');
	  	$enphasesecurCmd->save();
		
		$enphasesecurCmd = $this->getCmd(null, 'chargingStatus');
	  	if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Etat charge', __FILE__));
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('chargingStatus');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('string');
	  	$enphasesecurCmd->save();


  	}

	  
  // Fonction exécutée automatiquement avant la suppression de l'équipement
  	public function preRemove() {
  	}

  // Fonction exécutée automatiquement après la suppression de l'équipement
  	public function postRemove() {
  	}

  /* Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin */
    public function toHtml($_version = 'dashboard') {
	/*
		if ($this->getConfiguration('widget_template') != 1) {
			return parent::toHtml($_version);
		}*/
		if ($this->getConfiguration('eqtuile','') == "core"){
          	self::$_widgetPossibility = array('custom' => 'layout');
          	return eqLogic::toHtml($_version);
        }
		
		$replace = $this->preToHtml($_version);
		if (!is_array($replace)) {
			return $replace;
		}
		$version = jeedom::versionAlias($_version);
		$replace['#version#'] = $_version;
		
		$replace['#vehicle_vin'.$this->getId().'#'] = $this->getConfiguration('vin');
		$replace['#vehicle_type#'] = $this->getCmd(null, 'vehicle_type'); 
							

		$this->emptyCacheWidget(); 		//vide le cache. Pratique pour le développement

		// Traitement des commandes infos
		foreach ($this->getCmd('info') as $cmd) {
			$replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
			$replace['#' . $cmd->getLogicalId() . '_name#'] = $cmd->getName();
			$replace['#' . $cmd->getLogicalId() . '#'] = $cmd->execCmd();
			$replace['#' . $cmd->getLogicalId() . '_visible#'] = $cmd->getIsVisible();
			if ($cmd->getIsHistorized() == 1) {
				$replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
			}
			$replace['#' . $cmd->getLogicalId() . '_collect#'] = $cmd->getCollectDate();
		}

		// Traitement des commandes actions
		foreach ($this->getCmd('action') as $cmd) {
			$replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
			$replace['#' . $cmd->getLogicalId() . '_visible#'] = $cmd->getIsVisible();
			if ($cmd->getSubType() == 'select') {
				$listValue = "<option value>" . $cmd->getName() . "</option>";
				$listValueArray = explode(';', $cmd->getConfiguration('listValue'));
				foreach ($listValueArray as $value) {
					list($id, $name) = explode('|', $value);
					$listValue = $listValue . "<option value=" . $id . ">" . $name . "</option>";
				}
				$replace['#' . $cmd->getLogicalId() . '_listValue#'] = $listValue;
			}
		}
			
		// On definit le template à appliquer par rapport à la version Jeedom utilisée
		$template = 'enphasesecur_dashboard';
		$replace['#template#'] = $template;

		return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, $template, 'enphasesecur')));
	}

  // Permet de crypter/décrypter automatiquement des champs de configuration des équipements
  	

 	public function refresh() {
		$enphasesecur_path = realpath(dirname(__FILE__));

		$enphasesecur_pass = $this->getConfiguration('password');
		$enphasesecur_vin = $this->getConfiguration('vin');
		$enphasesecur_user = $this->getConfiguration('user');
		$enphasesecur_fichier = $enphasesecur_path .'/../../data/'. $enphasesecur_vin . '.json';
		$enphasesecur_cmd = 'python3 ' . $enphasesecur_path .'/../../resources/enphasesecur.py';
		$enphasesecur_cmd .= ' ' . $enphasesecur_user . ' ' . $enphasesecur_pass . ' ' . $enphasesecur_vin .' ' . 'statut' . ' ' . $enphasesecur_fichier;
		log::add('enphasesecur', 'debug', 'commande ' . $enphasesecur_cmd);
		exec($enphasesecur_cmd . ' >> ' . log::getPathToLog('enphasesecur') . ' 2>&1 &');
		sleep(5);
		$enphasesecur_json = json_decode(file_get_contents($enphasesecur_fichier), true);
		
		if ($enphasesecur_json['elVehDTE'] == "") {
			log::add('enphasesecur', 'debug', 'Type véhicule: Thermique');
			$this->checkAndUpdateCmd('vehicle_type', 'thermique');

			$enphasesecur_info = $enphasesecur_json['fuel']['fuelLevel'];
			log::add('enphasesecur', 'debug', 'Pourcentage restant réservoir: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('qfuel', $enphasesecur_info);
			$enphasesecur_info = $enphasesecur_json['fuel']['distanceToEmpty'];
			log::add('enphasesecur', 'debug', 'Estimation kilométrage restant: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('kmfuel', $enphasesecur_info);	
		}
		elseif ($enphasesecur_json['fuel'] == "") {
			log::add('enphasesecur', 'debug', 'Type véhicule: Electrique');
			$this->checkAndUpdateCmd('vehicle_type', 'electric');

			$enphasesecur_info = $enphasesecur_json['elVehDTE']['value'];
			log::add('enphasesecur', 'debug', 'Estimation kilométrage restant en électrique: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('elVehDTE', $enphasesecur_info);
			$enphasesecur_info = $enphasesecur_json['batteryFillLevel']['value'];
			log::add('enphasesecur', 'debug', 'Charge batterie: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('batteryFillLevel', $enphasesecur_info);
			$enphasesecur_info = $enphasesecur_json['plugStatus']['value'];
			log::add('enphasesecur', 'debug', 'Véhicule branché: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('connectorStatus', $enphasesecur_info);
			$enphasesecur_info = $enphasesecur_json['chargingStatus']['value'];
			log::add('enphasesecur', 'debug', 'Etat de la charge: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('chargingStatus', $enphasesecur_info);
		}
		else {
			log::add('enphasesecur', 'debug', 'Type véhicule: Hybride');
			$this->checkAndUpdateCmd('vehicle_type', 'hybride');

			$enphasesecur_info = $enphasesecur_json['elVehDTE']['value'];
			log::add('enphasesecur', 'debug', 'Estimation kilométrage restant en électrique: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('elVehDTE', $enphasesecur_info);

			$enphasesecur_info = $enphasesecur_json['batteryFillLevel']['value'];
			log::add('enphasesecur', 'debug', 'Charge batterie: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('batteryFillLevel', $enphasesecur_info);

			$enphasesecur_info = $enphasesecur_json['fuel']['fuelLevel'];
			log::add('enphasesecur', 'debug', 'Pourcentage restant réservoir: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('qfuel', $enphasesecur_info);

			$enphasesecur_info = $enphasesecur_json['fuel']['distanceToEmpty'];
			log::add('enphasesecur', 'debug', 'Estimation kilométrage restant: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('kmfuel', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['plugStatus']['value'];
			log::add('enphasesecur', 'debug', 'Véhicule branché: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('connectorStatus', $enphasesecur_info);

		}

		$enphasesecur_info = $enphasesecur_json['lockStatus']['value'];
		log::add('enphasesecur', 'debug', 'etat lock: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('etat', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['lastRefresh'];
		log::add('enphasesecur', 'debug', 'dernière actualisation: ' . $enphasesecur_info . ' UTC');
		$this->checkAndUpdateCmd('last', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['firmwareUpgInProgress']['value'];
		log::add('enphasesecur', 'debug', 'Mise à jour en cours: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('maj', $enphasesecur_info);
		$enphasesecur_info = $enphasesecur_json['deepSleepInProgress']['value'];
		log::add('enphasesecur', 'debug', 'Veille profonde: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('veille', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['odometer']['value'];
		log::add('enphasesecur', 'debug', 'Kilométrage: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('km', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['gps']['latitude'];
		log::add('enphasesecur', 'debug', 'Latitude: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('lat', $enphasesecur_info);
		$enphasesecur_info = $enphasesecur_json['gps']['longitude'];
		log::add('enphasesecur', 'debug', 'Longitude: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('long', $enphasesecur_info);
		$this->checkAndUpdateCmd('latlong', $enphasesecur_json['gps']['latitude'] . ',' . $enphasesecur_json['gps']['longitude']);


		$enphasesecur_info = $enphasesecur_json['battery']['batteryHealth']['value'];
		log::add('enphasesecur', 'debug', 'Etat batterie: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('hbat', $enphasesecur_info);
		$enphasesecur_info = $enphasesecur_json['battery']['batteryStatusActual']['value'];
		log::add('enphasesecur', 'debug', 'Tension batterie: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('tbat', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['oil']['oilLife'];
		log::add('enphasesecur', 'debug', 'Etat huile: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('ehuile', $enphasesecur_info);
		$enphasesecur_info = $enphasesecur_json['oil']['oilLifeActual'];
		log::add('enphasesecur', 'debug', 'Pourcentage huile: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('huile', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['tirePressure']['value'];
		log::add('enphasesecur', 'debug', 'Etat pression: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('pression', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['TPMS']['leftFrontTireStatus']['value'];
		log::add('enphasesecur', 'debug', 'Etat pneu avant gauche: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('etpnavgh', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['TPMS']['rightFrontTireStatus']['value'];
		log::add('enphasesecur', 'debug', 'Etat pneu avant droit: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('etpnavdr', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['TPMS']['outerLeftRearTireStatus']['value'];
		log::add('enphasesecur', 'debug', 'Etat pneu arrière gauche: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('etpnargh', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['TPMS']['outerRightRearTireStatus']['value'];
		log::add('enphasesecur', 'debug', 'Etat pneu arrière droit: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('etpnardr', $enphasesecur_info);

		$enphasesecur_info = ($enphasesecur_json['TPMS']['leftFrontTirePressure']['value'])/100;
		log::add('enphasesecur', 'debug', 'Pression pneu avant gauche: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('prpnavgh', $enphasesecur_info);

		$enphasesecur_info = ($enphasesecur_json['TPMS']['rightFrontTirePressure']['value'])/100;
		log::add('enphasesecur', 'debug', 'Pression pneu avant droit: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('prpnavdr', $enphasesecur_info);

		$enphasesecur_info = ($enphasesecur_json['TPMS']['outerLeftRearTirePressure']['value'])/100;
		log::add('enphasesecur', 'debug', 'Pression pneu arrière gauche: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('prpnargh', $enphasesecur_info);

		$enphasesecur_info = ($enphasesecur_json['TPMS']['outerRightRearTirePressure']['value'])/100;
		log::add('enphasesecur', 'debug', 'Pression pneu arrière droit: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('prpnardr', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['windowPosition']['driverWindowPosition']['value'];
		log::add('enphasesecur', 'debug', 'Fenetre conducteur avant: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('vicdav', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['windowPosition']['rearDriverWindowPos']['value'];
		log::add('enphasesecur', 'debug', 'Fenetre conducteur arrière: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('vicdar', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['windowPosition']['passWindowPosition']['value'];
		log::add('enphasesecur', 'debug', 'Fenetre passager avant: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('vipsav', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['windowPosition']['rearPassWindowPos']['value'];
		log::add('enphasesecur', 'debug', 'Fenetre passager arrière: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('vipsar', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['doorStatus']['driverDoor']['value'];
		log::add('enphasesecur', 'debug', 'Porte conducteur: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('doorcd', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['doorStatus']['passengerDoor']['value'];
		log::add('enphasesecur', 'debug', 'Porte passager: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('doorps', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['doorStatus']['rightRearDoor']['value'];
		log::add('enphasesecur', 'debug', 'Porte arrière droite: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('doorright', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['doorStatus']['leftRearDoor']['value'];
		log::add('enphasesecur', 'debug', 'Porte arrière gauche: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('doorleft', $enphasesecur_info);
		
		$enphasesecur_info = $enphasesecur_json['doorStatus']['hoodDoor']['value'];
		log::add('enphasesecur', 'debug', 'Capot: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('hood', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['doorStatus']['tailgateDoor']['value'];
		log::add('enphasesecur', 'debug', 'Coffre: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('tailgate', $enphasesecur_info);

		$enphasesecur_info = $enphasesecur_json['doorStatus']['innerTailgateDoor']['value'];
		log::add('enphasesecur', 'debug', 'Coffre intérieur: ' . $enphasesecur_info);
		$this->checkAndUpdateCmd('innertailgate', $enphasesecur_info);	

  	}

 	public function commandes($enphasesecur_statut) {
		//log::add('enphasesecur', 'debug', 'test ');
		$enphasesecur_pass = $this->getConfiguration('password');
		$enphasesecur_vin = $this->getConfiguration('vin');
		$enphasesecur_user = $this->getConfiguration('user');
		$enphasesecur_cmd = 'python3 ' . realpath(dirname(__FILE__)) .'/../../resources/enphasesecur.py';
		$enphasesecur_cmd .= ' ' . $enphasesecur_user . ' ' . $enphasesecur_pass . ' ' . $enphasesecur_vin .' ' . $enphasesecur_statut ;
		log::add('enphasesecur', 'debug', 'commande ' . $enphasesecur_cmd);
		exec($enphasesecur_cmd . ' >> ' . log::getPathToLog('enphasesecur') . ' 2>&1 &');
		sleep(30);
	}
}

class enphasesecurCmd extends cmd {

  // Exécution d'une commande
  	public function execute($_options = array()) {
	  	$eqlogic = $this->getEqLogic();
		try {
	  		switch ($this->getLogicalId()) 
	  		{ 
		  		case 'lock':
		  		$eqlogic->commandes("lock"); 
		  		break;
		  		case 'unlock':
		  		$eqlogic->commandes("unlock"); 
		  		break;
		  		case 'start':
		  		$eqlogic->commandes("start"); 
		  		break;
		  		case 'stop':
		  		$eqlogic->commandes("stop"); 	  
		  		break;
		  		case 'frefresh':
		  		$eqlogic->commandes("refresh"); 
		  		break;
				case 'signal':
				$eqlogic->commandes("signal"); 
				break;
	  		}
			$eqlogic->refresh();
		} catch (Exception $exc) {
			log::add('enphasesecur', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
		}
		//$eqLogic->refreshWidget();
  	}
}
?>
