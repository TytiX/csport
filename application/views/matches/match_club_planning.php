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
 * match list : club planning view
 *
 * @version $Id: match_club_planning.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */


$options_action = $actions;

?>

<?php
if( empty($matches)):
	echo '<p>Aucun match ne correspond à votre recherche.</p>';
else: ?>
	<div class="container">
		<div class="col-sm-3 block-btn"><a href="<?php echo site_url('club/exporter'); ?>">
			<button type="button" class="btn btn-default btn-xs action-btn"><?php echo img(_img_file_download_props('Exporter')); ?>Exporter la liste</button>
			</a>
		</div>
		<div class=" col-sm-offset-6 col-sm-3 block-btn"><a href="javascript:window.print();">
				<button type="button" class="btn btn-default btn-xs action-btn"><?php echo img(_img_print_props()); ?>Imprimer la liste</button>
			</a>
		</div>
	</div>

<?php
	echo $pagination;
?>
	<form name="form_planning" action="<?php echo site_url('club/enregistrer-matches'); ?>" method="post" role="form"  class="form-inline">

	<div class="container">
		<div class="col-sm-3 block-btn"><a href="<?php echo site_url('nouveau-match'); ?>">
				<button type="button" class="btn btn-success btn-xs action-btn"><?php echo img(_img_add_props()); ?>Ajouter un match</button>
			</a>
		</div>

		<div class=" col-sm-offset-6 col-sm-3 block-btn ">
			<button type="submit" class="btn btn-default btn-xs action-btn"><?php echo img(_img_save_props()); ?>Enregistrer les modifications</button>
		</div>

	</div>

	<div class="table-responsive">
		<table class="table table-striped table-hover">
		<tr>
			<th></th>
			<th>Date<span class="inline"><?php
				echo anchor(site_url('club/tri-matches/date-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('club/tri-matches/date-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Heure</th>
			<th>Lieu</th>
			<th>Compétition<span class="inline"><?php
				echo anchor(site_url('club/tri-matches/cs-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('club/tri-matches/cs-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Catégorie<span class="inline"><?php
				echo anchor(site_url('club/tri-matches/cat-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('club/tri-matches/cat-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Equipe 1<span class="inline"><?php
				echo anchor(site_url('club/tri-matches/equ1-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('club/tri-matches/equ1-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Equipe 2<span class="inline"><?php
				echo anchor(site_url('club/tri-matches/equ2-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('club/tri-matches/equ2-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Arbitre 1<span class="inline"><?php
				echo anchor(site_url('club/tri-matches/referee1-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('club/tri-matches/referee1-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Arbitre 2<span class="inline"><?php
				echo anchor(site_url('club/tri-matches/referee2-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('club/tri-matches/referee2-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
		</tr>
<?php foreach ($matches as $match):
		if($match->team1_club_id == $current_club):
			$receiving_team = true;
		else:
			$receiving_team = false;
		endif;
?>
		<tr>
			<td><?php if($receiving_team): ?>
				<a href="<?php echo site_url('convocation-match-' . $match->match_id); ?>">
					<button type="button" class="btn btn-warning btn-xs action-btn"><?php echo img(_img_email_props()); ?></button>
				</a>
				<?php endif; ?>
			</td>
			<td><div class="form-group"><?php
			if($receiving_team):
				$date_pattern = array(
					'name'      => 'match_date[' . $match->match_id . ']',
					'id'        => 'match_date_' . $match->match_id,
					'value'     => $match->match_date_format,
					'maxlength' => '10',
					'size'      => '8',
					'class'     => 'form-control form-date input-sm'
				);
				echo form_input($date_pattern);
			else:
				echo $match->match_date_format;
			endif; ?></div></td>
			<td><div class="form-group"><?php
			if($receiving_team):
				$time_pattern = array(
					'name'      => 'match_time[' . $match->match_id . ']',
					'id'        => 'match_time_' . $match->match_id,
					'value'     => $match->match_time_format,
					'maxlength' => '5',
					'size'      => '4',
					'class'     => 'form-control input-sm'
				);
				echo form_input($time_pattern);
			else:
				echo $match->match_time_format;
			endif; ?></div></td>
			<td><?php if($receiving_team): ?>
				<div class="form-group"><?php echo form_dropdown('match_place[' . $match->match_id . ']', $places, $match->place_id, 'class="form-control input-sm"'); ?></div>
			<?php else: ?>
				<?php echo $match->place_name; ?>
			<?php endif; ?>
			</td>
			<td><?php echo $match->cs_name; ?></td>
			<td><?php echo $match->category_name; ?></td>
			<td><?php echo $match->team1_name; ?> <span class="status status_<?php echo $match->match_team1_status; ?>">(<?php echo $match->match_team1_status; ?>)</span>
			<?php
				if(array_key_exists($match->match_team1, $teams)) {
					$default_action = $match->match_team1_status;
					echo form_dropdown('match_team1_status[' . $match->match_id . ']', $options_action, $default_action, 'class="form-control input-sm"');
					echo form_hidden('match_team1_previous_status[' . $match->match_id . ']', $default_action);
				}
			?></td>
			<td><?php echo $match->team2_name; ?> <span class="status status_<?php echo $match->match_team2_status; ?>">(<?php echo $match->match_team2_status; ?>)</span>
			<?php
				if(array_key_exists($match->match_team2, $teams)) {
					$default_action = $match->match_team2_status;
					echo form_dropdown('match_team2_status[' . $match->match_id . ']', $options_action, $default_action, 'class="form-control input-sm"');
					echo form_hidden('match_team2_previous_status[' . $match->match_id . ']', $default_action);
				}
			?></td>
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
		</tr>
<?php
	endforeach;
?>
		</table>
	</div>
	
	<div class="container">
		<div class=" col-sm-offset-9 col-sm-3 block-btn ">
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
