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
 * users list
 *
 * @version $Id: user_list.php 89 2014-09-20 09:36:14Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

// search fields
$input_name = array(
	'name'      => 'f_name',
	'id'        => 'f_name',
	'value'     => (isset($filters['uname'])? $filters['uname']:''),
	'maxlength' => '200',
	'class'     => 'form-control input-sm'
);
$options_clubs = $clubs;
$default_club = (isset($filters['club'])? $filters['club']:'');

$options_isreferee = array('all' => 'Tous', 1 => 'oui', 0 => 'non');
$default_isreferee = (isset($filters['ureferee'])? $filters['ureferee']:'');

$options_active = array('all' => 'Tous', 1 => 'oui', 0 => 'non');
$default_active = (isset($filters['uactive'])? $filters['uactive']:'');

?>


<h1>Liste des utilisateurs</h1>

<p class="block-btn"><a href="<?php echo site_url('utilisateurs/nouveau'); ?>">
		<button type="button" class="btn btn-success btn-xs action-btn"><?php echo img(_img_add_props()); ?>Créer un utilisateur</button>
	</a>
</p>

<?php echo $message; ?>

<!-- ---------------------------------------------------------------------------
	filters -->
<div class="col-sm-12">
	<?php echo form_open(site_url('utilisateurs/recherche'), 'class="form-inline" role="form"'); ?>
	<fieldset class="filters">
	  <legend><span id="show"><?php echo img(_img_search_props()); ?>Recherche<button type="button" class="btn btn-link"><?php echo _img_order_desc_props(); ?></button></span>
		  <span id="hide"><?php echo img(_img_search_props()); ?>Recherche<button type="button" class="btn btn-link"><?php echo _img_order_asc_props(); ?></button></span>
	  </legend>

	  <div class="filter-content">
			<div class="form-group">
			<label for="f_name">Nom</label>
			<?php echo form_input($input_name); ?>
		</div>
		<div class="form-group">
			<label for="f_club">Club</label>
			<?php echo form_dropdown('f_club', $options_clubs, $default_club, 'class="form-control input-sm" id="f_club"'); ?>
		</div>
		<div class="form-group">
			<label for="f_isreferee">Arbitre</label>
			<?php echo form_dropdown('f_isreferee', $options_isreferee, $default_isreferee, 'class="form-control input-sm" id="f_isreferee"'); ?>
		</div>
		<div class="form-group">
			<label for="f_isreferee">Utilisateur activé</label>
			<?php echo form_dropdown('f_active', $options_active, $default_active, 'class="form-control input-sm" id="f_active"'); ?>
		</div>

		<input type="submit" class="btn btn-primary btn-sm" name="submitform" value="Chercher">
		<input type="button" class="btn btn-default btn-sm" name="raz" value="Tout voir" onclick="javascript:location.href='<?php echo site_url('utilisateurs/liste'); ?>';">
	  </div>
	</fieldset>
<?php echo form_close(); ?>
</div>


<?php
if( empty($users)):
	echo '<p>Aucun utilisateur ne correspond à votre recherche.</p>';
else: ?>
	<div class="col-sm-3 block-btn">
		<a href="<?php echo site_url('exporter-utilisateurs'); ?>">
			<button type="button" class="btn btn-default btn-xs action-btn"><?php echo img(_img_file_download_props('Exporter')); ?>Exporter la liste</button>
		</a>
	</div>
	<div class=" col-sm-offset-6 col-sm-3 block-btn">
		<a href="<?php echo site_url('envoyer-mots-passe'); ?>">
			<button type="button" class="btn btn-warning btn-xs action-btn"><?php echo img(_img_email_props()); ?>Envoyer les mots de passe</button>
		</a>
	</div>
<?php
	echo $pagination;
?>
	<div class="table-responsive col-sm-12">
		<table class="table table-striped table-hover">
		<tr>
			<th>Nom Prénom<span class="inline"><?php
				echo anchor(site_url('utilisateurs/tri/name-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('utilisateurs/tri/name-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Email</th>
			<th>Téléphones</th>
			<th>Club<span class="inline"><?php
				echo anchor(site_url('utilisateurs/tri/club-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('utilisateurs/tri/club-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Arbitre</th>
			<th>Degré d'arbitrage</th>
			<th colspan="2" class="action"></th>
		</tr>
<?php foreach ($users as $user): ?>
		<tr>
			<td><?php echo $user->user_name; ?></td>
			<td><?php echo $user->user_email; ?></td>
			<td><?php echo $user->user_phone; ?><br><?php echo $user->user_mobile; ?></td>
			<td><?php echo $user->club_name; ?></td>
			<td><?php echo (($user->user_isreferee == 1)? 'O':'N'); ?></td>
			<td>
			<?php echo $user->user_referee_degree; ?>
			</td>
			<td class="action">
		<?php if(Permissions_model::userHasAccess('user_detail', false)): ?>
			<?php echo anchor('utilisateurs/fiche-' . $user->user_id, img(_img_details_props())); ?>
		<?php endif; ?>
			</td>
			<td class="action">
		<?php if(Permissions_model::userHasAccess('user_edit', false)): ?>
			<?php echo anchor('utilisateurs/modifier-' . $user->user_id, img(_img_edit_props())); ?>
		<?php endif; ?>
			</td>
		</tr>
<?php
	endforeach;
?>
		</table>
	</div>
<?php
	echo $pagination;
endif;
?>


<script type="text/javascript">

	$("#show").click(function() {
		$(".filter-content").show(400);
		$(this).hide();
		$("#hide").show();
	});
	$("#hide").click(function() {
		$(".filter-content").hide(400);
		$(this).hide();
		$("#show").show();
	});

</script>