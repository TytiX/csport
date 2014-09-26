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
 * profiles management
 *
 * @version $Id: profiles_model.php 65 2014-03-03 09:01:25Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 *
 */

class Profiles_model extends CI_Model
{

	static $profile_admin = 1;
	static $profile_general_planning_manager = 2;
	static $profile_general_referee_manager = 3;
	static $profile_club_planning_manager = 4;
	static $profile_club_referee_manager = 5;
	static $profile_team_manager = 6;
	static $profile_member = 8;


	function __construct ()
	{
		parent::__construct();
	}


	/**
	 * get profiles
	 *
	 * @param str $exclude, la liste des profils à ne pas afficher
	 *
	 * @return object $query->result
	 */
	function getAll()
	{
		$this->db->select()->from('profiles')->order_by('profile_order');
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		return $query->result();
	}


	/**
	 * get profiles as array (used for dropdowns)
	 *
	 * @param bool $default, true if there must be a default value
	 *
	 * @return array profil_id => profil_nom
	 */
	function getProfilesAsArray($default = false)
	{
		$profiles = self::getAll();
		$array = array();
		if($default) {
			$array[0] = '--';
		}
		foreach($profiles as $profile) {
			$array[$profile->profile_id] = $profile->profile_name;
		}
		return $array;
	}

	
	//--------------------------------------------------------------------------
	//
	// USER/PROFIL MANAGEMENT
	//
	//--------------------------------------------------------------------------


	/**
	 * get a user's profile ids
	 * 
	 * @param int $user_id
	 *
	 * @return array $profiles(key => rel_profile_id)
	 */
	function getProfileIdsByUser($user_id)
	{
		$this->db->select('rel_profile_id')
				->from('rel_user_profile')
				->where('rel_user_id', $user_id);
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		$profiles = array();
		foreach($query->result() as $row) {
			$profiles[] = $row->rel_profile_id;
		}
		return $profiles;
	}

	/**
	 * get a user's profiles
	 *
	 * @param int $user_id
	 *
	 * @return array $profiles(profile_id => profile_name)
	 */
	function getProfilesByUser($user_id)
	{
		$this->db->select('profile_id, profile_name')
				->from('profiles')
				->join('rel_user_profile', 'rel_profile_id = profile_id', 'left')
				->where('rel_user_id', $user_id);
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		$profiles = array();
		foreach($query->result() as $row) {
			$profiles[$row->profile_id] = $row->profile_name;
		}
		return $profiles;
	}


	/**
	 * set a user's profiles
	 *
	 * @param int $user_id
	 * @param array $profiles, array(key => $profile_id)
	 *
	 * @return void
	 */
	function setProfilesByUser($user_id, $profiles)
	{
		// delete all previous relations
		self::deleteProfilesByUser($user_id);
		// insert
		foreach($profiles as $profile_id) {
			self::insertProfileByUser($user_id, $profile_id);
		}
	}


	/**
	 * insert a relation user/profile
	 *
	 * @param int $user_id
	 * @param int $profile_id
	 *
	 * @return void
	 */
	function insertProfileByUser($user_id, $profile_id)
	{
		$this->db->insert('rel_user_profile', array(
				'rel_user_id'    => $user_id,
				'rel_profile_id' => $profile_id,
		));
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
	}


	/**
	 * delete a user's profiles
	 *
	 * @param int $user_id
	 *
	 * @return void
	 */
	function deleteProfilesByUser($user_id)
	{
		if( ! empty($user_id)) {
			$this->db->from('rel_user_profile')
				->where('rel_user_id', $user_id)
				->delete();
			if($this->db->_error_message()) {
				_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
			}
		}
	}


	//--------------------------------------------------------------------------
	//
	// DEDICATED FUNCTIONS
	//
	//--------------------------------------------------------------------------


	/**
	 * check if current connected user is admin
	 * 
	 * @return bool
	 */
	function isAdmin()
	{
		return self::_checkProfile(self::$profile_admin);
	}


	/**
	 * check if current connected user is general planning manager
	 *
	 * @return bool
	 */
	function isGeneralPlanningManager()
	{
		return self::_checkProfile(self::$profile_general_planning_manager);
	}


	/**
	 * check if current connected user is general referee manager
	 *
	 * @return bool
	 */
	function isGeneralRefereeManager()
	{
		return self::_checkProfile(self::$profile_general_referee_manager);
	}


	/**
	 * check if current connected user is club manager
	 *
	 * @return bool
	 */
	function isClubPlanningManager()
	{
		return self::_checkProfile(self::$profile_club_planning_manager);
	}


	/**
	 * check if current connected user is club referee manager
	 *
	 * @return bool
	 */
	function isClubRefereeManager()
	{
		return self::_checkProfile(self::$profile_club_referee_manager);
	}


	/**
	 * check if current connected user is team manager
	 *
	 * @return bool
	 */
	function isTeamManager()
	{
		return self::_checkProfile(self::$profile_team_manager);
	}


	/**
	 * check if user is only member (has no rights)
	 *
	 * @return bool
	 */
	function isOnlyMember()
	{
		$serial_profiles = $this->session->userdata('sess_profiles');
		$user_profiles = unserialize($serial_profiles);
		if(count($user_profiles) == 1 && in_array(self::$profile_member, $user_profiles)) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * check if the current connected user is referee
	 *
	 * @return bool
	 */
	function isReferee()
	{
		$is_referee = $this->session->userdata('sess_user_isreferee');
		if($is_referee == 1) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * check if current connected user has a precise profile
	 *
	 * @param int $profile
	 *
	 * @return boolean
	 */
	function _checkProfile($profile)
	{
		$serial_profiles = $this->session->userdata('sess_profiles');
		$user_profiles = unserialize($serial_profiles);
		if(in_array($profile, $user_profiles)) {
			return true;
		} else {
			return false;
		}
	}


}

/* End of file profiles_model.php */
/* Location: ./application/models/profiles_model.php */