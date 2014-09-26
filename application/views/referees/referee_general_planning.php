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
 * refereeing list : general planning view
 *
 * @version $Id: referee_general_planning.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */


$options_action = $status;


if( empty($matches)):
	echo '<p>Aucun match ne correspond à votre recherche.</p>';
else: 
	echo $pagination;
?>
	<form name="form_planning" action="<?php echo site_url('planning-arbitrage/enregistrer'); ?>" method="post" role="form"  class="form-inline">

	<div class="container">
		<div class="col-sm-3 block-btn"><a href="<?php echo site_url('planning-arbitrage/exporter'); ?>">
			<button type="button" class="btn btn-default btn-xs action-btn"><?php echo img(_img_file_download_props('Exporter')); ?>Exporter la liste</button>
			</a>
		</div>
		
		<div class=" col-sm-offset-6 col-sm-3 block-btn ">
			<button type="submit" class="btn btn-default btn-xs action-btn"><?php echo img(_img_save_props()); ?>Enregistrer les modifications</button>
		</div>
	</div>

	<div class="table-responsive">
		<table class="table table-striped table-hover">
		<tr>
			<th>Date<span class="inline"><?php
				echo anchor(site_url('planning-arbitrage/tri/date-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('planning-arbitrage/tri/date-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Heure</th>
			<th>Lieu</th>
			<th>Catégorie<span class="inline"><?php
				echo anchor(site_url('planning-arbitrage/tri/cat-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('planning-arbitrage/tri/cat-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Equipe 1<span class="inline"><?php
				echo anchor(site_url('planning-arbitrage/tri/equ1-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('planning-arbitrage/tri/equ1-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
			<th>Equipe 2<span class="inline"><?php
				echo anchor(site_url('planning-arbitrage/tri/equ2-asc'), _img_order_asc_props(), array('class' => 'up-down'));?><?php
				echo anchor(site_url('planning-arbitrage/tri/equ2-desc'), _img_order_desc_props(), array('class' => 'up-down'));?></span></th>
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
				$default_referee = 'u;' . $match->match_referee1_club . ';' . $match->match_referee1;
			elseif( ! empty($match->match_referee1_club)):
				$default_referee = 'c;' . $match->match_referee1_club . ';0';
			else:
				$default_referee = '';
			endif;
			echo form_dropdown('referee1[' . $match->match_id . ']', $assign_referees, $default_referee, 'class="form-control input-sm"');
			echo form_hidden('previous_referee1[' . $match->match_id . ']', $default_referee);
			if( ! empty($default_referee)):
				echo '<span class="status status_' . $match->match_referee1_status . '">(' . $match->match_referee1_status . ')</span>';
			endif;
			?></td>
			<td><?php
			echo form_dropdown('referee1_status[' . $match->match_id . ']', $options_action, $match->match_referee1_status, 'class="form-control input-sm"');
			?></td>
			<td><?php
			if( ! empty($match->referee2_name)):
				$default_referee = 'u;' . $match->match_referee2_club . ';' . $match->match_referee2;
			elseif( ! empty($match->match_referee2_club)):
				$default_referee = 'c;' . $match->match_referee2_club . ';0';
			else:
				$default_referee = '';
			endif;
			echo form_dropdown('referee2[' . $match->match_id . ']', $assign_referees, $default_referee, 'class="form-control input-sm"');
			echo form_hidden('previous_referee2[' . $match->match_id . ']', $default_referee);
			if( ! empty($default_referee)):
				echo '<span class="status status_' . $match->match_referee2_status . '">(' . $match->match_referee2_status . ')</span>';
			endif;
			?></td>
			<td><?php
			echo form_dropdown('referee2_status[' . $match->match_id . ']', $options_action, $match->match_referee2_status, 'class="form-control input-sm"');
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
<?php echo $pagination; ?>
	<div class="alert alert-info">Légende pour les équipes et les arbitres :<br>
		C = convoqué<br>
		OK = confirmé<br>
		R = reporté (équipe)<br>
		F = Forfait (équipe)<br>
		I = Indisponible (arbitre)
	</div>
<?php endif; ?>
