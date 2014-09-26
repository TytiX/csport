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
 * refereeing management
 *
 * @version $Id: referees.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Referees extends MY_Controller
{


	/**
	 * constructeur
	 *
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('matches_model');
	}


	/**
	 * default controller
	 */
	function index()
	{
		redirect('', 'location');
	}


	/**
	 * manage referees on matches
	 *
	 * @param string $display, display type (list|search|order)
	 * @param int $page_number
	 * @param string $orderby, sort column
	 * @param string $order, order asc|desc
	 */
	function managePlanning($display, $page_number = 1, $orderby = '', $order = 'asc')
	{
		Permissions_model::userHasAccess('referee_edit');

		$data = array('filters' => array());
		$more_params = '';

		$this->load->helper('form');
		$this->load->library('form_validation');

		$message = $this->session->flashdata('message');
		$type_message =$this->session->flashdata('type_message');
		$data['message'] = _set_result_message($message, $type_message);

		if($display == 'search'
			OR $display == 'order') {

			// get search filters
			$this->form_validation->set_rules('f_club',         '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_cat',          '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_referee',      '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_club_referee', '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_date_from',    '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_date_to',      '', 'trim|xss_clean');

			if($this->form_validation->run() === false) {

				// the user has ordered the results
				// and/or is browsing on multiple pages after a search
				// => must get search filters (stored in session)
				$f_club         = $this->session->userdata('fa_club');
				$f_cat          = $this->session->userdata('fa_cat');
				$f_referee      = $this->session->userdata('fa_referee');
				$f_club_referee = $this->session->userdata('fa_club_referee');
				$f_date_from    = $this->session->userdata('fa_date_from');
				$f_date_to      = $this->session->userdata('fa_date_to');

			} else {

				// user just pushed search button so get fresh filters
				$f_club         = set_value('f_club', '');
				$f_cat          = set_value('f_cat', '');
				$f_referee      = set_value('f_referee', '');
				$f_club_referee = set_value('f_club_referee', '');
				$f_date_from    = set_value('f_date_from', '');
				$f_date_to      = set_value('f_date_to', '');
				// store in session
				$this->session->set_userdata(array(
					'fa_club'         => $f_club,
					'fa_cat'          => $f_cat,
					'fa_referee'      => $f_referee,
					'fa_club_referee' => $f_club_referee,
					'fa_date_from'    => $f_date_from,
					'fa_date_to'      => $f_date_to
				));
			}

			$data['filters'] = array(
				'club'         => $f_club,
				'cat'          => $f_cat,
				'referee'      => $f_referee,
				'club_referee' => $f_club_referee,
				'date_from'    => $f_date_from,
				'date_to'      => $f_date_to,
			);

			// if visitor has sorted the list
			if($display == 'order') {
				if($orderby == 'date') {
					$order_type = 'order_date';
				} elseif($orderby == 'cat') {
					$order_type = 'order_cat';
				} elseif($orderby == 'cs') {
					$order_type = 'order_cs';
				} elseif($orderby == 'equ1') {
					$order_type = 'order_team1';
				} elseif($orderby == 'equ2') {
					$order_type = 'order_team2';
				} else {
					$order_type = 'order_date';
				}
				if($order != 'desc') {
					$order = 'asc';
				}
				$data['filters'][$order_type] = $order;
				$more_params = '/' . $orderby . '-' . $order;
			}

		}
		// if $display = list
		else {
			// reset search filters
			$this->session->unset_userdata(array(
				'fa_date_from'    => '',
				'fa_date_to'      => '',
				'fa_club'         => '',
				'fa_cat'          => '',
				'fa_referee'      => '',
				'fa_club_referee' => '',
			));
			$this->session->set_userdata(array('fa_date_from' => date('d/m/Y')));
			$data['filters'] = array('date_from' => date('d/m/Y'));
		}
		//-- end filters

		$nb_per_page = 10;
		$limit = ($page_number - 1) * $nb_per_page;
		$matches = Matches_model::getAllMatches($data['filters'], $limit, $nb_per_page);
		$data['matches'] = $matches['result'];

		// pagination
		$data['current_page_url'] = _recreate_uri(2, $this->uri->segment_array());
        $nb_tot_pages = ceil($matches['total'] / $nb_per_page);
		$data['pagination'] = Misc_model::pagination(
				$data['current_page_url'],
				$nb_tot_pages,
				$page_number,
				$more_params
			);

		$this->load->model('clubs_model');
		$data['clubs'] = Clubs_model::getClubsAsArray(true);
		$data['club_referee'] = Clubs_model::getClubsAsArray(true, true);
		$this->load->model('categories_model');
		$data['categories'] = Categories_model::getCategoriesAsArray(true);
		$this->load->model('teams_model');
		$data['teams'] = Teams_model::getTeamsAsArray(true);
		$this->load->model('users_model');
		$data['status'] = Users_model::getRefereeStatus();
		$data['referees'] = Users_model::getRefereesAsArray(0, true);
		$data['assign_referees'] = Users_model::getRefereesClubsAsArray(0, true);
		
		$data['title'] = "Attribution des arbitres";
		$data['form_url'] = site_url('planning-arbitrage/recherche');
		$data['form_raz'] = site_url('planning-arbitrage/liste');

		//-----------------------------
		// template
		//-----------------------------
		$this->template->write('title', $data['title']);
		$menu = $this->_getMenu('referee');
		$this->template->write('menu', $menu);
		$this->template->write_view('main_content', 'referees/partial_filters', $data);
		$this->template->write_view('main_content', 'referees/referee_general_planning', $data);
		$this->template->render();
	}


	/**
	 * records referees edition
	 */
	function validatePlanning()
	{
		Permissions_model::userHasAccess('referee_edit');
		$this->load->model('notifications_model');
		$this->load->library('form_validation');
		$data = array();
		$notification_data = array();

		$this->form_validation->set_rules('referee1',          'Arbitre 1',           'xss_clean');
		$this->form_validation->set_rules('referee2',          'Arbitre 2',           'xss_clean');
		$this->form_validation->set_rules('previous_referee1', 'Précédent arbitre 1', 'xss_clean');
		$this->form_validation->set_rules('previous_referee2', 'Précédent arbitre 2', 'xss_clean');
		$this->form_validation->set_rules('referee1_status',   'Statut arbitre 1',    'xss_clean');
		$this->form_validation->set_rules('referee2_status',   'Statut arbitre 2',    'xss_clean');

		if ($this->form_validation->run() === true) {
			// stack previous referees information
			$previous_referee1 = array();
			foreach($_POST['previous_referee1'] as $match_id => $previous) {
				$previous_referee1[$match_id] = set_value('previous_referee1');
			}
			// now get posted referee
			foreach($_POST['referee1'] as $match_id => $referee1) {
				$match_referee1 = set_value('referee1');
				$tmp = explode(';', $match_referee1);
				if(count($tmp) > 1) {
					if($tmp[0] == 'u') {
						$data[$match_id]->match_referee1 = $tmp[2];
						$data[$match_id]->match_referee1_club = $tmp[1];
						if( ! isset($previous_referee1[$match_id]) || $previous_referee1[$match_id] != $match_referee1) {
							//Notifications_model::alertSummonedReferee($match_id, $data[$match_id]->match_referee1);
							$notification_data[$match_id] = array('id' => $data[$match_id]->match_referee1, 'type' => 'u');
						}
					} elseif($tmp[0] == 'c') {
						$data[$match_id]->match_referee1 = 0;
						$data[$match_id]->match_referee1_club = $tmp[1];
						if( ! isset($previous_referee1[$match_id]) || $previous_referee1[$match_id] != $match_referee1) {
							//Notifications_model::alertSummonedRefereeClub($match_id, $data[$match_id]->match_referee1_club);
							$notification_data[$match_id] = array('id' => $data[$match_id]->match_referee1_club, 'type' => 'c');
						}
					}
				}
				// if empty but not empty before
				elseif(isset($previous_referee1[$match_id]) && ! empty($previous_referee1[$match_id])) {
					$data[$match_id]->match_referee1 = null;
					$data[$match_id]->match_referee1_club = null;
				}
			}
			foreach($_POST['referee1_status'] as $match_id => $referee1_status) {
				$match_referee1_status = set_value('referee1_status');
				if(isset($data[$match_id]->match_referee1) && ! empty($data[$match_id]->match_referee1)) {
					$data[$match_id]->match_referee1_status = $match_referee1_status;
				} elseif(isset($data[$match_id]->match_referee1_club) && ! empty($data[$match_id]->match_referee1_club)) {
					//$data[$match_id]->match_referee1_status = 'C';
					$data[$match_id]->match_referee1_status = $match_referee1_status;
				} else {
					$data[$match_id]->match_referee1_status = NULL;
				}
			}
			// same as previous, for 2nd referee
			$previous_referee2 = array();
			foreach($_POST['previous_referee2'] as $match_id => $previous) {
				$previous_referee2[$match_id] = set_value('previous_referee2');
			}
			foreach($_POST['referee2'] as $match_id => $referee2) {
				$match_referee2 = set_value('referee2');
				$tmp = explode(';', $match_referee2);
				if(count($tmp) > 1) {
					if($tmp[0] == 'u') {
						$data[$match_id]->match_referee2 = $tmp[2];
						$data[$match_id]->match_referee2_club = $tmp[1];
						if( ! isset($previous_referee2[$match_id]) || $previous_referee2[$match_id] != $match_referee2) {
							//Notifications_model::alertSummonedReferee($match_id, $data[$match_id]->match_referee2);
							$notification_data[$match_id] = array('id' => $data[$match_id]->match_referee2, 'type' => 'u');
							$data[$match_id]->match_referee2_status = 'C';
						}
					} elseif($tmp[0] == 'c') {
						$data[$match_id]->match_referee2 = 0;
						$data[$match_id]->match_referee2_club = $tmp[1];
						if( ! isset($previous_referee2[$match_id]) || $previous_referee2[$match_id] != $match_referee2) {
							//Notifications_model::alertSummonedRefereeClub($match_id, $data[$match_id]->match_referee2_club);
							$notification_data[$match_id] = array('id' => $data[$match_id]->match_referee2_club, 'type' => 'c');
							$data[$match_id]->match_referee2_status = 'C';
						}
					}
				}
				// if empty but not empty before
				elseif(isset($previous_referee2[$match_id]) && ! empty($previous_referee2[$match_id])) {
					$data[$match_id]->match_referee2 = null;
					$data[$match_id]->match_referee2_club = null;
					$data[$match_id]->match_referee2_status = null;
				}
			}
			foreach($_POST['referee2_status'] as $match_id => $referee2_status) {
				$match_referee2_status = set_value('referee2_status');
				if((isset($data[$match_id]->match_referee2)  && ! empty($data[$match_id]->match_referee2))
					|| (isset($data[$match_id]->match_referee2_club) && ! empty($data[$match_id]->match_referee2_club)) ) {
					$data[$match_id]->match_referee2_status = $match_referee2_status;
				}
			}
			Matches_model::massUpdate($data);
			Notifications_model::massAlertSummonedReferee($notification_data);
			$this->session->set_flashdata('message', 'Les arbitres ont été mis à jour.');
			$this->session->set_flashdata('type_message', CRH_TYPE_MSG_SUCCESS);
		} else {
			$this->session->set_flashdata('message', 'Une erreur est survenue.');
			$this->session->set_flashdata('type_message', CRH_TYPE_MSG_ERROR);
		}
		$referer = $_SERVER['HTTP_REFERER'];
		redirect($referer);
	}


	/**
	 * manage referees on matches
	 *
	 * @param string $display, display type (list|search|order)
	 * @param int $page_number
	 * @param string $orderby, sort column
	 * @param string $order, order asc|desc
	 */
	function manageClub($display, $page_number = 1, $orderby = '', $order = 'asc')
	{
		Permissions_model::userHasAccess('referee_edit');

		$club_id = $this->session->userdata('sess_club_id');
		$club_name = $this->session->userdata('sess_club_name');
		$data = array('filters' => array(), 'current_club' => $club_id);
		$more_params = '';

		$this->load->helper('form');
		$this->load->library('form_validation');

		$message = $this->session->flashdata('message');
		$type_message =$this->session->flashdata('type_message');
		$data['message'] = _set_result_message($message, $type_message);

		if($display == 'search'
			OR $display == 'order') {

			// get search filters
			$this->form_validation->set_rules('f_club',         '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_cat',          '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_referee',      '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_club_referee', '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_date_from',    '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_date_to',      '', 'trim|xss_clean');

			if($this->form_validation->run() === false) {

				// the user has ordered the results
				// and/or is browsing on multiple pages after a search
				// => must get search filters (stored in session)
				$f_club         = $this->session->userdata('fa_club');
				$f_cat          = $this->session->userdata('fa_cat');
				$f_referee      = $this->session->userdata('fa_referee');
				$f_club_referee = $this->session->userdata('fa_club_referee');
				$f_date_from    = $this->session->userdata('fa_date_from');
				$f_date_to      = $this->session->userdata('fa_date_to');

			} else {

				// user just pushed search button so get fresh filters
				$f_club         = set_value('f_club', '');
				$f_cat          = set_value('f_cat', '');
				$f_referee      = set_value('f_referee', '');
				$f_club_referee = set_value('f_club_referee', '');
				$f_date_from    = set_value('f_date_from', '');
				$f_date_to      = set_value('f_date_to', '');
				// store in session
				$this->session->set_userdata(array(
					'fa_club'         => $f_club,
					'fa_cat'          => $f_cat,
					'fa_referee'      => $f_referee,
					'fa_club_referee' => $f_club_referee,
					'fa_date_from'    => $f_date_from,
					'fa_date_to'      => $f_date_to
				));
			}

			$data['filters'] = array(
				'club'         => $f_club,
				'cat'          => $f_cat,
				'referee'      => $f_referee,
				'club_referee' => $f_club_referee,
				'date_from'    => $f_date_from,
				'date_to'      => $f_date_to,
			);

			// if visitor has sorted the list
			if($display == 'order') {
				if($orderby == 'date') {
					$order_type = 'order_date';
				} elseif($orderby == 'cat') {
					$order_type = 'order_cat';
				} elseif($orderby == 'cs') {
					$order_type = 'order_cs';
				} elseif($orderby == 'equ1') {
					$order_type = 'order_team1';
				} elseif($orderby == 'equ2') {
					$order_type = 'order_team2';
				} else {
					$order_type = 'order_date';
				}
				if($order != 'desc') {
					$order = 'asc';
				}
				$data['filters'][$order_type] = $order;
				$more_params = '/' . $orderby . '-' . $order;
			}

		}
		// if $display = list
		else {
			// reset search filters
			$this->session->unset_userdata(array(
				'fa_date_from'    => '',
				'fa_date_to'      => '',
				'fa_club'         => '',
				'fa_cat'          => '',
				'fa_referee'      => '',
				'fa_club_referee' => '',
			));
			$this->session->set_userdata(array('fa_club_referee' => $club_id, 'fa_date_from' => date('d/m/Y')));
			$data['filters'] = array('club_referee' => $club_id, 'date_from' => date('d/m/Y'));
		}
		//-- end filters

		$nb_per_page = 10;
		$limit = ($page_number - 1) * $nb_per_page;
		$matches = Matches_model::getAllMatches($data['filters'], $limit, $nb_per_page);
		$data['matches'] = $matches['result'];

		// pagination
		$data['current_page_url'] = _recreate_uri(2, $this->uri->segment_array());
        $nb_tot_pages = ceil($matches['total'] / $nb_per_page);
		$data['pagination'] = Misc_model::pagination(
				$data['current_page_url'],
				$nb_tot_pages,
				$page_number,
				$more_params
			);

		$this->load->model('clubs_model');
		$data['clubs'] = Clubs_model::getClubsAsArray(true);
		$data['club_referee'] = array($club_id => $club_name);
		$this->load->model('categories_model');
		$data['categories'] = Categories_model::getCategoriesAsArray(true);
		$this->load->model('teams_model');
		$data['teams'] = Teams_model::getTeamsAsArray(true);
		$this->load->model('users_model');
		$data['status'] = Users_model::getRefereeStatus();
		$data['referees'] = Users_model::getRefereesAsArray($club_id, true);
		$data['assign_referees'] = Users_model::getRefereesClubsAsArray($club_id, true);
		$data['all_referees'] = Users_model::getRefereesClubsAsArray(0, true);

		$data['title'] = "Matches de mon club à arbitrer";
		$data['form_url'] = site_url('arbitrage-club/recherche');
		$data['form_raz'] = site_url('arbitrage-club/liste');

		//-----------------------------
		// template
		//-----------------------------
		$this->template->write('title', $data['title']);
		$menu = $this->_getMenu('referee');
		$this->template->write('menu', $menu);
		$this->template->write_view('main_content', 'referees/partial_filters', $data);
		$this->template->write_view('main_content', 'referees/referee_club_planning', $data);
		$this->template->render();
	}


	/**
	 * referee match management
	 *
	 * @param string $display, display type (list|search|order)
	 * @param int $page_number
	 * @param string $orderby, sort column
	 * @param string $order, order asc|desc
	 */
	function manageMatch($display, $page_number = 1, $orderby = '', $order = 'asc')
	{
		//Permissions_model::userHasAccess('referee_edit');
		Permissions_model::refereeAccess();

		$user_id = $this->session->userdata('sess_user_id');
		$user_name = $this->session->userdata('sess_user_name');
		$club_id = $this->session->userdata('sess_club_id');
		$club_name = $this->session->userdata('sess_club_name');

		$data = array('filters' => array(), 'current_club' => $club_id, 'current_user' => $user_id);
		$more_params = '';

		$this->load->helper('form');
		$this->load->library('form_validation');

		$message = $this->session->flashdata('message');
		$type_message =$this->session->flashdata('type_message');
		$data['message'] = _set_result_message($message, $type_message);

		if($display == 'search'
			OR $display == 'order') {

			// get search filters
			$this->form_validation->set_rules('f_club',         '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_cat',          '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_referee',      '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_club_referee', '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_date_from',    '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_date_to',      '', 'trim|xss_clean');

			if($this->form_validation->run() === false) {

				// the user has ordered the results
				// and/or is browsing on multiple pages after a search
				// => must get search filters (stored in session)
				$f_club         = $this->session->userdata('fa_club');
				$f_cat          = $this->session->userdata('fa_cat');
				$f_referee      = $this->session->userdata('fa_referee');
				$f_club_referee = $this->session->userdata('fa_club_referee');
				$f_date_from    = $this->session->userdata('fa_date_from');
				$f_date_to      = $this->session->userdata('fa_date_to');

			} else {

				// user just pushed search button so get fresh filters
				$f_club         = set_value('f_club', '');
				$f_cat          = set_value('f_cat', '');
				$f_referee      = set_value('f_referee', '');
				$f_club_referee = set_value('f_club_referee', '');
				$f_date_from    = set_value('f_date_from', '');
				$f_date_to      = set_value('f_date_to', '');
				// store in session
				$this->session->set_userdata(array(
					'fa_club'         => $f_club,
					'fa_cat'          => $f_cat,
					'fa_referee'      => $f_referee,
					'fa_club_referee' => $f_club_referee,
					'fa_date_from'    => $f_date_from,
					'fa_date_to'      => $f_date_to
				));
			}

			$data['filters'] = array(
				'club'         => $f_club,
				'cat'          => $f_cat,
				'referee'      => $f_referee,
				'club_referee' => $f_club_referee,
				'date_from'    => $f_date_from,
				'date_to'      => $f_date_to,
			);

			// if visitor has sorted the list
			if($display == 'order') {
				if($orderby == 'date') {
					$order_type = 'order_date';
				} elseif($orderby == 'cat') {
					$order_type = 'order_cat';
				} elseif($orderby == 'cs') {
					$order_type = 'order_cs';
				} elseif($orderby == 'equ1') {
					$order_type = 'order_team1';
				} elseif($orderby == 'equ2') {
					$order_type = 'order_team2';
				} else {
					$order_type = 'order_date';
				}
				if($order != 'desc') {
					$order = 'asc';
				}
				$data['filters'][$order_type] = $order;
				$more_params = '/' . $orderby . '-' . $order;
			}

		}
		// if $display = list
		else {
			// reset search filters
			$this->session->unset_userdata(array(
				'fa_date_from'    => '',
				'fa_date_to'      => '',
				'fa_club'         => '',
				'fa_cat'          => '',
				'fa_referee'      => '',
				'fa_club_referee' => '',
			));
			$this->session->set_userdata(array('fa_club_referee' => $club_id, 'fa_date_from' => date('d/m/Y')));
			$data['filters'] = array('club_referee' => $club_id, 'date_from' => date('d/m/Y'));
		}
		//-- end filters

		$nb_per_page = 10;
		$limit = ($page_number - 1) * $nb_per_page;
		$matches = Matches_model::getAllMatches($data['filters'], $limit, $nb_per_page);
		$data['matches'] = $matches['result'];

		// pagination
		$data['current_page_url'] = _recreate_uri(2, $this->uri->segment_array());
        $nb_tot_pages = ceil($matches['total'] / $nb_per_page);
		$data['pagination'] = Misc_model::pagination(
				$data['current_page_url'],
				$nb_tot_pages,
				$page_number,
				$more_params
			);

		$this->load->model('clubs_model');
		$data['clubs'] = Clubs_model::getClubsAsArray(true);
		$data['club_referee'] = array($club_id => $club_name);
		$this->load->model('categories_model');
		$data['categories'] = Categories_model::getCategoriesAsArray(true);
		$this->load->model('teams_model');
		$data['teams'] = Teams_model::getTeamsAsArray(true);
		$this->load->model('users_model');
		$data['status'] = Users_model::getRefereeStatus();
		$data['referees'] = Users_model::getRefereesAsArray($club_id, true);
		$data['assign_referees'] = Users_model::getRefereesClubsAsArray(0, true);

		$data['title'] = "Mes matches à arbitrer";
		$data['form_url'] = site_url('arbitre/recherche');
		$data['form_raz'] = site_url('arbitre/liste');

		//-----------------------------
		// template
		//-----------------------------
		$this->template->write('title', $data['title']);
		$menu = $this->_getMenu('isreferee');
		$this->template->write('menu', $menu);
		$this->template->write_view('main_content', 'referees/partial_filters', $data);
		$this->template->write_view('main_content', 'referees/referee_planning', $data);
		$this->template->render();
	}


	/**
	 * records referees edition
	 */
	function validateMatch()
	{
		//Permissions_model::userHasAccess('referee_edit');
		Permissions_model::refereeAccess();
		$this->load->model('notifications_model');
		$this->load->library('form_validation');
		$data = array();
		$notification_data = array();
		$current_user = $this->session->userdata('sess_user_id');

		$this->form_validation->set_rules('referee1', 'Arbitre 1',       'xss_clean');
		$this->form_validation->set_rules('referee2', 'Arbitre 2',       'xss_clean');
		$this->form_validation->set_rules('referee1_status', 'Statut arbitre 1', 'xss_clean');
		$this->form_validation->set_rules('referee2_status', 'Statut arbitre 2', 'xss_clean');
		$this->form_validation->set_rules('previous_status_referee1', 'Précédent statut arbitre 1', 'xss_clean');
		$this->form_validation->set_rules('previous_status_referee2', 'Précédent statut arbitre 2', 'xss_clean');
		$this->form_validation->set_rules('referee1_take', 'Prendre arbitrage 1', 'xss_clean');
		$this->form_validation->set_rules('referee2_take', 'Prendre arbitrage 2', 'xss_clean');

		if ($this->form_validation->run() === true) {
			$previous_status_referee1 = array();
			foreach($_POST['previous_status_referee1'] as $match_id => $previous) {
				$old = set_value('previous_status_referee1');
				$previous_status_referee1[$match_id] = $old;
			}
			foreach($_POST['referee1_status'] as $match_id => $referee1_status) {
				$data[$match_id]->match_referee1_status = set_value('referee1_status');
				if($data[$match_id]->match_referee1_status == 'I' 
					&& ( ! isset($previous_status_referee1[$match_id]) || $previous_status_referee1[$match_id] != $data[$match_id]->match_referee1_status)) {
					//Notifications_model::alertClubRefereeNok($match_id, 1);
					$notification_data[$match_id] = 1;
				}
			}
			foreach($_POST['referee1_take'] as $match_id => $take) {
				$take_match = set_value('referee1_take');
				if($take_match == 1) {
					$data[$match_id]->match_referee1 = $current_user;
					$data[$match_id]->match_referee1_status = 'OK';
				}
			}
			$previous_status_referee2 = array();
			foreach($_POST['previous_status_referee2'] as $match_id => $previous) {
				$old = set_value('previous_status_referee2');
				$previous_status_referee2[$match_id] = $old;
			}
			foreach($_POST['referee2_status'] as $match_id => $referee2_status) {
				$data[$match_id]->match_referee2_status = set_value('referee2_status');
				if($data[$match_id]->match_referee2_status == 'I' 
					&& (! isset($previous_status_referee2[$match_id]) || $previous_status_referee2[$match_id] != $data[$match_id]->match_referee2_status)) {
					//Notifications_model::alertSummonedReferee($match_id, 2);
					$notification_data[$match_id] = 2;
				}
			}
			foreach($_POST['referee1_take'] as $match_id => $take) {
				$take_match = set_value('referee1_take');
				if($take_match == 1) {
					$data[$match_id]->match_referee1 = $current_user;
					$data[$match_id]->match_referee1_status = 'OK';
				}
			}
			foreach($_POST['referee2_take'] as $match_id => $take) {
				$take_match = set_value('referee2_take');
				if($take_match == 1) {
					$data[$match_id]->match_referee2 = $current_user;
					$data[$match_id]->match_referee2_status = 'OK';
				}
			}
			Matches_model::massUpdate($data);
			Notifications_model::massAlertClubRefereeNok($notification_data);
			$this->session->set_flashdata('message', 'Les arbitrages ont été mis à jour.');
			$this->session->set_flashdata('type_message', CRH_TYPE_MSG_SUCCESS);
		} else {
			$this->session->set_flashdata('message', 'Une erreur est survenue.');
			$this->session->set_flashdata('type_message', CRH_TYPE_MSG_ERROR);
		}
		$referer = $_SERVER['HTTP_REFERER'];
		redirect($referer);
	}


}

/* End of file referees.php */
/* Location: ./application/controllers/referees.php */