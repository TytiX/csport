<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * generic template for import files
 *
 * @version $Id: generic_import.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

$input_upload = array(
	'name' => 'uploaded_file',
	'id' => 'uploaded_file'
);
?>
<h1><?php echo $title; ?></h1>

<?php echo $instructions; ?>
<?php echo $message; ?>
<?php echo validation_errors(); ?>

<?php echo form_open_multipart($action, ' class="form-horizontal"'); ?>
<div class="form-group">
	<label class="col-sm-3 control-label" for="uploaded_file">Choisissez un fichier : </label>
	<div class="col-sm-6"><?php echo form_upload($input_upload); ?></div>
</div>
<div class="form-group">
	<div class="col-sm-offset-3 col-sm-6"><input type="submit" name="submit_form" value="Envoyer" class="btn btn-primary btn-sm"></div>
</div>
<?php echo form_close(); ?>