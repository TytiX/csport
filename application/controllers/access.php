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
 * access management
 *
 * @version $Id: access.php 69 2014-05-09 08:33:04Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access extends MY_Controller
{


	function __construct()
	{
		parent::__construct();
		$this->load->model('permissions_model');
		$this->load->model('users_model');
	}


	/**
	 * default controller (not used)
	 */
	public function index()
	{
		redirect('', 'location');
	}


	/**
	 * login form
	 */
	function login()
	{
		$data = array('message' => '');
		
		$data['default_ident'] = '';
		// check cookie
		$cookie = $this->input->cookie('csport');
		if( ! $cookie) {
			$cookie_data = array(
				'name'   => 'csport',
				'value'  => 'empty',
				'expire' => (3600*24*365)
			);
			$this->input->set_cookie($cookie_data);
		}
		else {
			$login = $this->input->post('login');
			// if form posted AND login not empty, get posted login
			if($login !== FALSE && $login != '') {
				$cookie_data = array(
					'name'   => 'csport',
					'value'  => $login,
					'expire' => (3600*24*365)
				);
				$this->input->set_cookie($cookie_data);
				$data['default_ident'] = $login;
			}
			// if form not posted and cookie already has a login
			elseif($cookie != 'empty') {
				$data['default_ident'] = $cookie;
			}
		}

		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		$this->form_validation->set_rules('csport_login', 'Identifiant', 'trim|required|xss_clean');
		$this->form_validation->set_rules('pwd', 'Mot de passe', 'trim|required|xss_clean');

		$message = $this->session->flashdata('message');
		$type_message = $this->session->flashdata('type_message');
		$data['message'] = _set_result_message($message, $type_message);

		if ($this->form_validation->run() !== false) {
			$user = Users_model::getUserByLogin(set_value('csport_login'), set_value('pwd'));
			// if user empty, or has no profile, or has only member profile, cannot connect
			if(empty($user)
				|| (empty($user->profiles) && ($user->user_isreferee != 1))
				|| (count($user->profiles) == 1) && array_key_exists(Profiles_model::$profile_member, $user->profiles)) {
				$data['message'] = _set_result_message('Compte incorrect', CRH_TYPE_MSG_ERROR);
			} else {
				// connect user
				Permissions_model::connectUser($user);
				// redirect
				redirect('matches/liste-simple');
			}
		}

		//-----------------------------
		// template
		//-----------------------------
		$this->template->write('title', 'Connexion');
		$this->template->write('menu', '');
		$this->template->write_view('main_content', 'login', $data);
		$this->template->render();
	}


	/**
	 * logout a user
	 * 
	 * @param string $token
	 */
	function logout($token)
	{
		$referer = $_SERVER['HTTP_REFERER'];
		if(empty($referer)) {
			$referer = 'matches/liste-simple';
		}
		// check if token corresponds to user
		$sess_token = $this->session->userdata('sess_token');
		// if not, do nothing and go back to referer
		if(empty($token) || $sess_token != $token) {
			redirect($referer);
		} else {
			Permissions_model::disconnectUser();
			redirect('connexion');
		}
	}


	/**
	 * error page : access denied
	 */
	function access_denied()
	{
		$message = _set_result_message("Vous n'avez pas accès à cette partie du site.", CRH_TYPE_MSG_ERROR);
		$this->template->write('title', 'Accès refusé');
		$menu = $this->_getMenu('');
		$this->template->write('menu', $menu);
		$this->template->write_view('main_content', 'templates/partial_error', array('message' => $message));
		$this->template->render();
	}


}

/* End of file access.php */
/* Location: ./application/controllers/access.php */