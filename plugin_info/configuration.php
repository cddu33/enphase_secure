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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('desktop', 'configuration', 'js', 'enphasesecur');
include_file('core', 'authentification', 'php');
if (!isConnect()) {
  include_file('desktop', '404', 'php');
  die();
}
?>
<form class="form-horizontal">
    <fieldset>
		<legend><i class="fas fa-wrench"></i> {{Choix Token de connexion}}</legend>
		<div class="form-group">
      		<label class="col-md-4 control-label">{{Méthode de gestion du token}}
        		<sup><i class="fas fa-question-circle tooltips" title="{{Mode auto: renouvellement automatique // Mode manuel: Fourniture du token par l'utilisateur (voir documentation)}}"></i></sup>
      		</label>
      		<div class="col-md-4">
			  	<select id="sel_token" onchange="renew();" class="configKey form-control" data-l1key="ctoken">
                    <option value="auto">{{Renouvellement automatique}}</option>
                    <option value="manu">{{Renouvellement Manuel}}</option>
                </select>
      		</div>
    	</div>
        <legend class="auto"><i class="fas fa-wrench auto"></i> {{Connexion Serveur Enphase}}</legend>
		<div class="form-group auto">
      		<label class="col-md-4 control-label">{{Nom d'utilisateur Enphase}}
        		<sup><i class="fas fa-question-circle tooltips" title="{{Nom d'utilisateur que vous utilisez pour vous sur le site Enphase web}}"></i></sup>
			</label>
      		<div class="col-md-4">
        		<input class="configKey form-control" data-l1key="user"/>
      		</div>
    	</div>
    	<div class="form-group auto">
      		<label class="col-md-4 control-label">{{Password Enphase}}
        		<sup><i class="fas fa-question-circle tooltips" title="{{Mot de passe que vous utilisez pour vous sur le site Enphase web}}"></i></sup>
      		</label>
      		<div class="col-md-4">
        		<input type="password" class="configKey form-control" data-l1key="password" autocomplete="new-password"/>
      		</div>
    	</div>
							
		<legend><i class="fas fa-wrench"></i> {{Passerelle Enphase}} </legend>

    	<div class="form-group">
      		<label class="col-md-4 control-label">{{Adesse IP local}}
        		<sup><i class="fas fa-question-circle tooltips" title="{{Adesse ip sur vote réseau de votre passerelle Enphase}}"></i></sup>
     		</label>
      		<div class="col-md-4">
        		<input class="configKey form-control" data-l1key="ip">
      		</div>
		</div>

		<div class="form-group auto">
      		<label class="col-md-4 control-label">{{Numéro de série}}
        		<sup><i class="fas fa-question-circle tooltips" title="{{Numéro de série de votre passerelle Enphase}}"></i></sup>
			</label>
      		<div class="col-md-4">
        		<input type="number" class="configKey form-control" data-l1key="serie">
      		</div>
		</div>

		<div class="form-group manu">
      		<label class="col-md-4 control-label">{{Token}}
        		<sup><i class="fas fa-question-circle tooltips" title="{{Token généré manuellement depuis le site Enphase}}"></i></sup>
     		</label>
			<div class="col-md-4">
				<input class="configKey form-control" data-l1key="token">
			</div>
		</div>

		<div class="form-group">
      		<label class="col-md-4 control-label">{{Actualisation}}
        		<sup><i class="fas fa-question-circle tooltips" title="{{Délais d'intéroggation de votre passerelle Enphase}}"></i></sup>
     		</label>
      		<div class="col-md-4">
        		<input type="number" class="configKey form-control" data-l1key="delais">
      		</div>
		</div>


        <legend><i class="fas fa-tablet-alt"></i>{{Equipement}}</legend>

		<div class="form-group">
			<label class="col-sm-4 control-label">{{Production et Consommation}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Choississez si vous voulez que la production soit sur un équipement différent de la consommation}}"></i></sup>
			</label>
			<div class="col-sm-4">
				<select id="sel_object" class="configKey form-control" data-l1key="widget">
                	<option value="1">{{Mode combiné}}</option>
                    <option value="3">{{Mode divisé}}</option>
            	</select>
                {{Le changement de mode supprimera les équipements}}
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-md-4 control-label">{{Raccordement Electrique}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Choississez le type de raccordement de vos panneaux solaires}}"></i></sup>
			</label>
			<div class="col-md-4">
				<select id="sel_reseau" onchange="renew();" class="configKey form-control" data-l1key="typereseau">
                    <option value="mono">{{Mono-phasé}}</option>
                    <option value="tri">{{Tri-phase}}</option>
                </select>
			</div>
		</div>


		<legend><i class="fas fa-wrench"></i> {{Auto Consommation}} </legend>
		<div class="form-group">
			<label class="col-md-4 control-label">{{Mode de fonctionnement}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Choississez le type de raccordement de vos panneaux solaires}}"></i></sup>
			</label>
			<div class="col-md-4">
				<select id="sel_autoconso" onchange="renew();" class="configKey form-control" data-l1key="autoconso">
                    <option value="oui">{{Automatique}}</option>
                    <option value="non">{{Arrêt}}</option>
                </select>
			</div>
		</div>
		<div class="form-group autoconso">
			<label class="col-sm-4 control-label">{{Surplus déclenchement seuil 1}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Définissez le seuil de surplus de production minimum pour déclencher l'autoconsommation de niveau 1 en watts}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="number" class="configKey form-control" data-l1key="wattsautoconso1on">
      		</div>
		</div>
		<div class="form-group mono">
			<label class="col-sm-4 control-label">{{Commande déclenchement seuil 1}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher au niveau 1}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso1on">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil1on()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		<div class="form-group tri">
			<label class="col-sm-4 control-label">{{Commande déclenchement seuil 1 phase1}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher au niveau 1 phase1}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso1on1">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil1on1()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		<div class="form-group tri">
			<label class="col-sm-4 control-label">{{Commande déclenchement seuil 1 phase2}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher au niveau 1 phase2}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso1on2">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil1on2()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		<div class="form-group tri">
			<label class="col-sm-4 control-label">{{Commande déclenchement seuil 1 phase3}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher au niveau 1 phase3}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso1on3">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil1on3()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>

		<div class="form-group autoconso">
			<label class="col-sm-4 control-label">{{Surplus arrêt seuil 1}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Définissez le seuil de production pour arréter l'autoconsommation de niveau 1 en watts}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="number" class="configKey form-control" data-l1key="wattsautoconso1off">
      		</div>
		</div>
		<div class="form-group mono">
			<label class="col-sm-4 control-label">{{Commande arrêt seuil 1}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher pour arréter l'autoconsomamtion de niveau 1}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso1off">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil1off()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		<div class="form-group tri">
			<label class="col-sm-4 control-label">{{Commande arrêt seuil 1 phase1}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher pour arréter l'autoconsomamtion de niveau 1 phase1}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso1off1">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil1off1()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		<div class="form-group tri">
			<label class="col-sm-4 control-label">{{Commande arrêt seuil 1 phase2}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher pour arréter l'autoconsomamtion de niveau 1 phase2}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso1off2">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil1off2()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		<div class="form-group tri">
			<label class="col-sm-4 control-label">{{Commande arrêt seuil 1 phase3}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher pour arréter l'autoconsomamtion de niveau 1 phase3}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso1off3">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil1off3()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		</br></br>
		<!-- <div class="form-group">
			<label class="col-sm-4 control-label">{{Surplus déclenchement seuil 2}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Définissez le seuil de surplus de production minimum pour déclencher l'autoconsommation de niveau 2 en watts}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="number" class="configKey form-control" data-l1key="wattsautoconso2on">
      		</div>
		</div>
		<div class="form-group mono">
			<label class="col-sm-4 control-label">{{Commande déclenchement seuil 2}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher au niveau 2}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso2on">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil2on()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		<div class="form-group tri">
			<label class="col-sm-4 control-label">{{Commande déclenchement seuil 2 phase1}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher au niveau 2 phase1}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso2on1">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil2on1()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		<div class="form-group tri">
			<label class="col-sm-4 control-label">{{Commande déclenchement seuil 2 phase2}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher au niveau 2 phase2}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso2on2">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil2on2()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		<div class="form-group tri">
			<label class="col-sm-4 control-label">{{Commande déclenchement seuil 2 phase3}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher au niveau 2 phase3}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso2on3">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil2on3()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>

		<div class="form-group">
			<label class="col-sm-4 control-label">{{Surplus arrêt seuil 2}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Définissez le seuil de production pour arréter l'autoconsommation de niveau 2 en watts}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="number" class="configKey form-control" data-l1key="wattsautoconso2off">
      		</div>
		</div>
		<div class="form-group mono">
			<label class="col-sm-4 control-label">{{Commande arrêt seuil 2}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher pour arréter l'autoconsommation de niveau 2}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso2off">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil2off()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		<div class="form-group tri">
			<label class="col-sm-4 control-label">{{Commande arrêt seuil 2 phase1}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher pour arréter l'autoconsommation de niveau 2 phase1}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso2off1">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil2off1()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		<div class="form-group tri">
			<label class="col-sm-4 control-label">{{Commande arrêt seuil 2 phase2}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher pour arréter l'autoconsommation de niveau 2 phase2}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso2off2">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil2off2()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div>
		<div class="form-group tri">
			<label class="col-sm-4 control-label">{{Commande arrêt seuil 2 phase3}}
				<sup><i class="fas fa-question-circle tooltips" title="{{Commande à déclencher pour arréter l'autoconsommation de niveau 2 phase3}}"></i></sup>
			</label>
			<div class="col-md-4">
        		<input type="text" class="configKey form-control" data-l1key="cmdautoconso2off3">
				<span class="input-group-btn">
          			<a class="btn btn-default cursor" title="Rechercher une commande" onclick="modalseuil2off3()"><i class="fas fa-list-alt"></i></a>
       			</span>
      		</div>
		</div> -->

	
    </fieldset>
</form>
<script>
function modalseuil1on() {
    jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
        $('.configKey[data-l1key=cmdautoconso1on]').atCaret('insert', result.human);
    });
}
function modalseuil1off() {
    jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
        $('.configKey[data-l1key=cmdautoconso1off]').atCaret('insert', result.human);
    });
}
// function modalseuil2on() {
//     jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
//         $('.configKey[data-l1key=cmdautoconso2on]').atCaret('insert', result.human);
//     });
// }
// function modalseuil2off() {
//     jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
//         $('.configKey[data-l1key=cmdautoconso2off]').atCaret('insert', result.human);
//     });
// }
function modalseuil1on1() {
    jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
        $('.configKey[data-l1key=cmdautoconso1on1]').atCaret('insert', result.human);
    });
}
function modalseuil1off1() {
    jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
        $('.configKey[data-l1key=cmdautoconso1off1]').atCaret('insert', result.human);
    });
}
function modalseuil1on2() {
    jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
        $('.configKey[data-l1key=cmdautoconso1on2]').atCaret('insert', result.human);
    });
}
function modalseuil1off2() {
    jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
        $('.configKey[data-l1key=cmdautoconso1off2]').atCaret('insert', result.human);
    });

}
function modalseuil1on3() {
    jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
        $('.configKey[data-l1key=cmdautoconso1on3]').atCaret('insert', result.human);
    });
}
	function modalseuil1off3() {
    jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
        $('.configKey[data-l1key=cmdautoconso1off3]').atCaret('insert', result.human);
    });
}
// function modalseuil2on1() {
//     jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
//         $('.configKey[data-l1key=cmdautoconso2on1]').atCaret('insert', result.human);
//     });
// }
// 	function modalseuil2off1() {
//     jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
//         $('.configKey[data-l1key=cmdautoconso2off1]').atCaret('insert', result.human);
//     });
// }
// function modalseuil2on2() {
//     jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
//         $('.configKey[data-l1key=cmdautoconso2on2]').atCaret('insert', result.human);
//     });
// }
// 	function modalseuil2off2() {
//     jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
//         $('.configKey[data-l1key=cmdautoconso2off2]').atCaret('insert', result.human);
//     });
// }
// function modalseuil2on3() {
//     jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
//         $('.configKey[data-l1key=cmdautoconso2on3]').atCaret('insert', result.human);
//     });
// }
// 	function modalseuil2off3() {
//     jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: 'other'}}, function (result) {
//         $('.configKey[data-l1key=cmdautoconso2off3]').atCaret('insert', result.human);
//     });
// }
</script>