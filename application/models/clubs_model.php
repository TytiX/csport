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
 * clubs management
 *
 * @version $Id: clubs_model.php 94 2014-09-24 11:37:03Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 *
 */

class Clubs_model extends CI_Model
{

	function __construct ()
	{
		parent::__construct();
	}


	function getAll($criteria, $limit = 0, $nb = CRH_NB_RECORD)
	{
		$result = array();
		$this->db->select('SQL_CALC_FOUND_ROWS *', false)
				->from('clubs');
		//-----------------------------
		// limit (if $nb = 0 -> get all)
		if($nb > 0) {
			$this->db->limit($nb, $limit);
		}
		//-----------------------------
		// filters
		if(isset($criteria['cactive']) && $criteria['cactive'] != 'all') {
			$this->db->where('club_active', $criteria['cactive']);
		}
		if( ! isset($criteria['cindep']) || empty($criteria['cindep'])) {
			$this->db->where('club_id != ' . INDEP_CLUB);
		}
		//-----------------------------
		// ordering
		$this->db->order_by('club_name asc');
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
	 * return active clubs as array
	 * (used for dropdowns)
	 *
	 * @param bool $default, true if there must be a default value
	 *
	 * @return array $club_array (club_id => club_name)
	 */
	function getClubsAsArray($default = false, $no_club = false)
	{
		$club_array = array();
		$criteria = array('cactive' => 1);
		if($default) {
			$club_array[''] = '--';
		}
		if($no_club) {
			$criteria['cindep'] = 1;
		}
		$results = self::getAll($criteria, 0, 0);
		foreach ($results['result'] as $club) {
			$club_array[$club->club_id] = $club->club_name;
		}
		return $club_array;
	}


}

/* End of file clubs_model.php */
/* Location: ./application/models/clubs_model.php */