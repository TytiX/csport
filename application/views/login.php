<?php
/**
 * CSPORT
 * refereeing and matches management tool
 *
 * Copyright (c) 2013-2014 Marie Kuntz - Lezard Rouge
 *
 * This file is part of CSPORT.
 * CSPORT is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * CSPORT is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with CSPORT.  If not, see <http://www.gnu.org/licenses/>.
 * See LICENCE.TXT file for more information.
 *
 * @copyright  Copyright (c) 2013-2014 Marie Kuntz - Lezard Rouge (http://www.lezard-rouge.fr)
 * @license    GNU-GPL v3 http://www.gnu.org/licenses/gpl.html
 * @version    1.0
 * @author     Marie Kuntz - Lezard Rouge SARL - www.lezard-rouge.fr - info@lezard-rouge.fr
 */

/**
 * VIEW
 * login form
 *
 * @version $Id: login.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

$input_login = array(
	'name'      => 'csport_login',
	'id'        => 'csport_login',
	'maxlength' => '200',
	'class'     => 'form-control input-sm'
);
$input_pwd = array(
	'name'      => 'pwd',
	'id'        => 'pwd',
	'value'     => '',
	'maxlength' => '50',
	'class'     => 'form-control input-sm'
);
?>

<?php echo form_open('connexion'); ?>

<fieldset class="col-sm-offset-3 col-sm-6">

	<legend>Connexion</legend>

	<?php echo validation_errors(); ?>
	<?php echo $message; ?>

	<div class="form-group">
		<label for="login" class="col-sm-3 control-label">Identifiant</label>
		<div class="col-sm-9">
			<?php echo form_input($input_login); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="pwd" class="col-sm-3 control-label">Mot de passe</label>
		<div class="col-sm-9">
			<?php echo form_password($input_pwd); ?>
		</div>
	</div>


	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
		  <button type="submit" class="btn btn-primary btn-sm">Connexion</button>
		</div>
	</div>

</fieldset>

<?php echo form_close(); ?>