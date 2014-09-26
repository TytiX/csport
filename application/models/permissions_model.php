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
 * permissions management
 *
 * @version $Id: permissions_model.php 65 2014-03-03 09:01:25Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

class Permissions_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}


	/**
	 * insert user infos in session
	 * 
	 * @param object $user
	 */
	function connectUser($user)
	{
		$profiles = array();
		foreach ($user->profiles as $prf_id => $prf_name) {
			$profiles[] = $prf_id;
		}
		// records infos in session table
		$data = array(
			'sess_user_id'             => $user->user_id,
			'sess_user_name'           => $user->user_name,
			'sess_user_email'          => $user->user_email,
			'sess_user_isreferee'      => $user->user_isreferee,
			'sess_user_referee_degree' => $user->user_referee_degree,
			'sess_club_id'             => $user->club_id,
			'sess_club_name'           => $user->club_name,
			'sess_profiles'            => serialize($profiles),
			'sess_teams'               => serialize($user->teams),
			'sess_token'               => md5(uniqid(rand(), true))
		);
		$this->session->set_userdata($data);
	}


	/**
	 * destroy user session
	 */
	function disconnectUser()
	{
		$data = array(
			'sess_user_id'             => 0,
			'sess_user_name'           => '',
			'sess_user_email'          => '',
			'sess_user_isreferee'      => '',
			'sess_user_referee_degree' => '',
			'sess_club_id'             => 0,
			'sess_club_name'           => '',
			'sess_profiles'            => '',
			'sess_teams'               => '',
			'sess_token'               => ''
		);
		$this->session->set_userdata($data);
		$this->session->unset_userdata($data);
		$this->session->sess_destroy();
	}

	
	/**
	 * check if current user can access to component
	 *
	 * @param str $component_name
	 * @param bool $redirection ; true if user must be redirected if no access
	 *							  false if function returns false if user cannot access
	 *
	 * @return bool
	 */
	function userHasAccess($component_name, $redirection = true)
	{
		$user_id = $this->session->userdata('sess_user_id');
		$this->load->model('users_model');
		$this->load->model('profiles_model');

		$ci =& get_instance();
		// if user not connected
		if(empty($user_id)) {
			$ci->session->set_flashdata('message', 'Veuillez vous identifier.');
			$ci->session->set_flashdata('type_message', CRH_TYPE_MSG_ERROR);
			redirect('connexion', 'location');
		} else {
			// if user is only member
			if(Profiles_model::isOnlyMember()) {
				$ci->session->set_flashdata('message', 'Vous n\'avez pas accès à cette partie du site.');
				$ci->session->set_flashdata('type_message', CRH_TYPE_MSG_ERROR);
				redirect('connexion');
			}
		}

		// check if one of user's profiles can access to component
		$serial_profiles = $this->session->userdata('sess_profiles');
		$user_profiles = unserialize($serial_profiles);
		$permitted = self::hasAccessByProfileAndComponent($component_name, $user_profiles);
		if($permitted) {
			return true;
		}

		//-------------------------------------
		// if no access
		// - if redirection == TRUE, redirect to error page
		// - if redirection == FALSE, return FALSE
		if($redirection) {
			redirect('acces-refuse', 'location');
		}
		return false;
	}


	/**
	 * check access for referees
	 *
	 * @param bool $redirection
	 * 
	 * @return bool
	 */
	function refereeAccess($redirection = true)
	{
		$this->load->model('profiles_model');
		if(Profiles_model::isReferee()) {
			return true;
		}
		if($redirection) {
			redirect('acces-refuse', 'location');
		}
		return false;
	}


	//--------------------------------------------------------------------------
	//
	// COMPONENTS MANAGEMENT
	//
	//--------------------------------------------------------------------------


	/**
	 * check if a component is tied to a list of profiles
	 *
	 * @param string $component_name, the component to search for
	 * @param array $profiles
	 *
	 * @return bool
	 */
	function hasAccessByProfileAndComponent ($component_name, $profiles)
	{
		if(empty($profiles)) {
			return false;
		} else {
			$this->db->select('rel_profile_id')
					->from('components')
					->join('rel_profile_component', 'rel_component_id = component_id', 'left')
					->where('component_name', $component_name)
					->where_in('rel_profile_id', $profiles);
			$query = $this->db->get();
			if($this->db->_error_message()) {
				_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
			}
			$nb = $query->num_rows();
			if(empty($nb)) {
				return false;
			}
			return true;
		}
	}


}

/* End of file permissions_model.php */
/* Location: ./application/models/permissions_model.php */