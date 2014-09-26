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
 * See LICENSE.TXT file for more information.
 *
 * @copyright  Copyright (c) 2013-2014 Marie Kuntz - Lezard Rouge (http://www.lezard-rouge.fr)
 * @license    GNU-GPL v3 http://www.gnu.org/licenses/agpl.html
 * @version    1.0
 * @author     Marie Kuntz - Lezard Rouge SARL - www.lezard-rouge.fr - info@lezard-rouge.fr
 */

/**
 * TEMPLATE
 * default template
 *
 * @version $Id: tpl_simple.php 93 2014-09-23 10:04:16Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CSPORT</title>

<link href="<?php echo base_url() . CRH_PATH_TO_CSS; ?>bootstrap-theme-ubuntu.min.css" rel="stylesheet">
<link href="<?php echo base_url() . CRH_PATH_TO_CSS; ?>jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
<link href="<?php echo base_url() . CRH_PATH_TO_CSS; ?>layout.css" rel="stylesheet">
<link href="<?php echo base_url() . CRH_PATH_TO_CSS; ?>elements.css" rel="stylesheet">

<script src="https://code.jquery.com/jquery.js"></script>
<script src="<?php echo base_url() . CRH_PATH_TO_JS; ?>jquery-ui-1.10.4.custom.min.js"></script>
<script src="<?php echo base_url() . CRH_PATH_TO_JS; ?>bootstrap.min.js"></script>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<?php
// scripts supplémentaires
echo $_scripts . "\n";
// CSS supplémentaires
echo $_styles . "\n";
?>
</head>

<body>
	<div class="container simple-container">
		<div class="row" id="main-content">
			<div class="col-sm-12">
				<?php echo $main_content; ?>
			</div>
		</div>
	</div>

</body>

</html>