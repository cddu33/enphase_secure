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

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>

<div class="form-group">
    <label class="col-sm-4 control-label">{{Groupement}}</label>
    <div class="col-sm-6">
		<select id="sel_object" class="eqLogicAttr form-control" data-l1key="groupement">
			<option value="0">{{Aucun}}</option>
			<option value="1">{{1}}</option>
			<option value="2">{{2}}</option>
			<option value="3">{{3}}</option>
			<option value="4">{{4}}</option>
		</select>
	</div>
</div>
