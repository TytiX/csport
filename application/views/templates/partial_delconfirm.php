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
 * PARTIAL
 * confirmation for deleting
 *
 * @version $Id: partial_delconfirm.php 65 2014-03-03 09:01:25Z lezardro $
 * @author Marie Kuntz / Lézard Rouge
 */
?>
<script type="text/javascript">
   function CloseBox() {
      if(typeof(parent.$.fancybox) == 'function') {
      	parent.$.fancybox.close();
      }
   }
</script>
<?php
// opening form
echo form_open($url_form);
// hidden inputs
echo form_hidden($input_hidden_name, $input_hidden_value);
// complementary fields, like an hidden field
if(isset($supplement)) {
	echo $supplement;
}
?>
<div id="tconfirm">
	<p>Êtes-vous sûr de vouloir supprimer <?php echo $nom_a_supprimer; ?> ? <br>
	<?php echo $valeur_a_supprimer; ?></p>
<?php
if(isset($infos) && !empty($infos)): ?>
	<div class="warning"><?php echo $infos; ?></div>
<?php endif;

$js = 'onclick="javascript:CloseBox();"';
echo form_submit('submitform', 'Oui') . form_button('annul', 'Non', $js) . "\n";
?>
</div>
<?php echo form_close(); ?>