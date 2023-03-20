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
        <legend><i class="fas fa-wrench auto"></i> {{Connexion Serveur Enphase}}</legend>
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
							<div class="form-group auto">
      							<label class="col-md-4 control-label">{{Id de site}}
        							<sup><i class="fas fa-question-circle tooltips" title="{{ID du site correspondant à votre passerelle}}"></i></sup>
      							</label>
      							<div class="col-md-4">
        							<input type="number" class="configKey form-control" data-l1key="site"/>
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
                            <legend><i class="fas fa-tablet-alt"></i> {{Equipement}}</legend>
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
								<label class="col-sm-4 control-label">{{Raccordement Electrique}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Choississez le type de raccordement de vos panneaux solaires}}"></i></sup>
								</label>
								<div class="col-sm-4">
									<select id="sel_object" class="configKey form-control" data-l1key="typereseau">
                                    <option value="mono">{{Mono-phasé}}</option>
                                    <option value="tri">{{Tri-phase}}</option>
                                    </select>
								</div>
							</div>
                            </fieldset>
                            </form>