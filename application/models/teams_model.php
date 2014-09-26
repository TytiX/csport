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
 * teams management
 *
 * @version $Id: teams_model.php 71 2014-05-09 09:19:14Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 *
 */

class Teams_model extends CI_Model
{

	function __construct ()
	{
		parent::__construct();
	}


	/**
	 * returns the possible status for a team
	 * 
	 * @return array $status
	 */
	function getTeamStatus()
	{
		$status = array(
			'C'  => 'Convoqué',
			'OK' => 'Confirmé',
			'R'  => 'Demande de report',
			'F'  => 'Forfait',
			'I'  => 'Indisponible'
		);
		return $status;
	}

	/**
	 * returns the possible actions for a team/club manager
	 *
	 * @return array $actions
	 */
	function getTeamActions($default = true)
	{
		$actions = array(
			'OK' => 'Confirmer',
			'R'  => 'Demande de report',
			'F'  => 'Forfait',
		);
		if($default) {
			$actions = array('C' => 'À confirmer') + $actions;
		}
		return $actions;
	}
	
	
	/**
	 * get teams
	 *
	 * @param array $criteria
	 * @param int $limit
	 * @param int $nb
	 * 
	 * @return array
	 */
	function getAll($criteria, $limit = 0, $nb = CRH_NB_RECORD)
	{
		$result = array();
		$this->db->select('SQL_CALC_FOUND_ROWS *', false)
				->from('teams');
		//-----------------------------
		// limit (if $nb = 0 -> get all)
		if($nb > 0) {
			$this->db->limit($nb, $limit);
		}
		//-----------------------------
		// filters
		if(isset($criteria['active']) && $criteria['active'] != 'all') {
			$this->db->where('team_active', $criteria['active']);
		}
		if(isset($criteria['club']) && ! empty($criteria['club'])) {
			$this->db->where('team_club_id', $criteria['club']);
		}
		//-----------------------------
		// ordering
		$this->db->order_by('team_name asc');
		//--
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}

		// get total number of records
		$total_query = $this->db->query('SELECT FOUND_ROWS() as total');
		$row_total = $total_query->row();
		$result['total'] = $row_total->total;
		// get results
		$result['result'] = $query->result();
		return $result;
	}


	/**
	 * return teams as an array
	 *
	 * @param bool $default, true to add a default choice
	 *
	 * @return array $team_array(id => name)
	 */
	function getTeamsAsArray($default = false)
	{
		$team_array = array();
		if($default) {
			$team_array['0'] = '--';
		}
		$results = self::getAll(array('active' => 1), 0, 0);
		foreach ($results['result'] as $team) {
			$team_array[$team->team_id] = $team->team_name;
		}
		return $team_array;
	}


	/**
	 * fetch teams where user_id is manager
	 *
	 * @param int $user_id
	 *
	 * @return array(team_id => team_name)
	 */
	function getTeamsByUser($user_id)
	{
		$this->db->select('*')
				->from('teams')
				->join('rel_team_manager', 'rel_team_id = team_id', 'left')
				->where('rel_user_id', $user_id)
				->where('team_active', 1);
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		return $query->result();
	}

	/**
	 * fetch teams where user_id is manager
	 *
	 * @param int $user_id
	 *
	 * @return array(team_id => team_name)
	 */
	function getTeamsByUserAsArray($user_id)
	{
		$this->db->select('*')
				->from('teams')
				->join('rel_team_manager', 'rel_team_id = team_id', 'left')
				->where('rel_user_id', $user_id)
				->where('team_active', 1);
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		$array = array();
		foreach ($query->result() as $row) {
			$array[$row->team_id] = $row->team_name;
		}
		return $array;
	}


	/**
	 * fetch a club's teams
	 * 
	 * @param int $club_id
	 *
	 * @return array(team_id => team_name)
	 */
	function getTeamsByClub($club_id)
	{
		$this->db->select('*')
				->from('teams')
				->where('team_club_id', $club_id)
				->where('team_active', 1)
				->order_by('team_name');
		$query = $this->db->get();
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		$array = array();
		foreach ($query->result() as $row) {
			$array[$row->team_id] = $row->team_name;
		}
		return $array;
	}


	/**
	 * get a team
	 *
	 * @param int $team_id
	 *
	 * @return query object
	 */
	function getTeam($team_id)
	{
		$this->db->select()
				->from('teams')
				->where('team_id', $team_id);
		$query = $this->db->get();
		return $query->row();
	}


	/**
	 * import teams & team managers
	 *
	 * @param string $fieldname, name of $_FILES field
	 * @param string $new_name, the new name of imported file
	 *
	 * @return array(success (bool), message (string))
	 */
	function importTeams($fieldname, $new_name)
	{
		$this->load->model('files_model');
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

		$i = 0; // line number
		while(($content = fgetcsv($handle, 0, Files_model::CSV_SEP)) !== false) {
			if($i > 0) { // ignore first line
				$team_id = protegeImport($content[0], $to_encoding, $from_encoding); // ID : if not empty, this is an update
				$team = array(
					'team_name'        => protegeImport($content[1], $to_encoding, $from_encoding),
					'team_club_id'     => protegeImport($content[2], $to_encoding, $from_encoding),
					'team_category_id' => protegeImport($content[3], $to_encoding, $from_encoding),
					'team_active'      => protegeImport($content[4], $to_encoding, $from_encoding),
				);
				// team managers
				$array_managers = array();
				if($team['team_active'] == 1) {
					$managers = protegeImport($content[5], $to_encoding, $from_encoding);
					if( ! empty($managers)) {
						$array_managers = explode(',', $managers);
					}
				}
				if(empty($team_id)) {
					$team_id = self::_newTeam($team);
				} else {
					$team_id = self::_updateTeam($team_id, $team);
				}
				self::setTeamManagers($team_id, $array_managers);
			}
			$i++;
		}
		return array('success' => true, 'message' => ($i - 1) . " lignes(s) ont été importées ou mises à jour.");
	}


	/**
	 * insert a new team
	 *
	 * @param array $data
	 *
	 * @return int $team_id, the new ID
	 */
	function _newTeam($data)
	{
		$this->db->insert('teams', $data);
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		$team_id = $this->db->insert_id();
		return $team_id;
	}


	/**
	 * update a team
	 *
	 * @param int $team_id, team ID
	 * @param array $data, data to update
	 *
	 * @return int $team_id, the new ID
	 */
	function _updateTeam($team_id, $data)
	{
		$this->db->where('team_id', $team_id);
		$this->db->update('teams', $data);
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
		return $team_id;
	}


	/**
	 * insert relations team/manager (user)
	 * (used for import)
	 *
	 * @param int $team_id
	 * @param array $managers
	 *
	 * @return void
	 */
	function setTeamManagers($team_id, $managers)
	{
		self::deleteTeamManagers($team_id);
		if( ! empty($managers)) {
			foreach ($managers as $manager_id) {
				self::insertTeamManager($team_id, trim($manager_id));
			}
		}
	}


	/**
	 * insert relations team/manager (user)
	 * (user for insert/update user)
	 * 
	 * @param int $user_id
	 * @param array $teams
	 *
	 * @return void
	 */
	function setTeamManagerByUser($user_id, $teams)
	{
		self::deleteTeamManagerByUser($user_id);
		foreach ($teams as $team_id) {
			self::insertTeamManager($team_id, $user_id);
		}
	}


	/**
	 * insert a relation user(manager)/team
	 *
	 * @param int $team_id
	 * @param int $manager_id
	 * 
	 * @return void
	 */
	function insertTeamManager($team_id, $manager_id)
	{
		$this->db->insert('crh_rel_team_manager', array(
			'rel_user_id' => $manager_id,
			'rel_team_id' => $team_id
		));
		if($this->db->_error_message()) {
			_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
		}
	}


	/**
	 * delete managers for a team
	 *
	 * @param int $team_id
	 *
	 * @return void
	 */
	function deleteTeamManagers($team_id)
	{
		if( ! empty($team_id)) {
			$this->db->from('crh_rel_team_manager')
				->where('rel_team_id', $team_id)
				->delete();
			if($this->db->_error_message()) {
				_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
			}
		}
	}

	
	/**
	 * delete teams management for a user
	 *
	 * @param int $user_id
	 *
	 * @return void
	 */
	function deleteTeamManagerByUser($user_id)
	{
		if( ! empty($user_id)) {
			$this->db->from('crh_rel_team_manager')
				->where('rel_user_id', $user_id)
				->delete();
			if($this->db->_error_message()) {
				_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
			}
		}
	}

	
}


/* End of file teams_model.php */
/* Location: ./application/models/teams_model.php */