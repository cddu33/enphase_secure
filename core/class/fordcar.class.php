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
      
		$fordcar_path = realpath(dirname(__FILE__));
		foreach (self::byType('fordcar', true) as $fordcar) {
			$cmd = 'python3 ' . $fordcar_path .'/../../resources/fordstatut.py';
			//$cmd .= ' ' . $fordcar::byKey('user', 'fordcar') . ' ' . $fordcar::byKey('password', 'fordcar') . ' ' . $fordcar::byKey('vin', 'fordcar') .' ' . 'statut' . ' ' . '/../../data/'. $fordcar::byKey('vin', 'fordcar') . '.json' . ' ' . $fordcar_path;
			$cmd .= ' ' . $this->getConfiguration("user") . ' ' . $this->getConfiguration("password") . ' ' . $this->getConfiguration("vin") .' ' . 'statut' . ' ' . '/../../data/'. $fordcar::byKey('vin', 'fordcar') . '.json' . ' ' . $fordcar_path;
		
			
			log::add('fordcar', 'debug', 'commande ' . $cmd);
			//$cmd->execCmd();
			exec($cmd . ' >> ' . log::getPathToLog('fordcar') . ' 2>&1 &');
		
		}
  //  fordcar::refresh();
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
 

  /*
  * Permet de modifier l'affichage du widget (également utilisable par les commandes)
  public function toHtml($_version = 'dashboard') {}
  */

  /*
  * Permet de déclencher une action avant modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function preConfig_param3() {}
  */

  /*
  * Permet de déclencher une action après modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function postConfig_param3() {}
  */

  /*     * **********************Getteur Setteur*************************** */

}

class fordcarCmd extends cmd {

	
  /*     * *************************Attributs****************************** */

  /*
  public static $_widgetPossibility = array();
  */

  /*     * ***********************Methode static*************************** */
  

	
	


  /*     * *********************Methode d'instance************************* */

  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  public function dontRemoveCmd() {
    return true;
  }
  */

  // Exécution d'une commande
  public function execute($_options = array()) {
  }

  /*     * **********************Getteur Setteur*************************** */



}