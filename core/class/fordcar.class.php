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

class fordcar extends eqLogic {
  /*     * *************************Attributs****************************** */

	public static function dependancy_info() {
		$return = array();
        $return['log'] = log::getPathToLog(__CLASS__ . '_update');
        $return['progress_file'] = jeedom::getTmpFolder(__CLASS__) . '/dependency';
        if (file_exists(jeedom::getTmpFolder(__CLASS__) . '/dependency')) {
			$return['state'] = 'in_progress';
        } else {
            if (exec(system::getCmdSudo() . system::get('cmd_check') . '-Ec "python3\-requests"') < 1) { // adaptez la liste des paquets et le total
                $return['state'] = 'nok';
            } else {
                $return['state'] = 'ok';
            }
        }
        return $return;
    }

	public static function dependancy_install() {
		log::remove(__CLASS__ . '_update');
		return array('script' => dirname(__FILE__) . '/../../resources/install_#stype#.sh ' . jeedom::getTmpFolder('fordcar') . '/dependance', 'log' => log::getPathToLog(__CLASS__ . '_update'));
	}
  

	
	
  /*
==
  * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
  * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
  public static $_widgetPossibility = array();
  */

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration du plugin
  * Exemple : "param1" & "param2" seront cryptés mais pas "param3"
   */
  //public static $_encryptConfigKey = array('password', 'vin');
  
 

  /*     * ***********************Methode static*************************** */

  /*
  * Fonction exécutée automatiquement toutes les minutes par Jeedom  */
 
  public static function cron() {
	  foreach (self::byType('fordcar', true) as $fordcar) { //parcours tous les équipements actifs du plugin vdm
		  $cmd = $fordcar->getCmd(null, 'refresh'); //retourne la commande "refresh" si elle existe
		  if (!is_object($cmd)) { //Si la commande n'existe pas
			  continue; //continue la boucle
			  }
			  $cmd->execCmd(); //la commande existe on la lance
}
}


  /*
  * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
  public static function cron5() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
  public static function cron10() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
  public static function cron15() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
  public static function cron30() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les heures par Jeedom
  public static function cronHourly() {}
  */

  /*
  * Fonction exécutée automatiquement tous les jours par Jeedom
  public static function cronDaily() {}
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
	  $fordcarCmd = $this->getCmd(null, 'refresh');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Rafraichir', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('refresh');
	  $fordcarCmd->setType('action');
	  $fordcarCmd->setSubType('other');
	  $fordcarCmd->save();
  
	  $fordcarCmd = $this->getCmd(null, 'lock');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Vérouiller', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('lock');
	  $fordcarCmd->setType('action');
	  $fordcarCmd->setSubType('other');
	  $fordcarCmd->save();
	  
	  $fordcarCmd = $this->getCmd(null, 'unlock');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Dévérouiller', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('unlock');
	  $fordcarCmd->setType('action');
	  $fordcarCmd->setSubType('other');
	  $fordcarCmd->save();
	  
	  $fordcarCmd = $this->getCmd(null, 'etat');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Etat', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('etat');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'last');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Dernière actualisation', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('last');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'maj');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Mise à jour en cours', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('maj');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('binary');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'veille');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Veille profonde', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('veille');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('binary');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'km');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Kilométrage', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('km');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('numeric');
	  $fordcarCmd->setUnite('km');
	  $fordcarCmd->save();
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
  }

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration des équipements
  * Exemple avec le champ "Mot de passe" (password)
   */
  public function decrypt() {
	  $this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
	  $this->setConfiguration('vin', utils::decrypt($this->getConfiguration('vin')));
  }
  public function encrypt() {
	  $this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
	  $this->setConfiguration('vin', utils::encrypt($this->getConfiguration('vin')));
  }
 

  public function refresh() {
	$fordcar_path = realpath(dirname(__FILE__));
	foreach (self::byType('fordcar', true) as $fordcar) {
		$fordcar_pass = $this->getConfiguration('password');
		$fordcar_vin = $this->getConfiguration('vin');
		$fordcar_user = $this->getConfiguration('user');
		$fordcar_fichier = $fordcar_path .'/../../data/'. $fordcar_vin . '.json';
		$fordcar_cmd = 'python3 ' . $fordcar_path .'/../../resources/fordstatut.py';
		$fordcar_cmd .= ' ' . $fordcar_user . ' ' . $fordcar_pass . ' ' . $fordcar_vin .' ' . 'statut' . ' ' . $fordcar_fichier;
		log::add('fordcar', 'debug', 'commande ' . $fordcar_cmd);
		exec($fordcar_cmd . ' >> ' . log::getPathToLog('fordcar') . ' 2>&1 &');
		$fordcar_json = json_decode(file_get_contents($fordcar_fichier), true);
		if ($fordcar_json === null) {
			throw new Exception(__('Json invalide ou non décodable : ', __FILE__));
		}
		log::add('fordcar', 'debug', 'etat lock:' . $fordcar_json['lockStatus']['value']);
		$this->checkAndUpdateCmd('etat', $fordcar_json['lockStatus']['value']);
		log::add('fordcar', 'debug', 'dernière actualisation:' . $fordcar_json['lastRefresh']);
		$this->checkAndUpdateCmd('last', $fordcar_json['lastRefresh']);
		log::add('fordcar', 'debug', 'Mise à jour en cours:' . $fordcar_json['lastRefrfirmwareUpgInProgressesh']['value']);
		$this->checkAndUpdateCmd('maj', $fordcar_json['lastRefrfirmwareUpgInProgressesh']['value']);
		log::add('fordcar', 'debug', 'Veille profonde' . $fordcar_json['deepSleepInProgress']['value']);
		$this->checkAndUpdateCmd('veille', $fordcar_json['deepSleepInProgress']['value']);
		log::add('fordcar', 'debug', 'Kilométrage' . $fordcar_json['odometer']['value']);
		$this->checkAndUpdateCmd('km', $fordcar_json['odometer']['value']);
	}
  }
  public function commandes($fordcar_statut) {
	  $fordcar_pass = $this->getConfiguration('password');
	  $fordcar_vin = $this->getConfiguration('vin');
	  $fordcar_user = $this->getConfiguration('user');
	  $fordcar_cmd = 'python3 ' . realpath(dirname(__FILE__)) .'/../../resources/fordcmd.py';
	  $fordcar_cmd .= ' ' . $fordcar_user . ' ' . $fordcar_pass . ' ' . $fordcar_vin .' ' . $fordcar_statut ;
	  log::add('fordcar', 'debug', 'commande ' . $fordcar_cmd);
	  exec($fordcar_cmd . ' >> ' . log::getPathToLog('fordcar') . ' 2>&1 &');
  }
}

class fordcarCmd extends cmd {

	
  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  */
  public function dontRemoveCmd() {
    return true;
  }

  // Exécution d'une commande
  public function execute($_options = array()) {
	  $eqlogic = $this->getEqLogic();
	  switch ($this->getLogicalId()) 
	  { 
		  case 'refresh':
		  $info = $eqlogic->refresh(); 
		  break;
		  case 'lock':
		  $info = $eqlogic->commandes("lock"); 
		  break;
		  case 'unlock':
		  $info = $eqlogic->commandes("unlock"); 
		  break;
	  }
	  return $info;
  }
  /*     * **********************Getteur Setteur*************************** */



}