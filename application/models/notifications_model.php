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
 * MODEL
 * notification management
 *
 * @version $Id: notifications_model.php 65 2014-03-03 09:01:25Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 *
 */

class Notifications_model extends CI_Model
{

	function __construct ()
	{
		parent::__construct();
		$this->load->model('emails_model');
		$this->load->model('matches_model');
		$this->load->model('users_model');
	}


	/**
	 * mass alert the other team when a team cancel or ask defer
	 *
	 * @param array $data ($match_id, array(team, status)
	 */
	function massAlertTeamNok($data)
	{
		foreach($data as $match_id => $info) {
			self::alertTeamNok($match_id, $info['team'], $info['status']);
		}
	}


	/**
	 * mass alert for referees or clubs who have been summoned for several matches
	 * 
	 * @param array $data (match_id => info(type, id))
	 *		string type, u(ser)|c(lub)
	 *		int id, id of referee or club to alert
	 */
	function massAlertSummonedReferee($data)
	{
		foreach($data as $match_id => $info) {
			if($info['type'] == 'u') {
				Notifications_model::alertSummonedReferee($match_id, $info['id']);
			} elseif($info['type'] == 'c') {
				Notifications_model::alertSummonedRefereeClub($match_id, $info['id']);
			}
		}
	}


	/**
	 * mass alert a club the summoned referee is not available
	 *
	 * @param array $data (match_id, team_number)
	 */
	function massAlertClubRefereeNok($data)
	{
		foreach($data as $match_id => $team_number) {
			self::alertClubRefereeNok($match_id, $team_number);
		}
	}


	/**
	 * alert the other team when a team cancel or ask defer
	 *
	 * @param int $match_id
	 * @param int $team ; 1|2
	 * @param string $status ; R|F
	 *
	 * @return bool
	 */
	function alertTeamNok($match_id, $team, $status)
	{
		$match = Matches_model::getMatch($match_id);
		// get users to warn
		if($team == 1) {
			$field = 'match_team2';
			$field2 = 'team2_club_id';
		} elseif($team == 2) {
			$field = 'match_team1';
			$field2 = 'team1_club_id';
		} else {
			return false;
		}
		$team_to_warn = $match->$field;
		$club_to_warn = $match->$field2;
		// club manager of the other team
		$club_manager = Users_model::getClubManager($club_to_warn);
		// team manager of the other team
		$team_manager = Users_model::getTeamManagers($team_to_warn);
		// referees
		$referees = Users_model::getRefereesByMatch($match->match_referee1, $match->match_referee2);
		$recipients = array();
		foreach($club_manager as $user) {
			$recipients[] = $user;
		}
		foreach($team_manager as $user) {
			$recipients[] = $user;
		}
		foreach($referees as $user) {
			$recipients[] = $user;
		}
		return Emails_model::sendAlertTeamNok($match, $status, $recipients);
	}


	/**
	 * alert a referee who has been summoned for a match
	 *
	 * @param int $match_id
	 * @param int $referee_id
	 *
	 * @return bool
	 */
	function alertSummonedReferee($match_id, $referee_id)
	{
		$match = Matches_model::getMatch($match_id);
		$referee = Users_model::getUser($referee_id);
		$recipients = array(0 => $referee);
		return Emails_model::sendRefereeNotification($match, $recipients);
	}


	/**
	 * alert a club who has been summoned for refeering match
	 *
	 * @param int $match_id
	 * @param int $club_id
	 *
	 * @return bool
	 */
	function alertSummonedRefereeClub($match_id, $club_id)
	{
		$match = Matches_model::getMatch($match_id);
		$manager = Users_model::getClubRefereeManager($club_id);
		$recipients = array();
		foreach ($manager as $user) {
			$recipients[] = $user;
		}
		return Emails_model::sendClubRefereeNotification($match, $recipients);
	}


	/**
	 * alert a club the summoned referee is not available
	 *
	 * @param int $match_id
	 * @param int $team_number
	 *
	 * @return bool
	 */
	function alertClubRefereeNok($match_id, $team_number)
	{
		$match = Matches_model::getMatch($match_id);
		if($team_number == 1) {
			$club_id = $match->match_referee1_club;
		} elseif($team_number == 2) {
			$club_id = $match->match_referee2_club;
		} else {
			return false;
		}
		$manager = Users_model::getClubRefereeManager($club_id);
		$recipients = array();
		foreach ($manager as $user) {
			$recipients[] = $user;
		}
		return Emails_model::sendAlertRefereeNok($match, $recipients);
	}


	/**
	 * alert a club manager and team manager that a home match is incomplete
	 *
	 * @param object $match
	 */
	function alertClubMeetingIncomplete($match) {
		// get recipients
		$club_manager = Users_model::getClubManager($match->team1_club_id);
		$team_manager = Users_model::getTeamManagers($match->match_team1);
		$recipients = array();
		foreach($club_manager as $user) {
			$recipients[] = $user;
		}
		foreach($team_manager as $user) {
			$recipients[] = $user;
		}
		// send mail
		Emails_model::sendAlertMeetingIncomplete($match, $recipients);
	}


	/**
	 * alert a club referee manager and club manager that a referee is missing on home match
	 * 
	 * @param object $match
	 */
	function alertClubRefereeMissing($match) {
		// get recipients
		$club_manager = Users_model::getClubManager($match->team1_club_id);
		$recipients = array();
		foreach($club_manager as $user) {
			$recipients[] = $user;
		}
		$manager = Users_model::getClubRefereeManager($match->match_referee1_club);
		foreach ($manager as $user) {
			$recipients[] = $user;
		}
		// send mail
		Emails_model::sendAlertMeetingRefereeMissing($match, $recipients);
	}


	/**
	 * alert a referee who has not confirmed his coming on a match
	 *
	 * @param object $match
	 * @param int $user_id
	 */
	function alertRefereeNotConfirmed($match, $user_id)
	{
		// get recipients
		$user = Users_model::getUser($user_id);
		$recipients = array($user);
		// send mail
		Emails_model::sendAlertRefereeNotConfirmed($match, $recipients);
	}


	/**
	 * alert a team who has not confirmed his coming on a match
	 *
	 * @param object $match
	 * @param int $team ; 1|2
	 */
	function alertTeamNotConfirmed($match, $team)
	{
		// get users to warn
		if($team == 1) {
			$field = 'match_team1';
			$field2 = 'team1_club_id';
		} elseif($team == 2) {
			$field = 'match_team2';
			$field2 = 'team2_club_id';
		} else {
			return false;
		}
		$team_to_warn = $match->$field;
		$club_to_warn = $match->$field2;
		// club manager of the other team
		$club_manager = Users_model::getClubManager($club_to_warn);
		// team manager of the other team
		$team_manager = Users_model::getTeamManagers($team_to_warn);
		$recipients = array();
		foreach($club_manager as $user) {
			$recipients[] = $user;
		}
		foreach($team_manager as $user) {
			$recipients[] = $user;
		}
		// send mail
		Emails_model::sendAlertTeamNotConfirmed($match, $recipients);
	}


	/**
	 * remind a referee a match is tomorrow
	 *
	 * @param object $match
	 * @param int $user_id
	 */
	function remindReferee($match, $user_id)
	{
		// get recipients
		$user = Users_model::getUser($user_id);
		$recipients = array($user);
		// send mail
		Emails_model::sendRefereeReminder($match, $recipients);
	}


}

/* End of file notifications_model.php */
/* Location: ./application/models/notifications_model.php */