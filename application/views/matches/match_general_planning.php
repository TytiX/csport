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
 * match list : general planning view
 *
 * @version $Id: match_general_planning.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */


// search fields
$input_date_from = array(
	'name'      => 'fp_date_from',
	'id'        => 'fp_date_from',
	'value'     => ((isset($filters['date_from']) && _dateNotEmpty($filters['date_from']))? $filters['date_from']:''),
	'maxlength' => '10',
	'size'      => '12',
	'class'     => 'form-control form-date input-sm'
);
$input_date_to = array(
	'name'      => 'fp_date_to',
	'id'        => 'fp_date_to',
	'value'     => ((isset($filters['date_to']) && _dateNotEmpty($filters['date_to']))? $filters['date_to']:''),
	'maxlength' => '10',
	'size'      => '12',
	'class'     => 'form-control form-date input-sm'
);

$options_clubs = $clubs;
$default_club = (isset($filters['club'])? $filters['club']:'');
$options_cat = $categories;
$default_cat = (isset($filters['cat'])? $filters['cat']:'');
$options_cs = $cs;
$default_cs = (isset($filters['cs'])? $filters['cs']:'');
$options_referee = $club_referee;
$default_referee = (isset($filters['club_referee'])? $filters['club_referee']:'');

?>

<h1>Gestion des matches</h1>

<?php echo $message; ?>
	
<!-- ---------------------------------------------------------------------------
	filters -->
<div class="col-sm-12">
	<?php echo form_open(site_url('planning/recherche-matches'), 'class="form-inline" role="form"'); ?>
	<fieldset class="filters">
		<legend><span id="show"><?php echo img(_img_search_props()); ?>Recherche<button type="button" class="btn btn-link"><?php echo _img_order_desc_props(); ?></button></span>
		  <span id="hide"><?php echo img(_img_search_props()); ?>Recherche<button type="button" class="btn btn-link"><?php echo _img_order_asc_props(); ?></button></span>
		</legend>

		<div class="filter-content">
			<div class="form-group">
			  <label for="fp_club">Club</label>
			  <?php echo form_dropdown('fp_club', $options_clubs, $default_club, 'class="form-control input-sm" id="fp_club"'); ?>
			</div>

			<div class="form-group">
			  <label for="fp_cat">Catégorie</label>
			  <?php echo form_dropdown('fp_cat', $options_cat, $default_cat, 'class="form-control input-sm" id="fp_cat"'); ?>
			</div>

			<div class="form-group">
			  <label for="fp_cs">Compétition</label>
			  <?php echo form_dropdown('fp_cs', $options_cs, $default_cs, 'class="form-control input-sm" id="fp_cs"'); ?>
			</div>

			<div class="form-group">
			  <label for="fp_referee">Club arbitre</label>
			  <?php echo form_dropdown('fp_referee', $options_referee, $default_referee, 'class="form-control input-sm" id="fp_referee"'); ?>
			</div>

			<div class="form-group">
			  <label for="fp_date_from">Matches entre le</label>
			  <?php echo form_input($input_date_from); ?>
			</div>

			<div class="form-group">
			  <label for="fp_date_to">et le</label>
			  <?php echo form_input($input_date_to); ?>
			</div>

			<input type="submit" class="btn btn-primary btn-sm" name="submitform" value="Chercher">
			<input type="button" class="btn btn-default btn-sm" name="raz" value="Tout voir" onclick="javascript:location.href='<?php echo site_url('planning/gestion-matches'); ?>';">
		</div>
	</fieldset>
<?php echo form_close(); ?>
</div>

<?php
if( empty($matches)):
	echo '<p>Aucun match ne correspond à votre recherche.</p>';
else: ?>

	<div class="container">
		<div class="col-sm-3 block-btn"><a href="<?php echo site_url('planning/exporter'); ?>">
			<button type="button" class="btn btn-default btn-xs action-btn"><?php echo img(_img_file_download_props('Exporter')); ?>Exporter la liste</button>
			</a>
		</div>
		<div class=" col-sm-offset-2 col-sm-3 block-btn"><a href="javascript:window.print();">
				<button type="button" class="btn btn-default btn-xs action-btn"><?php echo img(_img_print_props()); ?>Imprimer la liste</button>
			</a>
		</div>
		<div class=" col-sm-offset-2 col-sm-2 block-btn "><a href="<?php echo site_url('importer-matches'); ?>">
				<button type="button" class="btn btn-default btn-xs action-btn"><?php echo img(_img_file_upload_props()); ?>Importer un fichier</button>
			</a>
		</div>
	</div>

<?php
	echo $pagination;
?>
	<form name="form_planning" action="<?php echo site_url('planning/enregistrer-matches'); ?>" method="post" role="form"  class="form-inline">

	<div class="container">
		<div class="col-sm-3 block-btn"><a href="<?php echo site_url('nouveau-match'); ?>">
				<button type="button" class="btn btn-success btn-xs action-btn"><?php echo img(_img_add_props()); ?>Ajouter un match</button>
			</a>
		</div>

		<div class="col-sm-offset-2 col-sm-3 block-btn"><a href="<?php echo site_url('convocation-generale'); ?>">
				<button type="button" class="btn btn-warning btn-xs action-btn"><?php echo img(_img_email_props()); ?>Envoyer les convocations</button>
			</a>
		</div>

		<div class=" col-sm-offset-2 col-sm-2 block-btn">
			<button type="submit" class="btn btn-default btn-xs action-btn"><?php echo img(_img_save_props()); ?>Enregistrer les modifications</button>
		</div>
	</div>


	<div class="table-responsive">
		<table class="table  table-striped table-hover">
		<tr>
			<th>#</th>
			<th>Date/heure<span class="inline"><?php
				echo anchor(site_url('planning/tri-matches/date-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('planning/tri-matches/date-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Lieu</th>
			<th>Compétition<span class="inline"><?php
				echo anchor(site_url('planning/tri-matches/cs-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('planning/tri-matches/cs-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Catégorie<span class="inline"><?php
				echo anchor(site_url('planning/tri-matches/cat-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('planning/tri-matches/cat-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Equipe 1<span class="inline"><?php
				echo anchor(site_url('planning/tri-matches/equ1-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('planning/tri-matches/equ1-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Equipe 2<span class="inline"><?php
				echo anchor(site_url('planning/tri-matches/equ2-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('planning/tri-matches/equ2-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Arbitre 1<span class="inline"><?php
				echo anchor(site_url('planning/tri-matches/referee1-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('planning/tri-matches/referee1-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Arbitre 2<span class="inline"><?php
				echo anchor(site_url('planning/tri-matches/referee2-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('planning/tri-matches/referee2-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th></th>
		</tr>
<?php foreach ($matches as $match): ?>
		<tr>
			<td><?php echo $match->match_id; ?><br><br>
				<a href="<?php echo site_url('convocation-match-' . $match->match_id); ?>">
					<button type="button" class="btn btn-warning btn-xs action-btn"><?php echo img(_img_email_props()); ?></button>
				</a>
			</td>
			<td><div class="form-group"><?php
				$date_pattern = array(
					'name'      => 'match_date[' . $match->match_id . ']',
					'id'        => 'match_date_' . $match->match_id,
					'value'     => $match->match_date_format,
					'maxlength' => '10',
					'size'      => '8',
					'class'     => 'form-control form-date input-sm'
				);
				echo form_input($date_pattern); ?></div>
				<div class="form-group"><?php
				$time_pattern = array(
					'name'      => 'match_time[' . $match->match_id . ']',
					'id'        => 'match_time_' . $match->match_id,
					'value'     => $match->match_time_format,
					'maxlength' => '5',
					'size'      => '4',
					'class'     => 'form-control input-sm'
				);
				echo form_input($time_pattern); ?></div></td>
			<td>
				<div class="form-group"><?php echo form_dropdown('match_place[' . $match->match_id . ']', $places, $match->place_id, 'class="form-control input-sm"'); ?></div>
			</td>
			<td>
				<div class="form-group"><?php echo form_dropdown('match_cs[' . $match->match_id . ']', $cs, $match->cs_id, 'class="form-control input-sm"'); ?></div>
			</td>
			<td>
				<div class="form-group"><?php echo form_dropdown('match_cat[' . $match->match_id . ']', $categories, $match->category_id, 'class="form-control input-sm"'); ?></div>
			</td>
			<td>
				<div class="form-group"><?php echo form_dropdown('match_team1[' . $match->match_id . ']', $teams, $match->match_team1, 'class="form-control input-sm"'); ?></div>
				<div class="form-group"><?php echo form_dropdown('match_team1_status[' . $match->match_id . ']', $status, $match->match_team1_status, 'class="form-control input-sm"'); ?></div>
			</td>
			<td><div class="form-group"><?php echo form_dropdown('match_team2[' . $match->match_id . ']', $teams, $match->match_team2, 'class="form-control input-sm"'); ?></div>
				<div class="form-group"><?php echo form_dropdown('match_team2_status[' . $match->match_id . ']', $status, $match->match_team2_status, 'class="form-control input-sm"'); ?></div>
			</td>
			<td><?php if( ! empty($match->referee1_name)):
				echo $match->referee1_name;
			else:
				echo $match->referee1_club_name;
			endif;
			if ( ! empty($match->referee1_name) || ! empty($match->referee1_club_name)):
				echo ' <span class="status status_' . $match->match_referee1_status . '">(' . $match->match_referee1_status . ')</span>';
			endif;
			?></td>
			<td><?php if( ! empty($match->referee2_name)):
				echo $match->referee2_name;
			else:
				echo $match->referee2_club_name;
			endif;
			if ( ! empty($match->referee2_name) || ! empty($match->referee2_club_name)):
				echo ' <span class="status status_' . $match->match_referee2_status . '">(' . $match->match_referee2_status . ')</span>';
			endif;
			?></td>
			<td><a href="#" onclick="javascript:ConfirmDelete(<?php echo $match->match_id; ?>);"><?php echo img(_img_del_props()); ?></a></td>
		</tr>
<?php
	endforeach;
?>
		</table>
	</div>
	
	<div class="container">
		<div class="col-sm-3 block-btn"><a href="<?php echo site_url('nouveau-match'); ?>">
			<button type="button" class="btn btn-success btn-xs action-btn"><?php echo img(_img_add_props()); ?>Ajouter un match</button>
			</a>
		</div>

		<div class=" col-sm-offset-7 col-sm-2 block-btn ">
			<button type="submit" class="btn btn-default btn-xs action-btn"><?php echo img(_img_save_props()); ?>Enregistrer les modifications</button>
		</div>
	</div>

	</form>
<?php
	echo $pagination; ?>
	<div class="alert alert-info">Légende pour les équipes et les arbitres :<br>
		C = convoqué<br>
		OK = confirmé<br>
		R = reporté (équipe)<br>
		F = Forfait (équipe)<br>
		I = Indisponible (arbitre)
	</div>
<?php
endif;
?>


<script type="text/javascript" src="<?php echo base_url(). CRH_PATH_TO_JS; ?>jquery.ui.datepicker-fr.js"></script>
<script type="text/javascript">
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional["fr"]);
		$(".form-date").datepicker({
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

	function ConfirmDelete(match_id) {
       if (confirm("Êtes-vous sûr de vouloir supprimer définitivement ce match ?")) {
           location.href="<?php echo base_url() . index_page(); ?>/planning/supprimer-match-" + match_id;
       }
   }
   
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