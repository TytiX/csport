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
 * @version $Id: tpl_default.php 93 2014-09-23 10:04:16Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<meta name="description" content="Retrouvez ici tous les matches de Rink Hockey des Pays de la Loire">
<meta name="keywords" content="csport, rink hockey, pays de la loire, comité de rink hocket des pays de la loire, match, matches, arbitre, arbitrage">


<title>Comité de Rink Hockey des Pays de la Loire - <?php echo $title; ?></title>

<link href="<?php echo base_url() . CRH_PATH_TO_CSS; ?>bootstrap-theme-ubuntu.min.css" rel="stylesheet">
<link href="<?php echo base_url() . CRH_PATH_TO_CSS; ?>jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
<link href="<?php echo base_url() . CRH_PATH_TO_CSS; ?>layout.css" rel="stylesheet">
<link href="<?php echo base_url() . CRH_PATH_TO_CSS; ?>elements.css" rel="stylesheet">

<script src="<?php echo base_url() . CRH_PATH_TO_JS; ?>jquery-1.11.1.min.js"></script>
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
<div id="wrap">

	<div id="header" role="navigation">
        <div class="container">
			<div class="col-sm-2"><a href="<?php echo base_url(); ?>"><img src="<?php echo base_url() . CRH_PATH_TO_IMG; ?>logo.png" alt="logo" class="logo"></a></div>
			<div class="col-sm-9">
				<h1>CSPORT ROLLER</h1>
				<h2>
					<?php if ($show_team_drop_down): ?>
						Calendrier <?php echo form_dropdown('team', $teams, $team->team_id, 'class="team_calendar form-control input-sm"'); ?>
					<?php else: ?>
						Comité de Rink Hockey des Pays de la Loire
					<?php endif; ?>
				</h2>
			</div>
			<?php if (isset($login_menu)): echo $login_menu; endif; ?>
		</div>
	</div>

	<?php echo $menu; ?>

	<div class="container">
		<div class="row" id="main-content">
			<div class="col-sm-12">
				<?php echo $main_content; ?>
			</div>
		</div>
	</div>

</div><!-- #wrap -->

<div id="footer">
	<div class="container">
		<p class="text-muted credit">CSPORT v<?php echo CRH_VERSION; ?> - 2013-2014  GNU/GPL v3 - <a href="http://www.lezard-rouge.fr" target="_blank">Lézard Rouge</a>.</p>
	</div>
</div><!-- #footer -->


<script type="text/javascript">
	$(function() {
		$('.alert-success').delay(3000).animate({
			opacity: 0,
			height:  0,
			margin:  0,
			padding: 0,
			border:  0
		}, 600);
	});
	$(".team_calendar").change(function() {
		var url_tpl = "<?php echo site_url('matches/t__NUM__'); ?>";
		var url = url_tpl.replace("__NUM__", $(this).val());
		window.location = url;
	});
</script>

</body>

</html>
