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
 * users management
 *
 * @version $Id: users_model.php 88 2014-09-15 08:28:15Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 *
 */

class Users_model extends CI_Model
{

	function __construct ()
	{
		parent::__construct();
		$this->load->model('profiles_model');
	}


	/**
	 * return refereeeing degrees
	 *
	 * @return array
	 */
	function getRefereeDegrees($default = false)
	{
		$degrees = array(
			'stagiaire'   => 'stagiaire',
			'D1'          => 'D1',
			'D2'          => 'D2',
			'D3'          => 'D3',
			'D4'          => 'D4',
			'D5'          => 'D5',
			'superviseur' => 'superviseur'
		);
		if($default) {
			array_unshift($degrees, '--');
		}
		return $degrees;
	}


	/**
	 * returns the possible status for a referee
	 *
	 * @return array $status
	 */
	function getRefereeStatus()
	{
		$status = array(
			'C'  => 'Convoqué',
			'OK' => 'Confirmé',
			'I'  => 'Indisponible'
		);
		return $status;
	}

	/**
	 * get users
	 *
	 * @param array $criteria, a list of filters
	 * @param int $limit, from number
	 * @param int $nb, to number ; default CRH_NB_RECORD
	 *
	 * @return array $result (total, result)
	 */
	function getAllUsers($criteria, $limit = 0, $nb = CRH_NB_RECORD)
	{
		$result = array();
		$this->db->select('SQL_CALC_FOUND_ROWS
					user_id, user_name, user_address, user_email, user_phone, user_mobile,
					user_birthdate, DATE_FORMAT(user_birthdate, \'' . CRH_SQL_DATE_FORMAT . '\') as user_birthdate_format,
					user_login, user_licence, user_isreferee, user_referee_degree,
					user_active, user_club,
					club_name', false)
				->from('users')
				->join('clubs', 'club_id = user_club', 'left');
		// limit (if $nb = 0 -> get all)
		if($nb > 0) {
			$this->db->limit($nb, $limit);
		}

		//-----------------------------
		// filters
		//-----------------------------
		if(isset($criteria['uactive']) && $criteria['uactive'] != 'all') {
			$this->db->where('user_active',  $criteria['uactive']);
		}
		if(isset($criteria['uname']) && ! empty($criteria['uname'])) {
			$this->db->where('(user_name like "%' . $criteria['uname'] . '%")');
		}
		if(isset($criteria['club']) && $criteria['club'] != '') {
			$this->db->where('user_club', $criteria['club']);
		}
		if(isset($criteria['ureferee']) && $criteria['ureferee'] != 'all') {
			$this->db->where('user_isreferee', $criteria['ureferee']);
		}
		//-----------------------------
		// ordering
		if(isset($criteria['order_uname']) && ! empty($criteria['order_uname'])) {
			if($criteria['order_uname'] == 'desc') {
				$this->db->order_by('user_name desc');
			} else {
				$this->db->order_by('user_name asc');
			}
		} elseif(isset($criteria['order_udegree']) && ! empty($criteria['order_udegree'])) {
			if($criteria['order_udegree'] == 'desc') {
				$this->db->order_by('user_referee_degree desc, user_name desc');
			} else {
				$this->db->order_by('user_referee_degree asc, user_name asc');
			}
		} elseif(isset($criteria['order_club']) && ! empty($criteria['order_club'])) {
			if($criteria['order_club'] == 'desc') {
				$this->db->order_by('club_name desc, user_name desc');
			} else {
				$this->db->order_by('club_name asc, user_name asc');
			}
		} else {
			$this->db->order_by('user_name asc');
		}
		//--
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		//echo $this->db->last_query();

		// get total number of records
		$total_query = $this->db->query('SELECT FOUND_ROWS() as total');
		$row_total = $total_query->row();
		$result['total'] = $row_total->total;
		// get users results
		$result['result'] = $query->result();
		return $result;
	}


	/**
	 * get users active and who have to connect to site
	 *
	 * @return query result
	 */
	function getActiveUsers()
	{
		$this->db->select('user_id, user_name, user_email, user_login, user_password')
				->from('users')
				->join('rel_user_profile', 'rel_user_id = user_id', 'left')
				->where('user_active', 1)
				->where('(user_isreferee = 1
					OR (rel_profile_id IS NOT NULL
					AND rel_profile_id != ' . Profiles_model::$profile_member . '))')
				->group_by('user_id');
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		//echo $this->db->last_query(); 
		return $query->result();
	}


	/**
	 * get active users
	 *
	 * @return array(user_id => user_name)
	 */
	function getUsersAsArray()
	{
		$user_array = array();
		$results = self::getAllUSers(array(), 0, 0);
		foreach ($results['result'] as $user) {
			$user_array[$user->user_id] = $user->user_name;
		}
		return $user_array;
	}


	/**
	 * get referees as array
	 *
	 * @param int $club_id, 0 if all, else filtered on club_id
	 * @param bool $default, true if the array must begin with a default choice
	 *
	 * @return array(user_id => user_name)
	 */
	function getRefereesAsArray($club_id = 0, $default = false)
	{
		$user_array = array();
		$criteria = array('ureferee' => 1);
		if( ! empty($club_id)) {
			$criteria['club'] = $club_id;
		}
		$results = Users_model::getAllUsers($criteria, 0, 0);
		foreach ($results['result'] as $user) {
			$user_array[$user->user_id] = $user->user_name;
		}
		if($default) {
			$user_array = array(0 => '--') + $user_array;
		}
		return $user_array;
	}


	/**
	 * get a list of referees and clubs, ordered by club
	 *
	 * @param int $club_id, 0 if all, else filtered on club_id
	 * @param bool $default
	 *
	 * @return array
	 */
	function getRefereesClubsAsArray($club_id = 0, $default = false)
	{
		$user_array = array();
		$criteria = array('ureferee' => 1, 'order_club' => 'asc');
		if( ! empty($club_id)) {
			$criteria['club'] = $club_id;
		}
		$results = Users_model::getAllUsers($criteria, 0, 0);
		$club = -1;
		foreach ($results['result'] as $user) {
			if($club != $user->user_club) {
				$user_array['c;' . $user->user_club . ';0'] = $user->club_name;
				$club = $user->user_club;
			}
			$user_array['u;' . $user->user_club . ';' . $user->user_id] = ' | ' . $user->user_name;
		}
		if($default) {
			$user_array = array('0' => '--') + $user_array;
		}
		return $user_array;
	}


	/**
	 * get referees for a match (users only, not clubs)
	 *
	 * @param array $ids
	 *
	 * @return query object
	 */
	function getRefereesByMatch($ids)
	{
		$this->db->select('user_id, user_name, user_email')
				->from('users')
				->where_in('user_id', $ids);
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		//echo $this->db->last_query();
		return $query->result();
	}


	/**
	 * get team manager for a team
	 *
	 * @param int $team_id
	 *
	 * @return query object
	 */
	function getTeamManagers($team_id)
	{
		$this->db->select('user_id, user_name, user_email')
				->from('users')
				->join('rel_team_manager', 'rel_user_id = user_id', 'left')
				->where('rel_team_id', $team_id)
				->where('user_active', 1);
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		//echo $this->db->last_query();
		return $query->result();
	}



	/**
	 * get a club's manager(s)
	 *
	 * @param int $club_id
	 *
	 * @return query result
	 */
	function getClubManager($club_id)
	{
		$this->db->select('user_id, user_name, user_email')
				->from('users')
				->join('rel_user_profile', 'rel_user_id = user_id', 'left')
				->where('user_club', $club_id)
				->where('user_active', 1)
				->where('rel_profile_id', Profiles_model::$profile_club_planning_manager);
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		//echo $this->db->last_query();
		return $query->result();
	}


	/**
	 * get club's referees manager(s)
	 *
	 * @param int $club_id
	 *
	 * @return query result
	 */
	function getClubRefereeManager($club_id)
	{
		$this->db->select('user_id, user_name, user_email')
				->from('users')
				->join('rel_user_profile', 'rel_user_id = user_id', 'left')
				->where('user_club', $club_id)
				->where('user_active', 1)
				->where('rel_profile_id', Profiles_model::$profile_club_referee_manager);
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		//echo $this->db->last_query();
		return $query->result();
	}


	/**
	 * get a user info
	 *
	 * @param int $user_id
	 *
	 * @return object $user
	 */
	function getUser($user_id)
	{
		$this->db->select('*, DATE_FORMAT(user_birthdate, \'' . CRH_SQL_DATE_FORMAT . '\') as user_birthdate_format', false)
				->from('users')
				->join('clubs', 'club_id = user_club', 'left')
				->where('user_id', $user_id);
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		$user = $query->row();

		// profiles
		$this->load->model('profiles_model');
		$user->profiles = Profiles_model::getProfilesByUser($user_id);
		// team manager
		$this->load->model('teams_model');
		$user->teams = Teams_model::getTeamsByUserAsArray($user_id);

		return $user;
	}


	/**
	 * get a user by login and password
	 * returns false if login/pass don't match
	 *
	 * @param string $login
	 * @param string $pwd
	 *
	 * @return mixed boolean|object
	 */
	function getUserByLogin($login, $pwd)
	{
		$this->db->select('user_id, user_name, user_password, user_email, user_isreferee, user_referee_degree, club_id, club_name')
				->from('users')
				->join('clubs', 'club_id = user_club', 'left')
				->where('user_login', $login)
				->where('user_active', 1);
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		//echo $this->db->last_query();
		$user = $query->row();
		if(empty($user)) {
			return false;
		}
		if(Misc_model::decryptStr($user->user_password) != $pwd) {
			return false;
		}
		unset($user->user_password);
		// profiles
		$this->load->model('profiles_model');
		$user->profiles = Profiles_model::getProfilesByUser($user->user_id);
		// team manager
		$this->load->model('teams_model');
		$user->teams = Teams_model::getTeamsByUserAsArray($user->user_id);

		return $user;
	}


	/**
	 * import users
	 *
	 * @param string $fieldname, name of $_FILES field
	 * @param string $new_name, the new name of imported file
	 *
	 * @return array(success (bool), message (string))
	 */
	function importUsers($fieldname, $new_name)
	{
		$this->load->model('files_model');
		$this->load->model('profiles_model');
		$config['upload_path']   = CRH_PATH_TO_FILES;
		$config['overwrite']     = false;
		$config['allowed_types'] = 'csv';
		$config['file_name']     = $new_name;
		$result = Files_model::uploadFile($fieldname, $config);
		if($result['success'] === false) {
			return array('success' => false, 'message' => $result['message']);
		}

		$handle = fopen(CRH_PATH_TO_FILES . $new_name, 'rb');
		if( ! $handle) {
			return array('success' => false, 'message' => "Le fichier n'a pas pu être lu.");
		}

		$whole_content = fread($handle, filesize(CRH_PATH_TO_FILES . $new_name));
		rewind($handle);
		$to_encoding = 'UTF-8';
		$from_encoding = mb_detect_encoding($whole_content, Files_model::$supported_encoding, true);

		// get all clubs
		/*$this->load->model('clubs_model');
		$all_clubs = Clubs_model::getClubsAsArray();*/

		$i = 0; // line number
		while(($content = fgetcsv($handle, 0, Files_model::CSV_SEP)) !== false) {
			if(count($content) != 25) {
				return array('success' => false, 'message' => "Le fichier n'est pas au bon format ou ne contient pas le bon nombre de colonnes.");
			}
			if($i > 0) { // ignore first line

				$profiles = array();
				$has_profile = false;

				/*$user_club = protegeImport($content[1], $to_encoding, $from_encoding);
				$club_id = array_key_exists($user_club, $all_clubs);
				if(empty($club_id)) {
					$club_id = 0;
				}*/
				$club_id = protegeImport($content[1], $to_encoding, $from_encoding);

				$is_referee = protegeImport($content[21], $to_encoding, $from_encoding);
				if($is_referee === "OUI") {
					$is_referee = 1;
					$degree = protegeImport($content[22], $to_encoding, $from_encoding);
					$referee_degree = str_replace('° ', '', $degree);
					$referee_degree = str_replace('°', '', $referee_degree);
					$has_profile = true;
				} else {
					$is_referee = 0;
					$referee_degree = null;
				}
				$user_id = protegeImport($content[0], $to_encoding, $from_encoding); // ID : if not empty, this is an update
				$user = array(
					'user_name'           => protegeImport($content[3], $to_encoding, $from_encoding),
					'user_address'        => protegeImport($content[4], $to_encoding, $from_encoding),
					'user_email'          => protegeImport($content[5], $to_encoding, $from_encoding),
					'user_mobile'         => protegeImport($content[6], $to_encoding, $from_encoding),
					'user_phone'          => protegeImport($content[7], $to_encoding, $from_encoding),
					'user_birthdate'      => _date2ISO(protegeImport($content[24], $to_encoding, $from_encoding)),
					'user_licence'        => protegeImport($content[23], $to_encoding, $from_encoding),
					'user_isreferee'      => $is_referee,
					'user_referee_degree' => $referee_degree,
					'user_club'           => $club_id
				);
				// profiles
				// responsable planning club
				if(protegeImport($content[8], $to_encoding, $from_encoding) == 'OUI') {
					$profiles[] = 4;
					$has_profile = true;
				}
				// responsable arbitre club
				if(protegeImport($content[9], $to_encoding, $from_encoding) == 'OUI') {
					$profiles[] = 5;
					$has_profile = true;
				}
				// responsable d'équipe
				if(protegeImport($content[10], $to_encoding, $from_encoding) == 'OUI' // resp equipe U9
					|| protegeImport($content[11], $to_encoding, $from_encoding) == 'OUI' // resp equipe U11
					|| protegeImport($content[12], $to_encoding, $from_encoding) == 'OUI' // resp equipe U13
					|| protegeImport($content[13], $to_encoding, $from_encoding) == 'OUI' // resp equipe U15
					|| protegeImport($content[14], $to_encoding, $from_encoding) == 'OUI' // resp equipe U17
					|| protegeImport($content[15], $to_encoding, $from_encoding) == 'OUI' // resp equipe U20
					|| protegeImport($content[16], $to_encoding, $from_encoding) == 'OUI' // resp equipe R4
					|| protegeImport($content[17], $to_encoding, $from_encoding) == 'OUI' // resp equipe R3
					|| protegeImport($content[18], $to_encoding, $from_encoding) == 'OUI' // resp equipe N2
					|| protegeImport($content[19], $to_encoding, $from_encoding) == 'OUI' // resp equipe N1
					|| protegeImport($content[20], $to_encoding, $from_encoding) == 'OUI' // resp equipe N1F
				) {
					$profiles[] = 6;
					$has_profile = true;
				}

				if(empty($user_id)) {
					$user['user_active'] = 1;
					if($has_profile) {
						$user['user_login'] = createLogin($user['user_name']);
						$user['user_password'] = Misc_model::createPassword();
					}
					$user_id = self::_newUser($user);
				} else {
					$user_id = self::_updateUser($user_id, $user);
				}
				if($has_profile === false) {
					$profiles[] = 8; // simple member, no rights
				}
				Profiles_model::setProfilesByUser($user_id, $profiles);
			}
			$i++;
		}
		return array('success' => true, 'message' => ($i - 1) . " lignes(s) ont été importées ou mises à jour.");
	}


	/**
	 * insert or update a user
	 *
	 * @param int $user_id
	 * @param array $data
	 *
	 * @return int $user_id
	 */
	function setUser($user_id, $data)
	{
		if(isset($data->user_password) && ! empty($data->user_password)) {
			$data->user_password = Misc_model::cryptStr($data->user_password);
		} else {
			unset($data->user_password);
		}

		if(empty($data)) {
			return CRH_ERROR_DATA_EMPTY;
		}

		// check if login already exists for another user
		if( ! self::loginAvailable($data->user_login, $user_id)) {
			return CRH_ERROR_LOGIN_UNAVAILABLE;
		} else {

			$profiles = $data->profiles;
			unset($data->profiles);
			$teams = $data->teams;
			unset($data->teams);


			if($user_id == 0) {
				$user_id = self::_newUser($data);
			} else {
				$user_id = self::_updateUser($user_id, $data);
			}

			// update profiles
			$this->load->model('profiles_model');
			Profiles_model::setProfilesByUser($user_id, $profiles);
			// update teams management
			$this->load->model('teams_model');
			Teams_model::setTeamManagerByUser($user_id, $teams);
		}
		return $user_id;
	}


	/**
	 * change password for a user
	 *
	 * @param int $user_id
	 * @param array $data
	 *
	 * @return mixed int|string $user_id|message error
	 */
	function changePassword($user_id, $password)
	{
		if(isset($password) && ! empty($password)) {
			$data->user_password = Misc_model::cryptStr($password);
		} else {
			return CRH_ERROR_DATA_EMPTY;
		}

		$user_id = self::_updateUser($user_id, $data);
		return $user_id;
	}


	/**
	 * check if a login is available
	 *
	 * @param $user_login
	 * @param $user_id
	 *
	 * @return bool
	 */
	function loginAvailable($user_login, $user_id)
	{
		if(empty($user_login)) {
			return true;
		}
		$this->db->select('user_id')
				->from('users')
				->where('user_login', $user_login)
				->where('user_id !=', $user_id);
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		if($query->num_rows() == 0) {
			return true;
		}
		return false;
	}


	/**
	 * insert a new user
	 *
	 * @param array $data
	 *
	 * @return int $user_id, the new ID
	 */
	function _newUser($data)
	{
		$this->db->insert('users', $data);
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		$user_id = $this->db->insert_id();
		return $user_id;
	}


	/**
	 * update a user
	 *
	 * @param int $user_id, user ID
	 * @param array $data, data to update
	 *
	 * @return
	 */
	function _updateUser($user_id, $data)
	{
		$this->db->where('user_id', $user_id);
		$this->db->update('users', $data);
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		return $user_id;
	}


}

/* End of file users_model.php */
/* Location: ./application/models/users_model.php */