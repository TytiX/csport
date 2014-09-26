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
 * match form (to add or edit a match)
 *
 * @version $Id: match_form.php 65 2014-03-03 09:01:25Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

//-------------------------------------
// fields definition
$input_date = array(
	'name'      => 'date',
	'id'        => 'date',
	'value'     => ((isset($match->match_date_format) && _dateNotEmpty($match->match_date_format))? $match->match_date_format:''),
	'maxlength' => '10',
	'class'     => 'form-control form-date input-sm'
);
$input_time = array(
	'name'      => 'time',
	'id'        => 'time',
	'value'     => ((isset($match->match_time_format) && _dateNotEmpty($match->match_time_format))? $match->match_time_format:''),
	'maxlength' => '5',
	'size'      => '4',
	'class'     => 'form-control input-sm'
);
$input_comments = array(
	'name'      => 'comments',
	'id'        => 'comments',
	'value'     => ((isset($match->match_comments))? htmlspecialchars_decode($match->match_comments, ENT_QUOTES):''),
	'cols'      => 25,
	'rows'      => 3,
	'class'     => 'form-control input-sm'
);
?>

<h1><?php echo $title; ?></h1>

<?php echo $message; ?>

<?php
echo form_open(site_url('enregistrer-match'), 'class="form-horizontal" role="form"')
	. form_hidden('match_id', $match_id);
?>
<fieldset class="col-sm-8">

	<div class="form-group">
		<label for="date" class="col-sm-3 control-label">Date</label>
		<div class="col-sm-9">
			<?php echo form_input($input_date); ?>
			<?php echo form_error('date'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="time" class="col-sm-3 control-label">Heure</label>
		<div class="col-sm-9">
			<?php echo form_input($input_time); ?>
			<span class="help-block"><?php echo img(_img_tip_props('')); ?>Entrez l'heure sous le format HHhMM (par exemple : 09h00, 14h30...).</span>
			<?php echo form_error('time'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-sm-3 control-label">Lieu</label>
		<div class="col-sm-9">
			<?php echo form_dropdown('place', $places, (isset($match->match_place_id)? $match->match_place_id:''), 'id="place" class="form-control input-sm"'); ?>
			<?php echo form_error('place'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="cs" class="col-sm-3 control-label">Compétition</label>
		<div class="col-sm-9">
			<?php echo form_dropdown('cs', $cs, (isset($match->match_cs_id)? $match->match_cs_id:''), 'id="cs" class="form-control input-sm"'); ?>
			<span class="help-block"><?php echo img(_img_tip_props('')); ?>Champ obligatoire.</span>
			<?php echo form_error('cs'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="cat" class="col-sm-3 control-label">Catégorie</label>
		<div class="col-sm-9">
			<?php echo form_dropdown('cat', $categories, (isset($match->match_category_id)? $match->match_category_id:''), 'id="cat" class="form-control input-sm"'); ?>
			<span class="help-block"><?php echo img(_img_tip_props('')); ?>Champ obligatoire.</span>
			<?php echo form_error('cat'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="team1" class="col-sm-3 control-label">Equipe 1</label>
		<div class="col-sm-9">
			<?php echo form_dropdown('team1', $teams, (isset($match->match_team1)? $match->match_team1:''), 'id="team1" class="form-control input-sm"'); ?>
			<span class="help-block"><?php echo img(_img_tip_props('')); ?>Champ obligatoire.</span>
			<?php echo form_error('team1'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="team2" class="col-sm-3 control-label">Equipe 2</label>
		<div class="col-sm-9">
			<?php echo form_dropdown('team2', $teams, (isset($match->match_team2)? $match->match_team2:''), 'id="team2" class="form-control input-sm"'); ?>
			<span class="help-block"><?php echo img(_img_tip_props('')); ?>Champ obligatoire.</span>
			<?php echo form_error('team2'); ?>
		</div>
	</div>

	<div class="form-group">
		<label for="comments" class="col-sm-3 control-label">Commentaires</label>
		<div class="col-sm-9">
			<?php echo form_textarea($input_comments); ?>
			<?php echo form_error('comments'); ?>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
<?php $js = 'onclick="location.href=\'' . site_url('planning/gestion-matches') . '\';"';
echo form_submit('submitform', 'Enregistrer', 'class="btn btn-primary btn-sm"') . "\n"
	. form_button('cancel', 'Retour à la liste sans enregistrer', $js . ' class="btn btn-default btn-sm"') . "\n"; ?>
		</div>
	</div>

</fieldset>

<?php echo form_close(); ?>

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
</script>