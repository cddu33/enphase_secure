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

	  $fordcarCmd = $this->getCmd(null, 'lat');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Latitude', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('lat');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('numeric');
	  $fordcarCmd->setUnite('°');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'long');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Longitude', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('long');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('numeric');
	  $fordcarCmd->setUnite('°');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'hbat');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Etat batterie', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('hbat');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'tbat');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Tension batterie', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('tbat');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('numeric');
	  $fordcarCmd->setUnite('V');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'ehuile');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Etat huile', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('ehuile');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'huile');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Pourcentage huile', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('huile');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('numeric');
	  $fordcarCmd->setUnite('%');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'pression');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Etat pression', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('pression');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  //$fordcarCmd->setUnite('%');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'etpnavgh');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Etat pneu avant gauche', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('etpnavgh');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  //$fordcarCmd->setUnite('%');
	  $fordcarCmd->save();

	   $fordcarCmd = $this->getCmd(null, 'etpnavdr');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Etat pneu avant droit', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('etpnavdr');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  //$fordcarCmd->setUnite('%');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'etpnargh');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Etat pneu arrière gauche', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('etpnargh');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  //$fordcarCmd->setUnite('%');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'etpnardr');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Etat pneu arrière droit', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('etpnardr');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  //$fordcarCmd->setUnite('%');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'prpnavgh');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Pression pneu avant gauche', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('prpnavgh');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('numeric');
	  $fordcarCmd->setUnite('bar');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'prpnavdr');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Pression pneu avant droit', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('prpnavdr');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('numeric');
	  $fordcarCmd->setUnite('bar');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'prpnargh');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Pression pneu arrière gauche', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('prpnargh');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('numeric');
	  $fordcarCmd->setUnite('bar');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'prpnardr');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Pression pneu arrière droit', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('prpnardr');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('numeric');
	  $fordcarCmd->setUnite('bar');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'vicdav');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Vitre conducteur avant', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('vicdav');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  //$fordcarCmd->setUnite('bar');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'vicdar');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Vitre conducteur arrière', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('vicdar');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  //$fordcarCmd->setUnite('bar');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'vipsav');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Vitre passager avant', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('vipsav');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  //$fordcarCmd->setUnite('bar');
	  $fordcarCmd->save();

	  $fordcarCmd = $this->getCmd(null, 'vipsar');
	  if (!is_object($fordcarCmd)) {
		  $fordcarCmd = new fordcarCmd();
		  $fordcarCmd->setName(__('Vitre passager arrière', __FILE__));
	  }
	  $fordcarCmd->setEqLogic_id($this->getId());
	  $fordcarCmd->setLogicalId('vipsar');
	  $fordcarCmd->setType('info');
	  $fordcarCmd->setSubType('string');
	  //$fordcarCmd->setUnite('bar');
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
		$fordcar_info = $fordcar_json['lockStatus']['value'];
		log::add('fordcar', 'debug', 'etat lock:' . $fordcar_info);
		$this->checkAndUpdateCmd('etat', $fordcar_info);

		$fordcar_info = $fordcar_json['lastRefresh'];
		log::add('fordcar', 'debug', 'dernière actualisation:' . $fordcar_info);
		$this->checkAndUpdateCmd('last', $fordcar_info);

		$fordcar_info = $fordcar_json['firmwareUpgInProgress']['value'];
		log::add('fordcar', 'debug', 'Mise à jour en cours:' . $fordcar_info);
		$this->checkAndUpdateCmd('maj', $fordcar_info);

		$fordcar_info = $fordcar_json['deepSleepInProgress']['value'];
		log::add('fordcar', 'debug', 'Veille profonde' . $fordcar_info);
		$this->checkAndUpdateCmd('veille', $fordcar_info);

		$fordcar_info = $fordcar_json['odometer']['value'];
		log::add('fordcar', 'debug', 'Kilométrage' . $fordcar_info);
		$this->checkAndUpdateCmd('km', $fordcar_info);

		$fordcar_info = $fordcar_json['gps']['latitude'];
		log::add('fordcar', 'debug', 'Latitude' . $fordcar_info);
		$this->checkAndUpdateCmd('lat', $fordcar_info);

		$fordcar_info = $fordcar_json['gps']['longitude'];
		log::add('fordcar', 'debug', 'Longitude' . $fordcar_info);
		$this->checkAndUpdateCmd('long', $fordcar_info);

		$fordcar_info = $fordcar_json['battery']['batteryHealth']['value'];
		log::add('fordcar', 'debug', 'Etat batterie' . $fordcar_info);
		$this->checkAndUpdateCmd('hbat', $fordcar_info);

		$fordcar_info = $fordcar_json['battery']['batteryStatusActual']['value'];
		log::add('fordcar', 'debug', 'Tension batterie' . $fordcar_info);
		$this->checkAndUpdateCmd('tbat', $fordcar_info);

		$fordcar_info = $fordcar_json['oil']['oilLife'];
		log::add('fordcar', 'debug', 'Etat huile' . $fordcar_info);
		$this->checkAndUpdateCmd('ehuile', $fordcar_info);

		$fordcar_info = $fordcar_json['oil']['oilLifeActual'];
		log::add('fordcar', 'debug', 'Pourcentage huile' . $fordcar_info);
		$this->checkAndUpdateCmd('huile', $fordcar_info);

		$fordcar_info = $fordcar_json['oil']['oilLifeActual'];
		log::add('fordcar', 'debug', 'Pourcentage huile' . $fordcar_info);
		$this->checkAndUpdateCmd('huile', $fordcar_info);

		$fordcar_info = $fordcar_json['tirePressure']['value'];
		log::add('fordcar', 'debug', 'Etat pression' . $fordcar_info);
		$this->checkAndUpdateCmd('pression', $fordcar_info);

		$fordcar_info = $fordcar_json['TPMS']['leftFrontTireStatus']['value'];
		log::add('fordcar', 'debug', 'Etat pneu avant gauche' . $fordcar_info);
		$this->checkAndUpdateCmd('etpnargh', $fordcar_info);

		$fordcar_info = $fordcar_json['TPMS']['rightFrontTireStatus']['value'];
		log::add('fordcar', 'debug', 'Etat pneu avant droit' . $fordcar_info);
		$this->checkAndUpdateCmd('etpnardr', $fordcar_info);

		$fordcar_info = $fordcar_json['TPMS']['outerLeftRearTireStatus']['value'];
		log::add('fordcar', 'debug', 'Etat pneu arrière gauche' . $fordcar_info);
		$this->checkAndUpdateCmd('etpnavgh', $fordcar_info);

		$fordcar_info = $fordcar_json['TPMS']['outerRightRearTireStatus']['value'];
		log::add('fordcar', 'debug', 'Etat pneu arrière droit' . $fordcar_info);
		$this->checkAndUpdateCmd('etpnavdr', $fordcar_info);
	
		$fordcar_info = $fordcar_json['TPMS']['leftFrontTirePressure']['value'];
		log::add('fordcar', 'debug', 'Pression pneu avant gauche' . $fordcar_info);
		$this->checkAndUpdateCmd('prpnargh', $fordcar_info);

		$fordcar_info = $fordcar_json['TPMS']['rightFrontTirePressure']['value'];
		log::add('fordcar', 'debug', 'Pression pneu avant droit' . $fordcar_info);
		$this->checkAndUpdateCmd('prpnardr', $fordcar_info);

		$fordcar_info = $fordcar_json['TPMS']['outerLeftRearTirePressure']['value'];
		log::add('fordcar', 'debug', 'Pression pneu arrière gauche' . $fordcar_info);
		$this->checkAndUpdateCmd('prpnavgh', $fordcar_info);

		$fordcar_info = $fordcar_json['TPMS']['outerRightRearTirePressure']['value'];
		log::add('fordcar', 'debug', 'Pression pneu arrière droit' . $fordcar_info);
		$this->checkAndUpdateCmd('prpnavdr', $fordcar_info);

		$fordcar_info = $fordcar_json['windowPosition']['driverWindowPosition']['value'];
		log::add('fordcar', 'debug', 'Fenetre conducteur avant' . $fordcar_info);
		$this->checkAndUpdateCmd('vicdav', $fordcar_info);

		$fordcar_info = $fordcar_json['windowPosition']['rearDriverWindowPos']['value'];
		log::add('fordcar', 'debug', 'Fenetre conducteur arrière' . $fordcar_info);
		$this->checkAndUpdateCmd('vicdar', $fordcar_info);

		$fordcar_info = $fordcar_json['windowPosition']['passWindowPosition']['value'];
		log::add('fordcar', 'debug', 'Fenetre passager avant' . $fordcar_info);
		$this->checkAndUpdateCmd('vipsav', $fordcar_info);

		$fordcar_info = $fordcar_json['windowPosition']['rearPassWindowPos']['value'];
		log::add('fordcar', 'debug', 'Fenetre passager arrière' . $fordcar_info);
		$this->checkAndUpdateCmd('vipsar', $fordcar_info);
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
  
  public function dontRemoveCmd() {
    return true;
  }*/

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