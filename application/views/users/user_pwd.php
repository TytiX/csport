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
 * password modification form
 *
 * @version $Id: user_pwd.php 65 2014-03-03 09:01:25Z lezardro $
 * @author Marie Kuntz / Lézard Rouge
 *
 */

//-------------------------------------
// fields
$input_old_pwd = array(
	'name'        => 'old_pwd',
	'id'          => 'old_pwd',
	'class'       => 'form-control',
	'value'       => '',
	'maxlength'   => '50',
	'size'        => '30',
	'autocomplete' => 'off'
);

$input_new_pwd = array(
	'name'        => 'new_pwd',
	'id'          => 'new_pwd',
	'class'       => 'form-control',
	'value'       => '',
	'maxlength'   => '50',
	'size'        => '30',
	'autocomplete' => 'off'
);

//-------------------------------------
// display
echo form_open(site_url('changer-mot-passe'), 'class="form-horizontal" role="form"');
?>
<fieldset class="col-sm-6">

	<legend>Modification du mot de passe</legend>

	<p>Entrez le mot de passe actuel puis saisissez le nouveau mot de passe.<br />
	Le nouveau mot de passe doit comporter 6 caractères minimum. Vous pouvez utiliser des lettres,
	des chiffres et les caractères spéciaux suivants : !:-_%&.
	</p>

	<?php echo $message; ?>

	<div class="form-group">
		<label for="old_pwd" class="col-sm-3 control-label">Mot de passe actuel</label>
		<div class="col-sm-9">
			<?php echo form_input($input_old_pwd); ?>
			<?php echo form_error('old_pwd'); ?>
		</div>
	</div>

	<div class="form-group">
		<label for="new_pwd" class="col-sm-3 control-label">Nouveau mot de passe</label>
		<div class="col-sm-9">
			<?php echo form_input($input_new_pwd); ?>
			<?php echo form_error('new_pwd'); ?>
		</div>
	</div>

<?php
echo form_submit('submitform', 'Enregistrer', 'class="btn btn-primary btn-sm"');
?>
</fieldset>
<?php echo form_close(); ?>
