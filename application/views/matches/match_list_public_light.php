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
 * match list : public simplified view
 *
 * @version $Id: match_list_public_light.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

?>

<?php
if( empty($matches)):
	echo '<p>Aucun match ne correspond à votre recherche.</p>';
else: ?>
	<div class="container">
		<div class="col-sm-3 block-btn"><a href="<?php echo site_url('exporter-matches'); ?>">
			<button type="button" class="btn btn-default btn-xs action-btn"><?php echo img(_img_file_download_props('Exporter')); ?>Exporter la liste</button>
			</a>
		</div>
		<div class=" col-sm-offset-2 col-sm-3 block-btn"><a href="javascript:window.print();">
				<button type="button" class="btn btn-default btn-xs action-btn"><?php echo img(_img_print_props()); ?>Imprimer la liste</button>
			</a>
		</div>
		<div class=" col-sm-offset-2 col-sm-2 block-btn "><a href="<?php echo site_url('matches/recherche-complete'); ?>">
				<button type="button" class="btn btn-default btn-xs action-btn">Vue complète</button>
			</a>
		</div>
	</div>
<?php
	echo $pagination;
?>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
		<tr>
			<th>Date</th>
			<th>Heure</th>
			<th>Lieu</th>
			<th>Catégorie</th>
			<th>Equipe 1</th>
			<th>Equipe 2</th>
		</tr>
<?php foreach ($matches as $match): ?>
		<tr>
			<td><?php echo $match->match_date_format; ?></td>
			<td><?php echo $match->match_time_format; ?></td>
			<td><?php echo $match->place_name; ?></td>
			<td><?php echo $match->category_name; ?></td>
			<td><?php echo $match->team1_name; ?> <span class="status status_<?php echo $match->match_team1_status; ?>">(<?php echo $match->match_team1_status; ?>)</span></td>
			<td><?php echo $match->team2_name; ?> <span class="status status_<?php echo $match->match_team2_status; ?>">(<?php echo $match->match_team2_status; ?>)</span></td>
		</tr>
<?php
	endforeach;
?>
		</table>
	</div>
<?php
	echo $pagination; ?>
	<div class="alert alert-info">Légende pour les équipes :<br>
		C = convoqué<br>
		OK = confirmé<br>
		R = reporté<br>
		F = Forfait
	</div>
<?php
endif;
?>


<script type="text/javascript" src="<?php echo base_url(). CRH_PATH_TO_JS; ?>jquery.ui.datepicker-fr.js"></script>
<script type="text/javascript">
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional["fr"]);
		$("#f_date_from, #f_date_to").datepicker({
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
</script>