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
			$deps = array('bs4', 'PyJWT', 'asyncio', 'httpx', 'lxml', 'html5lib', 'html.parser', 'six');
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


	public static function creationmaj() {
		$numberwidget = count(self::byType('enphasesecur', false)); 
		foreach (self::byType('enphasesecur', true) as $eqLogic) {
			if (config::bykey('widget', __CLASS__) == 1){
				if ($eqLogic->getConfiguration('type') == 'net' || $eqLogic->getConfiguration('type') == 'total' || $eqLogic->getConfiguration('type') == 'bat' || $eqLogic->getConfiguration('type') == 'prod') {
					log::add('enphasesecur', 'info', 'Suppression ??quipement suite ?? changement de mode vers combin??');
					$eqLogic->remove();
				}
			}
			else {
				if ($eqLogic->getConfiguration('type') == 'combine') {
					log::add('enphasesecur', 'info', 'Suppression ??quipement suite ?? changement de mode vers divis??');
					$eqLogic->remove();
				}
			}
		}

		if (config::bykey('widget', __CLASS__) == 1){

			if (!is_object(eqLogic::byLogicalId('enphasesecur_combine', 'enphasesecur'))) {
				log::add('enphasesecur', 'debug', 'Cr??ation ??quipement combin??');
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
				log::add('enphasesecur', 'debug', 'Cr??ation ??quipement Production');
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
				log::add('enphasesecur', 'debug', 'Cr??ation ??quipement Consommation Net');
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
				log::add('enphasesecur', 'debug', 'Cr??ation ??quipement Consommation Total');
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
				log::add('enphasesecur', 'debug', 'Cr??ation ??quipement Stockage');
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



	// Fonction ex??cut??e automatiquement avant la cr??ation de l'??quipement
	public function preInsert() {
		/*if (count(self::byType('enphasesecur', true))>= 1 ){
			throw new Exception('Il ne peu y avoir qu\'une seule passerelle sur ce plugin');
		}*/
	}

	// Fonction ex??cut??e automatiquement apr??s la cr??ation de l'??quipement
	public function postInsert() {}

	// Fonction ex??cut??e automatiquement avant la mise ?? jour de l'??quipement
  	
	public function preUpdate() {
	 /*	if ($this->getConfiguration('user') == '') {
			throw new Exception('L\'identifiant ne peut pas ??tre vide');
	 	}
	 	if ($this->getConfiguration('password') == '') {
			throw new Exception('Le mot de passe ne peut etre vide');
	 	}
		 if ($this->getConfiguration('ip') == '') {
			throw new Exception('L\'adresse IP ne peu pas ??tre vide');
	 	}
		 if ($this->getConfiguration('serie') == '') {
			throw new Exception('Le num??ro de s??rie de la passerelle ne peu pas ??tre vide');
	 	}
		 if ($this->getConfiguration('site') == '') {
			throw new Exception('Le num??ro de site ne peu pas ??tre vide');
	 	}
		if ($this->getConfiguration('delais') == '') {
			throw new Exception('Le d??lais ne peu pas ??tre 0 ');
		}
		if ($this->getConfiguration('delais') < '10') {
			throw new Exception('Le d??lais ne peux pas ??tre inf??rieur ?? 10s');
		}*/
  	}
  	
	// Fonction ex??cut??e automatiquement apr??s la mise ?? jour de l'??quipement
  	public function postUpdate() {}

	// Fonction ex??cut??e automatiquement avant la sauvegarde (cr??ation ou mise ?? jour) de l'??quipement
  	public function preSave() {}

	// Fonction ex??cut??e automatiquement apr??s la sauvegarde (cr??ation ou mise ?? jour) de l'??quipement
	public function postSave() {
     
      if ($this->getConfiguration('type') == '' || $this->getConfiguration('type') == null) {
        $this->setConfiguration('type', 'combine');
        $this->save();
      }
		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'prod') {
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
		}

		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'total') {
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
		}

		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'net' || $this->getConfiguration('type') == 'total') {

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
		}
			

		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'net') {
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
				$enphasesecurCmd->setName(__('Export R??seau', __FILE__));
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
				$enphasesecurCmd->setName(__('Import R??seau', __FILE__));
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
		if ($this->getConfiguration('type') == 'combine' || $this->getConfiguration('type') == 'bat') {
			$enphasesecurCmd = $this->getCmd(null, 'batnow');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Puissance d??livr??e', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				$enphasesecurCmd->setConfiguration('historizeRound', '3');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
			}
			$enphasesecurCmd->setEqLogic_id($this->getId());
			$enphasesecurCmd->setLogicalId('batnow');
			$enphasesecurCmd->setType('info');
			$enphasesecurCmd->setSubType('numeric');
			$enphasesecurCmd->setUnite('W');
			$enphasesecurCmd->save();

			$enphasesecurCmd = $this->getCmd(null, 'batperc');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Pourcentage charge', __FILE__));
				$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
				$enphasesecurCmd->setIsHistorized('1');
				//$enphasesecurCmd->setConfiguration('historizeRound', '0');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
			}
			$enphasesecurCmd->setEqLogic_id($this->getId());
			$enphasesecurCmd->setLogicalId('batperc');
			$enphasesecurCmd->setType('info');
			$enphasesecurCmd->setSubType('numeric');
			$enphasesecurCmd->setUnite('%');
			$enphasesecurCmd->save();

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
			}
			$enphasesecurCmd->setEqLogic_id($this->getId());
			$enphasesecurCmd->setType('info');
			$enphasesecurCmd->setSubType('numeric');
			$enphasesecurCmd->setUnite('W');
			$enphasesecurCmd->save();

			$enphasesecurCmd = $this->getCmd(null, 'Watt');
			if (!is_object($enphasesecurCmd)) {
				$enphasesecurCmd = new enphasesecurCmd();
				$enphasesecurCmd->setName(__('Puissance', __FILE__));
				$enphasesecurCmd->setIsVisible(true);
				$enphasesecurCmd->setIsHistorized(true);
				$enphasesecurCmd->setLogicalId('Watt');
				$enphasesecurCmd->setGeneric_type('CONSUMPTION');
			}
			$enphasesecurCmd->setEqLogic_id($this->getId());
			$enphasesecurCmd->setType('info');
			$enphasesecurCmd->setSubType('numeric');
			$enphasesecurCmd->setUnite('W');
			$enphasesecurCmd->save();
		}
  	}
	// Fonction ex??cut??e automatiquement avant la suppression de l'??quipement
  	public function preRemove() {
		self::deamon_stop();
	}

	// Fonction ex??cut??e automatiquement apr??s la suppression de l'??quipement
  	public function postRemove() {}

	// Fontion pour widget
	public function toHtml($_version = 'dashboard') {
		if ($this->getConfiguration('widgetTemplate') != 1) {
			return parent::toHtml($_version);
		 }
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
			$replace['#' . $logical . '_name#'] = $cmd->getName();
			$replace['#' . $logical . '_collect#'] = $collectDate;
		}
		$replace['#refresh_id#'] = $this->getCmd('action', 'refresh')->getId();
	
		$html = template_replace($replace, getTemplate('core', $version, 'enphasesecur_dashboard', __CLASS__));
		cache::set('widgetHtml' . $_version . $this->getId(), $html, 0);
		return $html;
	}

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
        	if ((config::byKey('user', __CLASS__) == '') || (config::byKey('password', __CLASS__) == '') || (config::byKey('site', __CLASS__) == '') || (config::byKey('ip', __CLASS__) == '') || (config::byKey('serie', __CLASS__) == '')) {
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

		self::getFreePort();

		if (config::byKey('delais', __CLASS__) == ''){
			config::save('delais','60','enphasesecur');
		}
		
       $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != 'ok') {
            throw new Exception(__('Veuillez v??rifier la configuration', __FILE__));
        }

		if (config::byKey('delais', __CLASS__) == ''){
			config::save('delais','60','enphasesecur');
		}

		enphasesecur::creationmaj();
		
		$path = realpath(dirname(__FILE__) . '/../../resources/enphasesecurd'); // r??pertoire du d??mon
		$cmd = 'python3 ' . $path . '/enphasesecurd.py'; // nom du d??mon
		$cmd .= ' --renew "' . trim(str_replace('"', '\"', config::byKey('ctoken', __CLASS__))) . '"'; 
		$cmd .= ' --loglevel ' . log::convertLogLevel(log::getLogLevel(__CLASS__));
		$cmd .= ' --socketport ' . config::byKey('socketport', __CLASS__); // port par d??faut
		$cmd .= ' --callback ' . network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/enphasesecur/core/php/jeeenphasesecur.php'; // chemin de la callback url ?? modifier (voir ci-dessous)
		$cmd .= ' --user "' . trim(str_replace('"', '\"', config::byKey('user', __CLASS__))) . '"'; 
		$cmd .= ' --password "' . trim(str_replace('"', '\"', config::byKey('password', __CLASS__))) . '"'; 
		$cmd .= ' --site "' . trim(str_replace('"', '\"', config::byKey('site', __CLASS__))) . '"'; 
		$cmd .= ' --serie "' . trim(str_replace('"', '\"', config::byKey('serie', __CLASS__))) . '"'; 
		$cmd .= ' --token "' . trim(str_replace('"', '\"', config::byKey('token', __CLASS__))) . '"'; 
		$cmd .= ' --ip "' . trim(str_replace('"', '\"', config::byKey('ip', __CLASS__))) . '"'; 
		$cmd .= ' --apikey ' . jeedom::getApiKey(__CLASS__); // l'apikey pour authentifier les ??changes suivants
		$cmd .= ' --pid ' . jeedom::getTmpFolder(__CLASS__) . '/deamon.pid'; // et on pr??cise le chemin vers le pid file (ne pas modifier)
		$cmd .= ' --delais '  . config::byKey('delais', __CLASS__); // delais actualisation
		log::add(__CLASS__, 'info', $cmd);
        log::add(__CLASS__, 'info', 'Lancement d??mon');
        $result = exec($cmd . ' >> ' . log::getPathToLog('enphasesecur_daemon') . ' 2>&1 &'); // 'template_daemon' est le nom du log pour votre d??mon, vous devez nommer votre log en commen??ant par le pluginid pour que le fichier apparaisse dans la page de config
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
            log::add(__CLASS__, 'error', __('Impossible de lancer le d??mon, v??rifiez le log', __FILE__), 'unableStartDeamon');
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
        system::kill('enphasesecurd.py'); // nom du d??mon ?? modifier
        sleep(1);
    }
}

class enphasesecurCmd extends cmd {

	// Ex??cution d'une commande
  	public function execute($_options = array()) {
	  	/*$eqlogic = $this->getEqLogic();
		try {
			$eqlogic->refresh();
		} catch (Exception $exc) {
			log::add('enphasesecur', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
		}*/
  	}
}
