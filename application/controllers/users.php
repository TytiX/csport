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
 * users management
 *
 * @version $Id: users.php 87 2014-09-14 16:33:21Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends MY_Controller
{


	/**
	 * constructeur
	 *
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('users_model');
	}


	/**
	 * default controller
	 */
	function index()
	{
		redirect('', 'location');
	}


	/**
	 * users list
	 *
	 * @param string $display, display type (list|search|order)
	 * @param int $page_number
	 * @param string $orderby, sort column
	 * @param string $order, order asc|desc
	 */
	function userList($display, $page_number = 1, $orderby = 'uname', $order = 'asc')
	{
		Permissions_model::userHasAccess('user_list');

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
			$this->form_validation->set_rules('f_name',      '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_club',      '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_isreferee', '', 'trim|xss_clean');
			$this->form_validation->set_rules('f_active',    '', 'trim|xss_clean');

			if($this->form_validation->run() === false) {

				/* the user has ordered the results
				 * and/or is browsing on multiple pages after a search
				 * => must get search filters (stored in session)
				 */
				$f_name      = $this->session->userdata('f_name');
				$f_club      = $this->session->userdata('f_club');
				$f_isreferee = $this->session->userdata('f_isreferee');
				$f_active    = $this->session->userdata('f_active');

			} else {

				/* user just pushed search button so get fresh filters
				 */
				$f_name      = set_value('f_name', '');
				$f_club      = set_value('f_club', '');
				$f_isreferee = set_value('f_isreferee', 'all');
				$f_active    = set_value('f_active', 'all');
				// store in session
				$this->session->set_userdata(array(
					'f_name'      => $f_name,
					'f_club'      => $f_club,
					'f_isreferee' => $f_isreferee,
					'f_active'    => $f_active
				));
			}

			$data['filters'] = array(
				'uname'    => $f_name,
				'club'     => $f_club,
				'ureferee' => $f_isreferee,
				'uactive'  => $f_active,
			);

			// if user has sorted the list
			if($display == 'order') {
				if($orderby == 'name') {
					$order_type = 'order_uname';
				} elseif($orderby == 'degree') {
					$order_type = 'order_udegree';
				} elseif($orderby == 'club') {
					$order_type = 'order_club';
				} else {
					$order_type = 'order_uname';
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
				'f_name'      => '',
				'f_club'      => '',
			));
			$this->session->set_userdata(array('f_isreferee' => 'all', 'f_active' => 1));
			$data['filters'] = array('ureferee' => 'all', 'uactive' => 1);
		}
		//-- end filters

		$limit = ($page_number - 1) * CRH_NB_RECORD;
		$users = Users_model::getAllUsers($data['filters'], $limit, CRH_NB_RECORD);
		$data['users'] = $users['result'];

		// pagination
		$data['current_page_url'] = _recreate_uri(2, $this->uri->segment_array());
        $nb_tot_pages = ceil($users['total'] / CRH_NB_RECORD);
		$data['pagination'] = Misc_model::pagination(
				$data['current_page_url'],
				$nb_tot_pages,
				$page_number,
				$more_params
			);

		$this->load->model('clubs_model');
		$data['clubs'] = Clubs_model::getClubsAsArray(true, true);

		//-----------------------------
		// template
		//-----------------------------
		$this->template->write('title', 'Liste des utilisateurs');
		$menu = $this->_getMenu('user');
		$this->template->write('menu', $menu);
		$this->template->write_view('main_content', 'users/user_list', $data);
		$this->template->render();
	}


	/**
	 * add/edit a user
	 *
	 * @param int $user_id, 0 if new
	 */
	function form ($user_id = 0)
	{
		if($user_id == 0) {
			Permissions_model::userHasAccess('user_add');
		} else {
			Permissions_model::userHasAccess('user_edit');
		}
		$this->load->helper(array('form'));
		$data = array();
		$data['user_id'] = $user_id;

		$message = $this->session->flashdata('message');
		$type_message =$this->session->flashdata('type_message');
		$data['message'] = _set_result_message($message, $type_message);

		$title = 'Nouvel';
		if($user_id > 0) {
			$data['user'] = Users_model::getUser($user_id);
			$title = 'Modifier un';
			$this->load->model('teams_model');
			$data['teams'] = Teams_model::getTeamsByClub($data['user']->user_club);
		}
		$data['title'] = $title . ' utilisateur';

		$data['referee_degrees'] = Users_model::getRefereeDegrees(true);
		$this->load->model('profiles_model');
		$data['profiles'] = Profiles_model::getProfilesAsArray();
		$this->load->model('clubs_model');
		$data['clubs'] = Clubs_model::getClubsAsArray(true, true);

		//-----------------------------
		// template
		//-----------------------------
		$this->template->write('title', $data['title']);
		$menu = $this->_getMenu('user');
		$this->template->write('menu', $menu);
		$this->template->write_view('main_content', 'users/user_form', $data);
		$this->template->render();
	}


	/**
	 * add/edit form validation
	 */
	function validate()
	{
		$user_id = $this->input->post('user_id', TRUE);
		if($user_id == 0) {
			Permissions_model::userHasAccess('user_add');
		} else {
			Permissions_model::userHasAccess('user_edit');
		}
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$data = array();

		// validation rules
		$this->form_validation->set_rules('user_id',    'user_id', 'integer');
		$this->form_validation->set_rules('name',       'Nom',     'trim|required|xss_clean');
		$this->form_validation->set_rules('address',    'Adresse',  'trim|xss_clean');
		$this->form_validation->set_rules('email',      'Email',   'trim|valid_email|xss_clean');
		$this->form_validation->set_rules('phone',      'Tél. fixe',  'trim|xss_clean');
		$this->form_validation->set_rules('mobile',     'Tél. portable',  'trim|xss_clean');
		$this->form_validation->set_rules('birthdate',  'Date de naissance',  'trim|xss_clean');
		$this->form_validation->set_rules('login',      'Login',   'trim|alpha_numeric|xss_clean');
		$this->form_validation->set_rules('pwd',        'Mot de passe', 'trim|min_length[6]|max_length[50]|callback_my_password_check|xss_clean');
		$this->form_validation->set_rules('licence',    'Licence',  'trim|xss_clean');
		$this->form_validation->set_rules('club',       'Club',  'trim|xss_clean');
		$this->form_validation->set_rules('isreferee',  'Arbitre',  'trim|xss_clean');
		$this->form_validation->set_rules('referee_degree', 'Degré d\'arbitrage',  'trim|xss_clean');
		$this->form_validation->set_rules('active',     'Actif',  'trim|xss_clean');
		$this->form_validation->set_rules('ckb_prf',    'Profil', '');
		$this->form_validation->set_rules('ckb_team',   'Responsable d\'équipe', '');

		// run form validation
		$result = $this->form_validation->run();

		$data['user_id'] = set_value('user_id', 0);
		$data['user']->user_name    = set_value('name');
		$data['user']->user_address = set_value('address');
		$data['user']->user_email = set_value('email');
		$data['user']->user_phone = set_value('phone');
		$data['user']->user_mobile = set_value('mobile');
		$data['user']->user_birthdate_format = set_value('birthdate');
		$data['user']->user_birthdate = _date2ISO($data['user']->user_birthdate_format);
		$data['user']->user_login = set_value('login');
		$data['user']->user_password = set_value('pwd');
		$data['user']->user_licence  = set_value('licence');
		$data['user']->user_club  = set_value('club');
		$data['user']->user_isreferee  = set_value('isreferee');
		$data['user']->user_referee_degree  = set_value('referee_degree');
		$data['user']->user_active  = set_value('active');
		if(isset($_POST['ckb_prf'])) {
			foreach($_POST['ckb_prf'] as $ckb_prf) {
				$prf = set_value('ckb_prf');
				$data['user']->profiles[$prf] = $prf;
			}
		} else {
			$data['user']->profiles = array();
		}
		if(isset($_POST['ckb_team'])) {
			foreach($_POST['ckb_team'] as $ckb_team) {
				$team = set_value('ckb_team');
				$data['user']->teams[$team] = $team;
			}
		} else {
			$data['user']->teams = array();
		}

		//-------------------------------------
		if ($result !== false) {

			unset($data['user']->user_birthdate_format);
			$data['user_id'] = Users_model::setUser($data['user_id'], $data['user']);

			if ($data['user_id'] == CRH_ERROR_DATA_EMPTY) {
				$this->session->set_flashdata('message', 'Aucune donnée à enregistrer.');
				$this->session->set_flashdata('type_message', CRH_TYPE_MSG_ERROR);
			} elseif($data['user_id'] == CRH_ERROR_LOGIN_UNAVAILABLE) {
				$this->session->set_flashdata('message', "L'identifiant que vous avez indiqué est déjà utilisé pour un autre utilisateur. Votre saisie n'a pas été enregistrée.");
				$this->session->set_flashdata('type_message', CRH_TYPE_MSG_ERROR);
			} else {
				$this->session->set_flashdata('message', "L'utilisateur a bien été enregistré.");
				$this->session->set_flashdata('type_message', CRH_TYPE_MSG_SUCCESS);
			}
			redirect('utilisateurs/liste', 'location');
		} else {
			$this->session->set_flashdata('message', 'Veuillez vérifier votre saisie.');
			$this->session->set_flashdata('type_message', CRH_TYPE_MSG_ERROR);
		}

		$message = $this->session->flashdata('message');
		$type_message =$this->session->flashdata('type_message');
		$data['message'] = _set_result_message($message, $type_message);

		$title = 'Nouvel';
		if($data['user_id'] > 0) {
			$title = 'Modifier un';
			$this->load->model('teams_model');
			$data['teams'] = Teams_model::getTeamsByClub($data['user']->user_club);
		}
		$data['title'] = $title . ' utilisateur';

		$data['referee_degrees'] = Users_model::getRefereeDegrees(true);
		$this->load->model('profiles_model');
		$data['profiles'] = Profiles_model::getProfilesAsArray();
		$this->load->model('clubs_model');
		$data['clubs'] = Clubs_model::getClubsAsArray(true, true);

		//-----------------------------
		// template
		//-----------------------------
		$this->template->write('title', $data['title']);
		$menu = $this->_getMenu('user');
		$this->template->write('menu', $menu);
		$this->template->write_view('main_content', 'users/user_form', $data);
		$this->template->render();
	}


	/**
	 * send passwords to all useful users on site (have profile or is referee)
	 */
	function sendPasswords()
	{
		Permissions_model::userHasAccess('user_list');
		// on recupère tous les users qui ont un profil (hors non-membre)
		$users = Users_model::getActiveUsers();
		// pour chaque user, on envoie un mail avec son identifiant et son mot de passe
		$this->load->model('emails_model');
		foreach($users as $user) {
			Emails_model::sendPassword($user);
		}
		redirect('utilisateurs/liste');
	}


	/**
	 * details on a user
	 *
	 * @param int $user_id
	 */
	function detail($user_id)
	{
		Permissions_model::userHasAccess('user_detail');
		$data = array();
		$data['user_id'] = $user_id;

		$message = $this->session->flashdata('message');
		$type_message =$this->session->flashdata('type_message');
		$data['message'] = _set_result_message($message, $type_message);

		$data['user'] = Users_model::getUser($user_id);
		$this->load->model('teams_model');
		$data['teams'] = Teams_model::getTeamsByClub($data['user']->user_club);

		$this->load->model('profiles_model');
		$data['profiles'] = Profiles_model::getProfilesAsArray();
		$this->load->model('clubs_model');
		$data['clubs'] = Clubs_model::getClubsAsArray(true, true);

		//-----------------------------
		// template
		//-----------------------------
		$this->template->write('title', 'Fiche utilisateur');
		$menu = $this->_getMenu('user');
		$this->template->write('menu', $menu);
		$this->template->write_view('main_content', 'users/user_detail', $data);
		$this->template->render();
	}


	/**
	 * user's password edition
	 */
	function changePwd()
	{
		Permissions_model::userHasAccess('user_pwd');
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$data = array();
		$user_id = $this->session->userdata('user_id');
		$type_message = '';
		$message = '';

		$this->form_validation->set_rules('old_pwd', 'Mot de passe actuel', 'required|trim|xss_clean');
		$this->form_validation->set_rules('new_pwd', 'Nouveau mot de passe', 'required|trim|min_length[6]|max_length[50]|callback_my_password_check|xss_clean');

		if ($this->form_validation->run() === true) {
			$old_pwd = set_value('old_pwd');
			$new_pwd = set_value('new_pwd');
			$user = Users_model::getUser($user_id);
			if(Misc_model::decryptStr($user->user_password) != $old_pwd) {
				$message = "Le mot de passe actuel saisi ne correspond pas à ce qui est enregistré.";
				$type_message = CRH_TYPE_MSG_ERROR;
			} else {
				$user_id = Users_model::changePassword($user_id, $new_pwd);
				if($user_id === CRH_ERROR_DATA_EMPTY) {
					$message = "Une erreur est survenue.";
					$type_message = CRH_TYPE_MSG_ERROR;
				} else {
					$message = "Le nouveau mot de passe a bien été enregistré.";
					$type_message = CRH_TYPE_MSG_SUCCESS;
				}
			}
		}

		$data['message'] = _set_result_message($message, $type_message);

		//-----------------------------
		// template
		//-----------------------------
		$this->template->write('title', 'Modification du mot de passe');
		$menu = $this->_getMenu('pwd');
		$this->template->write('menu', $menu);
		$this->template->write_view('main_content', 'users/user_pwd', $data);
		$this->template->render();
	}


	/**
	 * callback function to check password characters validity
	 *
	 * @param string $password
	 *
	 * @return boolean
	 */
	function my_password_check($password)
	{
		if( ! empty($password) && ! preg_match("/^([a-z0-9-_!:%&])+$/i", $password)) {
			$this->form_validation->set_message('my_password_check', 'Le champ %s contient un caractère non permis.');
			return false;
		} else {
			return true;
		}
	}

}

/* End of file users.php */
/* Location: ./application/controllers/users.php */