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
 * routines
 *
 * @version $Id: routines.php 73 2014-05-12 12:24:54Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Routines extends MY_Controller
{


	function __construct()
	{
		parent::__construct();
		$this->load->model('matches_model');
		$this->load->model('users_model');
		$this->load->model('emails_model');
		$this->load->model('notifications_model');
		//$this->cron();
	}

	
	/**
	 * default controller
	 */
	function index()
	{
		redirect('', 'location');
	}


	/**
	 * check the controller is called by cli
	 */
	function cron()
	{
		if( ! $this->input->is_cli_request())
		{
			die();
		}
	}


	/**
	 * collect incomplete matches to warn managers
	 *
	 * @param int $delay, number of days in which the match is programmed
	 */
	function checkMatchesIncompleted($delay)
	{
		$matches = Matches_model::getIncompleteMatches($delay);
		//dump($matches);
		foreach($matches as $match) {
			// if referee is missing
			if(empty($match->match_referee1)) {
				Notifications_model::alertClubRefereeMissing($match);
			}
			// if other information are missing
			if(empty($match->match_place_id) || empty($match->match_team2) || $match->match_time_format = '00h00') {
				Notifications_model::alertClubMeetingIncomplete($match);
			}
		}
	}


	/**
	 * collect matches where teams and/or referees have not confirmed
	 *
	 * @param int $delay, number of days in which the match is programmed
	 */
	function checkTeamRefereeNotConfirmed($delay)
	{
		$matches = Matches_model::getMatchesNotConfirmed($delay);
		//dump($matches);
		foreach($matches as $match) {
			// if referee1 has not confirmed
			if( ! empty($match->match_referee1) && ($match->match_referee1_status == 'C' || empty($match->match_referee1_status))) {
				Notifications_model::alertRefereeNotConfirmed($match, $match->match_referee1);
			}
			// if referee2 has not confirmed
			if( ! empty($match->match_referee2) && ($match->match_referee2_status == 'C' || empty($match->match_referee2_status))) {
				Notifications_model::alertRefereeNotConfirmed($match, $match->match_referee2);
			}
			// if team1 has not confirmed
			if( ! empty($match->match_team1) && ($match->match_team1_status == 'C' || empty($match->match_team1_status))) {
				Notifications_model::alertTeamNotConfirmed($match, 1);
			}
			// if team2 has not confirmed
			if( ! empty($match->match_team1) && ($match->match_team2_status == 'C' || empty($match->match_team1_status))) {
				Notifications_model::alertTeamNotConfirmed($match, 2);
			}
		}
	}


	/**
	 * match reminder for referees
	 */
	function matchReminder()
	{
		// get tomorrow matches
		$criteria = array(
			'date_from' => date('d/m/Y', mktime('00', '00', '00', date('n'), date('j') + 1, date('Y'))),
			'date_to' => date('d/m/Y', mktime('00', '00', '00', date('n'), date('j') + 1, date('Y')))
		);
		$matches = Matches_model::getAllMAtches($criteria, 0, 0);
		//dump($matches);
		foreach($matches['result'] as $match) {
			// remind to referee1
			if( ! empty($match->match_referee1)) {
				Notifications_model::remindReferee($match, $match->match_referee1);
			}
			// remind to referee2
			if( ! empty($match->match_referee2)) {
				Notifications_model::remindReferee($match, $match->match_referee2);
			}
		}
	}

}

/* End of file routines.php */
/* Location: ./application/controllers/routines.php */