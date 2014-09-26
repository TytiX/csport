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
 * user details
 *
 * @version $Id: user_detail.php 65 2014-03-03 09:01:25Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

?>
<fieldset class="col-sm-6">

	<legend><?php echo $user->user_name; ?></legend>

	<?php echo $message; ?>

	<p class="block-btn"><a href="<?php echo site_url('utilisateurs/recherche'); ?>"><button type="button" class="btn btn-default btn-xs action-btn"><?php echo img(_img_back_props()); ?>Retour à la liste</button></a>
		<a href="<?php echo site_url('utilisateurs/modifier-' . $user->user_id); ?>"><button type="button" class="btn btn-primary btn-xs action-btn"><?php echo img(_img_edit_props()); ?>Modifier l'utilisateur</button></a>
	</p>

	<table class="table table-responsive">
		<tr>
			<td>Adresse</td>
			<td><?php echo $user->user_address; ?></td>
		</tr>
		<tr>
			<td>Email</td>
			<td><?php echo $user->user_email; ?></td>
		</tr>
		<tr>
			<td>Tél. portable</td>
			<td><?php echo $user->user_mobile; ?></td>
		</tr>
		<tr>
			<td>Tél. fixe</td>
			<td><?php echo $user->user_phone; ?></td>
		</tr>
		<tr>
			<td>Date de naissance</td>
			<td><?php echo (_dateNotEmpty($user->user_birthdate)) ? $user->user_birthdate_format:''; ?></td>
		</tr>
		<tr>
			<td>Licence</td>
			<td><?php echo $user->user_licence; ?></td>
		</tr>
		<tr>
			<td>Identifiant</td>
			<td><?php echo $user->user_login; ?></td>
		</tr>
		<tr>
			<td>Club</td>
			<td><?php echo $user->club_name; ?></td>
		</tr>
		<tr>
			<td>Arbitre</td>
			<td><?php echo ($user->user_isreferee == 1)? img(_img_checked_props()):''; ?></td>
		</tr>
		<tr>
			<td>Degré d'arbitrage</td>
			<td><?php echo $user->user_referee_degree; ?></td>
		</tr>
		<tr>
			<td>Actif</td>
			<td><?php echo ($user->user_active == 1)? img(_img_checked_props()):img(_img_uncheck_props()); ?></td>
		</tr>
	</table>

</fieldset>

<fieldset class="col-sm-6">

	<legend>Profils</legend>
	
	<table class="table table-responsive">
		<tr>
			<td>Profils</td>
			<td><?php
			foreach($profiles as $profile_id => $profile_name):
				$pattern = (array_key_exists($profile_id, $user->profiles)? img(_img_checked_props()):'');
				echo $pattern . $profile_name . '<br>';
			endforeach; ?>
			</td>
		</tr>
<?php if(array_key_exists(6, $user->profiles)): ?>
		<tr>
			<td>Responsable d'équipe</td>
			<td><?php
			foreach($teams as $team_id => $team_name):
				$pattern = (array_key_exists($team_id, $user->teams)? img(_img_checked_props()):'');
				echo $pattern . $team_name . '<br>';
			endforeach; ?></td>
		</tr>
<?php endif; ?>
	</table>

</fieldset>
