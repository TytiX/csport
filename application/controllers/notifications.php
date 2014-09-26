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
 * CONTROLLER
 * notification management
 *
 * @version $Id: notifications.php 80 2014-08-14 07:41:42Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notifications extends MY_Controller
{


	function __construct()
	{
		parent::__construct();
		$this->load->model('matches_model');
		$this->load->model('users_model');
		$this->load->model('emails_model');
	}


	/**
	 * default controller
	 */
	function index()
	{
		redirect('', 'location');
	}


	/**
	 * send notifications for all matches
	 */
	function notifyAll()
	{
		Permissions_model::userHasAccess('notify_all');

		$error = 0;

		$matches = Matches_model::getAllMatches(array('date_from' => date('Y-m-d')), 0, 0);
		// for each matches, get users to send (club, teams, referees)
		foreach($matches['result'] as $match) {
			// send club & teams
			$team1_manager = array();
			$team2_manager = array();
			$club1_manager = array();
			$club2_manager = array();
			if( ! empty($match->match_team1)) {
				$team1_manager = Users_model::getTeamManagers($match->match_team1);
				$club1_manager = Users_model::getClubManager($match->team1_club_id);
			}
			if( ! empty($match->match_team2)) {
				$team2_manager = Users_model::getTeamManagers($match->match_team2);
				$club2_manager = Users_model::getClubManager($match->team2_club_id);
			}
			$recipients = array();
			foreach($team1_manager as $user) {
				$recipients[] = $user;
			}
			foreach($club1_manager as $user) {
				$recipients[] = $user;
			}
			foreach($team2_manager as $user) {
				$recipients[] = $user;
			}
			foreach($club2_manager as $user) {
				$recipients[] = $user;
			}
			$result = Emails_model::sendTeamNotification($match, $recipients);
			if( ! $result) {
				$error = 1;
			}
			// send referees if summoned
			$referees = Users_model::getRefereesByMatch(array($match->match_referee1, $match->match_referee2));
			$result2 = Emails_model::sendRefereeNotification($match, $referees);
			if( ! $result2) {
				$error = 1;
			}
		}
		if($error == 1) {
			$message = "Des erreurs ont été rencontrées lors de l'envoi des notifications. Veuillez consulter le fichier de logs.";
			$this->session->set_flashdata('message', $message);
			$this->session->set_flashdata('type_message', CRH_TYPE_MSG_WARNING);
		} else {
			$message = "Les notifications ont bien été envoyées.";
			$this->session->set_flashdata('message', $message);
			$this->session->set_flashdata('type_message', CRH_TYPE_MSG_PERM_SUCCESS);
		}
		// return to page
		$referer = $_SERVER['HTTP_REFERER'];
		redirect($referer);
	}


	function notifyMatch($match_id)
	{
		Permissions_model::userHasAccess('notify_match');
		$error = 0;
		$match = Matches_model::getMatch($match_id);

		// send club & teams
		$team1_manager = array();
		$team2_manager = array();
		$club1_manager = array();
		$club2_manager = array();
		if( ! empty($match->match_team1)) {
			$team1_manager = Users_model::getTeamManagers($match->match_team1);
			$club1_manager = Users_model::getClubManager($match->team1_club_id);
		}
		if( ! empty($match->match_team2)) {
			$team2_manager = Users_model::getTeamManagers($match->match_team2);
			$club2_manager = Users_model::getClubManager($match->team2_club_id);
		}
		$recipients = array();
		foreach($team1_manager as $user) {
			$recipients[] = $user;
		}
		foreach($club1_manager as $user) {
			$recipients[] = $user;
		}
		foreach($team2_manager as $user) {
			$recipients[] = $user;
		}
		foreach($club2_manager as $user) {
			$recipients[] = $user;
		}
		$result = Emails_model::sendAlertTeamMatchChanged($match, $recipients);
		if( ! $result) {
			$error = 1;
		}
		// send referees if summoned, else referee managers
		$referees = Users_model::getRefereesByMatch(array($match->match_referee1, $match->match_referee2));
		if(empty($match->match_referee1)) {
			$club1_referee_manager = Users_model::getClubRefereeManager($match->match_referee1_club);
		}
		if(empty($match->match_referee2)) {
			$club2_referee_manager = Users_model::getClubRefereeManager($match->match_referee2_club);
		}
		$referees = array_merge($referees, $club1_referee_manager, $club2_referee_manager);
		$result2 = Emails_model::sendAlertRefereeMatchChanged($match, $referees);
		if( ! $result2) {
			$error = 1;
		}
		if($error == 1) {
			$message = "Des erreurs ont été rencontrées lors de l'envoi des notifications. Veuillez consulter le fichier de logs.";
			$this->session->set_flashdata('message', $message);
			$this->session->set_flashdata('type_message', CRH_TYPE_MSG_WARNING);
		} else {
			$message = "Les notifications ont bien été envoyées.";
			$this->session->set_flashdata('message', $message);
			$this->session->set_flashdata('type_message', CRH_TYPE_MSG_PERM_SUCCESS);
		}
		// return to page
		$referer = $_SERVER['HTTP_REFERER'];
		redirect($referer);
	}


}

/* End of file notifications.php */
/* Location: ./application/controllers/notifications.php */