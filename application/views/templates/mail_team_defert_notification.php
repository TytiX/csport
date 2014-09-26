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
 * mail template for alerting a club manager
 *
 * @version $Id: mail_team_defert_notification.php 65 2014-03-03 09:01:25Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */
?>

<div>
	<p>Bonjour,</p>
	
	<p>Vous recevez ce message car l'équipe devant jouer la rencontre sportive suivante
		demande un report :<br>
		Date : <?php echo $date; ?><br>
		Heure : <?php echo $hour; ?><br>
		Lieu : <?php echo $place; ?><br>
		Compétition : <?php echo $cs; ?><br>
		Catégorie : <?php echo $cat; ?><br>
		Equipe qui reçoit : <?php echo $team1; ?><br>
		Equipe reçue : <?php echo $team2; ?><br>
		Arbitre(s) officiant(s) : <?php echo $referees; ?><br>
	</p>

	<p>Merci de prendre contact avec le responsable de l'équipe pour décaler la rencontre.</p>

	<p>Sportivement,</p>

	<p>Le CRH des Pays de la Loire<br>
		<?php echo base_url(); ?></p>

	<p class="end-of-message">Vous recevez ce message car vous êtes responsable de club ou responsable d'équipe.<br>
	Afin d'éviter que de futurs messages ne passent en spam, ajoutez l'expéditeur dans votre carnet d'adresses.</p>

</div>