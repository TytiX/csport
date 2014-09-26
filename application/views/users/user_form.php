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
 * add/edit user form
 *
 * @version $Id: user_form.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

//-------------------------------------
// fields definition
$input_name = array(
	'name'      => 'name',
	'id'        => 'name',
	'value'     => ((isset($user->user_name))? htmlspecialchars_decode($user->user_name, ENT_QUOTES):''),
	'maxlength' => '200',
	'class'     => 'form-control input-sm'
);
$input_address = array(
	'name'      => 'address',
	'id'        => 'address',
	'value'     => ((isset($user->user_address))? htmlspecialchars_decode($user->user_address, ENT_QUOTES):''),
	'cols'      => 25,
	'rows'      => 3,
	'class'     => 'form-control'
);
$input_email = array(
	'name'      => 'email',
	'id'        => 'email',
	'value'     => ((isset($user->user_email))? htmlspecialchars_decode($user->user_email, ENT_QUOTES):''),
	'maxlength' => '200',
	'class'     => 'form-control input-sm'
);
$input_phone = array(
	'name'      => 'phone',
	'id'        => 'phone',
	'value'     => ((isset($user->user_phone))? htmlspecialchars_decode($user->user_phone, ENT_QUOTES):''),
	'maxlength' => '200',
	'class'     => 'form-control input-sm'
);
$input_mobile = array(
	'name'      => 'mobile',
	'id'        => 'mobile',
	'value'     => ((isset($user->user_mobile))? htmlspecialchars_decode($user->user_mobile, ENT_QUOTES):''),
	'maxlength' => '200',
	'class'     => 'form-control input-sm'
);
$input_birthdate = array(
	'name'      => 'birthdate',
	'id'        => 'birthdate',
	'value'     => ((isset($user->user_birthdate_format) && _dateNotEmpty($user->user_birthdate))? $user->user_birthdate_format:''),
	'maxlength' => '10',
	'size'      => '12',
	'class'     => 'form-control form-date input-sm'
);
$input_login = array(
	'name'      => 'login',
	'id'        => 'login',
	'value'     => ((isset($user->user_login))? htmlspecialchars_decode($user->user_login, ENT_QUOTES):''),
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
$input_licence = array(
	'name'      => 'licence',
	'id'        => 'licence',
	'value'     => ((isset($user->user_licence))? $user->user_licence:''),
	'maxlength' => '50',
	'class'     => 'form-control input-sm'
);

$ckb_referee = array(
	'name'      => 'isreferee',
	'id'        => 'isreferee',
	'value'     => 1,
	'class'     => '',
	'checked'   => ((isset($user->user_isreferee) && $user->user_isreferee == 1)? true:false)
);
$ckb_active = array(
	'name'      => 'active',
	'id'        => 'active',
	'value'     => 1,
	'class'     => '',
	'checked'   => ((isset($user->user_active) && $user->user_active == 1 || ! isset($user))? true:false)
);
// dropdowns
$options_clubs = $clubs;
$default_club = ((isset($user->user_club))? $user->user_club:'');

$options_referee_degree = $referee_degrees;
$default_referee_degree = ((isset($user->user_referee_degree))? $user->user_referee_degree:'');
?>
<h1><?php echo $title; ?></h1>

<?php echo $message; ?>

<?php
echo form_open(site_url('utilisateurs/enregistrer'), 'class="form-horizontal" role="form"')
	. form_hidden('user_id', $user_id);
?>
<fieldset class="col-sm-6">

	<legend>Informations utilisateur</legend>

	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Nom Prénom *</label>
		<div class="col-sm-9">
			<?php echo form_input($input_name); ?>
			<span class="help-block"><?php echo img(_img_tip_props('')); ?>Champ obligatoire.</span>
			<?php echo form_error('name'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="address" class="col-sm-3 control-label">Adresse</label>
		<div class="col-sm-9">
			<?php echo form_textarea($input_address); ?>
			<?php echo form_error('address'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="email" class="col-sm-3 control-label">Email</label>
		<div class="col-sm-9">
			<?php echo form_input($input_email); ?>
			<?php echo form_error('email'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="mobile" class="col-sm-3 control-label">Tél. portable</label>
		<div class="col-sm-9">
			<?php echo form_input($input_mobile); ?>
			<?php echo form_error('mobile'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="phone" class="col-sm-3 control-label">Tél. fixe</label>
		<div class="col-sm-9">
			<?php echo form_input($input_phone); ?>
			<?php echo form_error('phone'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="birthdate" class="col-sm-3 control-label">Date de naissance</label>
		<div class="col-sm-9">
			<?php echo form_input($input_birthdate); ?>
			<?php echo form_error('birthdate'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="licence" class="col-sm-3 control-label">Licence</label>
		<div class="col-sm-9">
			<?php echo form_input($input_licence); ?>
			<?php echo form_error('licence'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="login" class="col-sm-3 control-label">Identifiant</label>
		<div class="col-sm-9">
			<?php echo form_input($input_login); ?>
			<span class="help-block"><?php echo img(_img_tip_props('')); ?>Indiquez
				l'identifiant de l'utilisateur s'il doit se connecter sur le site.</span>
			<?php echo form_error('login'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="pwd" class="col-sm-3 control-label">Mot de passe</label>
		<div class="col-sm-9">
			<?php echo form_input($input_pwd); ?>
			<span class="help-block"><?php echo img(_img_tip_props('')); ?>Indiquez
				au minimum 6 caractères.<br>Caractères acceptés : a-z (majuscules et minuscules),
				0-9, caractères spéciaux suivants : !:-_%&<br>
				Le mot de passe n'est pas obligatoire pour la modification de l'utilisateur
				ou pour un utilisateur qui ne se connecte pas sur le site.</span>
			<?php echo form_error('pwd'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="club" class="col-sm-3 control-label">Club</label>
		<div class="col-sm-9">
			<?php echo form_dropdown('club', $options_clubs, $default_club, 'id="club" class="form-control input-sm"'); ?>
			<?php echo form_error('club'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="isreferee" class="col-sm-3 control-label">Arbitre</label>
		<div class="col-sm-9">
			<?php echo form_checkbox($ckb_referee); ?>
			<span class="help-block"><?php echo img(_img_tip_props('')); ?>Cochez
				si l'utilisateur est arbitre.</span>
			<?php echo form_error('isreferee'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="referee_degree" class="col-sm-3 control-label">Degré d'arbitrage</label>
		<div class="col-sm-9">
			<?php echo form_dropdown('referee_degree', $options_referee_degree, $default_referee_degree, 'id="referee_degree" class="form-control input-sm"'); ?>
			<span class="help-block"><?php echo img(_img_tip_props('')); ?>Si l'utilisateur
				est arbitre, indiquez son niveau d'arbitrage.</span>
			<?php echo form_error('referee_degree'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-sm-3 control-label">Actif</label>
		<div class="col-sm-9">
			<?php echo form_checkbox($ckb_active); ?>
			<?php echo form_error('active'); ?>
		</div>
	</div>

</fieldset>

<fieldset class="col-sm-6">
	<legend>Profils et droits d'accès</legend>

	<div class="form-group">
		<label for="" class="col-sm-3 control-label">Profils</label>
		<div class="col-sm-9">
		<?php
		foreach($profiles as $profile_id => $profile_name):
			$pattern = array(
				'name'     => 'ckb_prf[]',
				'id'       => 'prf_' . $profile_id,
				'checked'  => ((isset($user->profiles) && array_key_exists($profile_id, $user->profiles))? true:false),
				'value'    => $profile_id
			); ?>
			<div class="checkbox">
				<label><?php echo form_checkbox($pattern) . $profile_name; ?></label>
			</div>
		<?php endforeach; ?>
			<?php echo form_error('ckb_prf[]'); ?>
		</div>
	</div>

	<div class="form-group">
		<label for="" class="col-sm-3 control-label">Responsable d'équipe</label>
		<div class="col-sm-9">
			<?php /*if( ! isset($user->profiles) || ! array_key_exists(6, $user->profiles)): ?>
				<span class="help-block">Pour pouvoir sélectionner une équipe vous devez cocher le profil "responsable d'équipe", ci-dessus.</span>
			<?php endif;*/ ?>
			<div  id="team-list">
			<?php if (! isset($teams)): ?>
				<span class="help-block">Sélectionnez un club dans la liste à gauche.</span>
			<?php else: ?>
				<?php foreach($teams as $team_id => $team_name):
					$pattern = array(
						'name'      => 'ckb_team[]',
						'checked'   => ((isset($user->teams) && array_key_exists($team_id, $user->teams))? true:false),
						'value'     => $team_id
					); ?>
					<div class="checkbox">
						<label><?php echo form_checkbox($pattern) . $team_name; ?></label>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
			</div>
			<?php echo form_error('ckb_team[]'); ?>
		</div>
	</div>

</fieldset>

<?php $js = 'onclick="location.href=\'' . site_url('utilisateurs/liste') . '\';"';
echo form_submit('submitform', 'Enregistrer', 'class="btn btn-primary btn-sm"') . "\n"
	. form_button('cancel', 'Retour à la liste sans enregistrer', $js . ' class="btn btn-default btn-sm"') . "\n"; ?>

<?php echo form_close(); ?>

<script type="text/javascript" src="<?php echo base_url(). CRH_PATH_TO_JS; ?>jquery.ui.datepicker-fr.js"></script>
<script type="text/javascript">
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional["fr"]);
		$("#birthdate").datepicker({
			showOn: "both",
			buttonImage: "<?php echo base_url(). CRH_PATH_TO_IMG; ?>calendar.png",
			buttonImageOnly: true,
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat: "dd/mm/yy",
			showAnim: "slideDown"
		});
	});
/*
	$("#prf_6").click(function() {
		if($("#prf_6").prop( "checked" ) == true) {
			// if checked, enabled ckb_team
			//$("ckb_team").prop("disabled", false);
			//var elt = document.getElementsByName('ckb_team');
			//console.log(elt);
			$("ckb_team").each(function(name) {
				//$("ckb_team").removeAttr("disabled");
				//$(this).prop("disabled", false);
				console.log(name);
				//
			});
			 $("[name='ckb_team']").each(function(idx) {
				 console.log('merde');
			 });

		} else {
			// if unchecked, disabled ckb_team
			//$("ckb_team").prop("disabled", true);
			//$(this).prop("disabled", true);
		}
	});*/

	$("#club").change(function() {
		var $team_list = $('#team-list');
		$team_list.html('');
		$.post("<?php echo site_url('equipes/liste-par-club'); ?>",
			{ data: $(this).val() },
			function(json) {
				$.each(json, function(index, value) {
					var buffer = '<div class="checkbox"><label>'
								+ '<input type="checkbox" name="ckb_team[]"'
								+ ' value="' + index + '">'
								+ value +'</label></div>';
					$team_list.append(buffer);
                });
			},
			'json'
		);
	});
</script>