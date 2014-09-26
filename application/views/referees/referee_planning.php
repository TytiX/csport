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
 * refereeing list : referee planning view
 *
 * @version $Id: referee_planning.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

$options_action = $status;

?>

<?php
if( empty($matches)):
	echo '<p>Aucun match ne correspond à votre recherche.</p>';
else: 
	echo $pagination;
?>
	<form name="form_planning" action="<?php echo site_url('arbitre/enregistrer'); ?>" method="post" role="form" class="form-inline">

	<div class="container">
		<div class=" col-sm-offset-9 col-sm-3 block-btn ">
			<button type="submit" class="btn btn-default btn-xs action-btn"><?php echo img(_img_save_props()); ?>Enregistrer les modifications</button>
		</div>
	</div>

	<div class="table-responsive">
		<table class="table table-striped table-hover">
		<tr>
			<th>Date<span class="inline"><?php
				echo anchor(site_url('arbitre/tri/date-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('arbitre/tri/date-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Heure</th>
			<th>Lieu</th>
			<th>Catégorie<span class="inline"><?php
				echo anchor(site_url('arbitre/tri/cat-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('arbitre/tri/cat-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Equipe 1<span class="inline"><?php
				echo anchor(site_url('arbitre/tri/equ1-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('arbitre/tri/equ1-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Equipe 2<span class="inline"><?php
				echo anchor(site_url('arbitre/tri/equ2-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('arbitre/tri/equ2-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Arbitre 1</th>
			<th>Statut arbitre 1</th>
			<th>Arbitre 2</th>
			<th>Statut arbitre 2</th>
		</tr>
<?php foreach ($matches as $match): ?>
		<tr>
			<td><?php echo $match->match_date_format; ?></td>
			<td><?php echo $match->match_time_format; ?></td>
			<td><?php echo $match->place_name; ?></td>
			<td><?php echo $match->category_name; ?></td>
			<td><?php echo $match->team1_name; ?> <span class="status status_<?php echo $match->match_team1_status; ?>">(<?php echo $match->match_team1_status; ?>)</span></td>
			<td><?php echo $match->team2_name; ?> <span class="status status_<?php echo $match->match_team2_status; ?>">(<?php echo $match->match_team2_status; ?>)</span></td>
			<td><?php
			if( ! empty($match->referee1_name)):
				echo $match->referee1_name . ( ! empty($match->referee1_club_name)? ' (' . $match->referee1_club_name . ')':"");
			elseif( ! empty($match->match_referee1_club)):
				echo $match->referee1_club_name;
			endif;
			?></td>
			<td><?php
			$already = false;
			if($match->match_referee1 == $current_user):
				echo form_dropdown('referee1_status[' . $match->match_id . ']', $options_action, $match->match_referee1_status, 'class="form-control input-sm"');
				echo form_hidden('previous_status_referee1[' . $match->match_id . ']', $match->match_referee1_status);
			elseif(empty($match->match_referee1) && ($match->match_referee1_club == $current_club) && ($match->match_referee2 != $current_user)):
				$ckb_pattern = array(
					'name'  => 'referee1_take[' . $match->match_id . ']',
					'id'    => 'take1_' . $match->match_id,
					'value' => 1,
				);
				echo form_checkbox($ckb_pattern) . " Prendre l'arbitrage";
				$already = true;
			else:
				if (isset($match->match_referee1_status)):
					echo $options_action[$match->match_referee1_status];
				endif;
			endif;
			?></td>
			<td><?php
			if( ! empty($match->referee2_name)):
				echo $match->referee2_name . ( ! empty($match->referee2_club_name)? ' (' . $match->referee2_club_name . ')':"");
			elseif( ! empty($match->match_referee2_club)):
				echo $match->referee2_club_name;
			endif;
			?></td>
			<td><?php
			if($match->match_referee2 == $current_user):
				echo form_dropdown('referee2_status[' . $match->match_id . ']', $options_action, $match->match_referee2_status, 'class="form-control input-sm"');
				echo form_hidden('previous_status_referee2[' . $match->match_id . ']', $match->match_referee2_status);
			elseif(empty($match->match_referee2) && ($match->match_referee2_club == $current_club) 
					&& ($match->match_referee1 != $current_user) && ! $already):
				$ckb_pattern = array(
					'name'  => 'referee2_take[' . $match->match_id . ']',
					'id'    => 'take2_' . $match->match_id,
					'value' => 1,
				);
				echo form_checkbox($ckb_pattern) . " Prendre l'arbitrage";
			else:
				if (isset($match->match_referee2_status)):
					echo $options_action[$match->match_referee2_status];
				endif;
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
	echo $pagination;
?>
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
