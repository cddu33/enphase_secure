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
		
		//fordcar::refresh();
		
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
	   $refresh = $this->getCmd(null, 'refresh');
  if (!is_object($refresh)) {
    $refresh = new fordcarCmd();
    $refresh->setName(__('Rafraichir', __FILE__));
  }
  $refresh->setEqLogic_id($this->getId());
  $refresh->setLogicalId('refresh');
  $refresh->setType('action');
  $refresh->setSubType('other');
  $refresh->save();
  	   $lock = $this->getCmd(null, 'lock');
  if (!is_object($lock)) {
    $lock = new fordcarCmd();
    $lock->setName(__('Vérouiller', __FILE__));
  }
  $lock->setEqLogic_id($this->getId());
  $lock->setLogicalId('lock');
  $lock->setType('action');
  $lock->setSubType('other');
  $lock->save();

  $unlock = $this->getCmd(null, 'unlock');
  if (!is_object($unlock)) {
    $unlock = new fordcarCmd();
    $unlock->setName(__('Dévérouiller', __FILE__));
  }
  $unlock->setEqLogic_id($this->getId());
  $unlock->setLogicalId('unlock');
  $unlock->setType('action');
  $unlock->setSubType('other');
  $unlock->save();
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
			$fordcar_cmd = 'python3 ' . $fordcar_path .'/../../resources/fordstatut.py';
			$fordcar_cmd .= ' ' . $fordcar_user . ' ' . $fordcar_pass . ' ' . $fordcar_vin .' ' . 'statut' . ' ' . $fordcar_path .'/../../data/'. $fordcar_vin . '.json';
			log::add('fordcar', 'debug', 'commande ' . $fordcar_cmd);
			//$cmd->execCmd();
			exec($fordcar_cmd . ' >> ' . log::getPathToLog('fordcar') . ' 2>&1 &');
		}
  }
  public function commandes($fordcar_statut) {
		$fordcar_pass = $this->getConfiguration('password');
		$fordcar_vin = $this->getConfiguration('vin');
			$fordcar_user = $this->getConfiguration('user');
			$fordcar_cmd = 'python3 ' . realpath(dirname(__FILE__)) .'/../../resources/fordcmd.py';
			$fordcar_cmd .= ' ' . $fordcar_user . ' ' . $fordcar_pass . ' ' . $fordcar_vin .' ' . $fordcar_statut ;
			log::add('fordcar', 'debug', 'commande ' . $fordcar_cmd);
			//$cmd->execCmd();
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
 $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this
  switch ($this->getLogicalId()) { //vérifie le logicalid de la commande
    case 'refresh': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave de la classe vdm .
    $info = $eqlogic->refresh(); //On lance la fonction randomVdm() pour récupérer une vdm et on la stocke dans la variable $info
   break;
   case 'lock': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave de la classe vdm .
    $info = $eqlogic->commandes("lock"); //On lance la fonction randomVdm() pour récupérer une vdm et on la stocke dans la variable $info*
	break;
	 case 'unlock': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave de la classe vdm .
    $info = $eqlogic->commandes("unlock"); //On lance la fonction randomVdm() pour récupérer une vdm et on la stocke dans la variable $info
    break;
  }
  return $info;
  }
  /*     * **********************Getteur Setteur*************************** */



}