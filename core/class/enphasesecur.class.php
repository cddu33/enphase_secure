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
			$enphasesecurCmd->setConfiguration('minValue', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setConfiguration('historizeRound', '2');
			$enphasesecurCmd->setGeneric_type('POWER'); 
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('PwattHoursToday');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('kW');
		$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'PwattHoursSevenDays');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Prod Semaine', __FILE__));
			$enphasesecurCmd->setConfiguration('minValue', '0');
			$enphasesecurCmd->setConfiguration('historizeRound', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setGeneric_type('POWER');
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('PwattHoursSevenDays');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('kW');
		$enphasesecurCmd->save();
		
		$enphasesecurCmd = $this->getCmd(null, 'PwattHoursLifetime');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Prod MES', __FILE__));
			$enphasesecurCmd->setConfiguration('minValue', '0');
			$enphasesecurCmd->setConfiguration('historizeRound', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setGeneric_type('POWER');
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('PwattHoursLifetime');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('kW');
		$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'PwattsNow');
	  	if (!is_object($enphasesecurCmd)) {
			$enphasesecurCmd = new enphasesecurCmd();
		  	$enphasesecurCmd->setName(__('Prod Inst', __FILE__));
			//$enphasesecurCmd->setConfiguration('minValue', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setConfiguration('historizeRound', '3'); 
			$enphasesecurCmd->setGeneric_type('POWER');
	  	}
	  	$enphasesecurCmd->setEqLogic_id($this->getId());
	  	$enphasesecurCmd->setLogicalId('PwattsNow');
	  	$enphasesecurCmd->setType('info');
	  	$enphasesecurCmd->setSubType('numeric');
	  	$enphasesecurCmd->setUnite('kW');
		$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'CwattHoursToday');
		if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Conso Jour', __FILE__));
			$enphasesecurCmd->setConfiguration('minValue', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setConfiguration('historizeRound', '2'); 
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattHoursToday');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('kW');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'CwattHoursSevenDays');
		if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Conso Semaine', __FILE__));
			$enphasesecurCmd->setConfiguration('minValue', '0');
			$enphasesecurCmd->setConfiguration('historizeRound', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattHoursSevenDays');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('kW');

	  	$enphasesecurCmd->save();
	  
	  	$enphasesecurCmd = $this->getCmd(null, 'CwattHoursLifetime');
		if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Consom MES', __FILE__));
			$enphasesecurCmd->setConfiguration('minValue', '0');
			$enphasesecurCmd->setConfiguration('historizeRound', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattHoursLifetime');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('kW');
	  	$enphasesecurCmd->save();

	  	$enphasesecurCmd = $this->getCmd(null, 'CwattsNow');
		if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Conso Inst', __FILE__));
			$enphasesecurCmd->setConfiguration('minValue', '0');
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setIsHistorized('1');
			$enphasesecurCmd->setConfiguration('historizeRound', '3');
			$enphasesecurCmd->setGeneric_type('CONSUMPTION');
		}
		$enphasesecurCmd->setEqLogic_id($this->getId());
		$enphasesecurCmd->setLogicalId('CwattsNow');
		$enphasesecurCmd->setType('info');
		$enphasesecurCmd->setSubType('numeric');
		$enphasesecurCmd->setUnite('kW');
	  	$enphasesecurCmd->save();

		$enphasesecurCmd = $this->getCmd(null, 'tension');
		if (!is_object($enphasesecurCmd)) {
		  	$enphasesecurCmd = new enphasesecurCmd();
			$enphasesecurCmd->setName(__('Tension', __FILE__));
			$enphasesecurCmd->setTemplate('dashboard', 'core::badge');
			$enphasesecurCmd->setConfiguration('minValue', '0');
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

			$enphasesecur_info = $enphasesecur_json['production']['1']['whLifetime']/1000;
			log::add('enphasesecur', 'debug', 'Production depuis la mise en service: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('PwattHoursLifetime', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['production']['1']['whToday']/1000;
			log::add('enphasesecur', 'debug', 'Production du jour: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('PwattHoursToday', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['production']['1']['whLastSevenDays']/1000;
			log::add('enphasesecur', 'debug', 'Production de la semaine: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('PwattHoursSevenDays', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['production']['1']['wNow']/1000;
			log::add('enphasesecur', 'debug', 'Production instantannée: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('PwattsNow', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['consumption']['0']['whLifetime']/1000;
			log::add('enphasesecur', 'debug', 'Consommation depuis la mise en service: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('CwattHoursLifetime', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['consumption']['0']['whToday']/1000;
			log::add('enphasesecur', 'debug', 'Consommation du jour: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('CwattHoursToday', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['consumption']['0']['whLastSevenDays']/1000;
			log::add('enphasesecur', 'debug', 'Consommation de la semaine: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('CwattHoursSevenDays', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['consumption']['0']['wNow']/1000;
			log::add('enphasesecur', 'debug', 'Consommation instantannée: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('CwattsNow', $enphasesecur_info);	
		
			$enphasesecur_info = $enphasesecur_json['consumption']['0']['rmsVoltage'];
			log::add('enphasesecur', 'debug', 'Tension réseau: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('tension', $enphasesecur_info);
		}
		else {
			log::add('enphasesecur', 'debug', 'Envoy-S-Standard-EU');
			
			$enphasesecur_info = $enphasesecur_json['production']['0']['whLifetime']/1000;
			log::add('enphasesecur', 'debug', 'Production depuis la mise en service: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('PwattHoursLifetime', $enphasesecur_info);	

			$enphasesecur_info = $enphasesecur_json['production']['0']['wNow']/1000;
			log::add('enphasesecur', 'debug', 'Production instantannée: ' . $enphasesecur_info);
			$this->checkAndUpdateCmd('PwattsNow', $enphasesecur_info);	
		}

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
       /* $user = config::byKey('user', __CLASS__); // exemple si votre démon à besoin de la config user,
        $pswd = config::byKey('password', __CLASS__); // password,
        $clientId = config::byKey('clientId', __CLASS__); // et clientId
        if ($user == '') {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('Le nom d\'utilisateur n\'est pas configuré', __FILE__);
        } elseif ($pswd == '') {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('Le mot de passe n\'est pas configuré', __FILE__);
        } elseif ($clientId == '') {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('La clé d\'application n\'est pas configurée', __FILE__);
        }*/
        return $return;
    }
	public static function deamon_start() {
        self::deamon_stop();
        $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != 'ok') {
            throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
        }

        $path = realpath(dirname(__FILE__) . '/../../resources/demond'); // répertoire du démon à modifier
        $cmd = 'python3 ' . $path . '/demond.py'; // nom du démon à modifier
        $cmd .= ' --loglevel ' . log::convertLogLevel(log::getLogLevel(__CLASS__));
        $cmd .= ' --socketport ' . config::byKey('socketport', __CLASS__, '55060'); // port par défaut à modifier
        $cmd .= ' --callback ' . network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/template/core/php/jeeTemplate.php'; // chemin de la callback url à modifier (voir ci-dessous)
        /*$cmd .= ' --user "' . trim(str_replace('"', '\"', config::byKey('user', __CLASS__))) . '"'; // on rajoute les paramètres utiles à votre démon, ici user
        $cmd .= ' --pswd "' . trim(str_replace('"', '\"', config::byKey('password', __CLASS__))) . '"'; // et password*/
        $cmd .= ' --apikey ' . jeedom::getApiKey(__CLASS__); // l'apikey pour authentifier les échanges suivants
        $cmd .= ' --pid ' . jeedom::getTmpFolder(__CLASS__) . '/deamon.pid'; // et on précise le chemin vers le pid file (ne pas modifier)
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
	  	$eqlogic = $this->getEqLogic();
		try {
			$eqlogic->refresh();
		} catch (Exception $exc) {
			log::add('enphasesecur', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
		}
  	}
}
?>
