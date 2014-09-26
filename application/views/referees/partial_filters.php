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
 * partial view : filters for refereeing
 *
 * @version $Id: partial_filters.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

// search fields
$input_date_from = array(
	'name'      => 'f_date_from',
	'id'        => 'f_date_from',
	'value'     => ((isset($filters['date_from']) && _dateNotEmpty($filters['date_from']))? $filters['date_from']:''),
	'maxlength' => '10',
	'size'      => '12',
	'class'     => 'form-control form-date input-sm'
);
$input_date_to = array(
	'name'      => 'f_date_to',
	'id'        => 'f_date_to',
	'value'     => ((isset($filters['date_to']) && _dateNotEmpty($filters['date_to']))? $filters['date_to']:''),
	'maxlength' => '10',
	'size'      => '12',
	'class'     => 'form-control form-date input-sm'
);

$options_clubs = $clubs;
$default_club = (isset($filters['club'])? $filters['club']:'');
$options_cat = $categories;
$default_cat = (isset($filters['cat'])? $filters['cat']:'');
$options_club_referee = $club_referee;
$default_club_referee = (isset($filters['club_referee'])? $filters['club_referee']:'');
$options_referee = $referees;
$default_referee = (isset($filters['referee'])? $filters['referee']:'');

?>

<h1><?php echo $title; ?></h1>

<?php echo $message; ?>

<!-- ---------------------------------------------------------------------------
	filters -->
<div class="col-sm-12">
	<?php echo form_open($form_url, 'class="form-inline" role="form"'); ?>
	<fieldset class="filters">
		<legend><span id="show"><?php echo img(_img_search_props()); ?>Recherche<button type="button" class="btn btn-link"><?php echo _img_order_desc_props(); ?></button></span>
		  <span id="hide"><?php echo img(_img_search_props()); ?>Recherche<button type="button" class="btn btn-link"><?php echo _img_order_asc_props(); ?></button></span>
		</legend>

		<div class="filter-content">
			<div class="form-group">
			<label for="f_club">Club</label>
			<?php echo form_dropdown('f_club', $options_clubs, $default_club, 'class="form-control input-sm" id="f_club"'); ?>
		  </div>

		  <div class="form-group">
			<label for="f_cat">Catégorie</label>
			<?php echo form_dropdown('f_cat', $options_cat, $default_cat, 'class="form-control input-sm" id="f_cat"'); ?>
		  </div>

		  <div class="form-group">
			<label for="f_club_referee">Arbitre Club</label>
			<?php echo form_dropdown('f_club_referee', $options_club_referee, $default_club_referee, 'class="form-control input-sm" id="f_club_referee"'); ?>
		  </div>


		  <div class="form-group">
			<label for="f_referee">Arbitre</label>
			<?php echo form_dropdown('f_referee', $options_referee, $default_referee, 'class="form-control input-sm" id="f_referee"'); ?>
		  </div>

		  <div class="form-group">
			<label for="f_date_from">Matches entre le</label>
			<?php echo form_input($input_date_from); ?>
		  </div>

		  <div class="form-group">
			<label for="f_date_to">et le</label>
			<?php echo form_input($input_date_to); ?>
		  </div>

		  <input type="submit" class="btn btn-primary btn-sm" name="submitform" value="Chercher">
		  <input type="button" class="btn btn-default btn-sm" name="raz" value="Tout voir" onclick="javascript:location.href='<?php echo $form_raz; ?>';">
		</div>
	</fieldset>
<?php echo form_close(); ?>
</div>

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