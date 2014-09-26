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
 * categories management
 *
 * @version $Id: categories_model.php 90 2014-09-22 19:07:05Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 *
 */

class Categories_model extends CI_Model
{

	function __construct ()
	{
		parent::__construct();
	}


	function getAll($criteria, $limit = 0, $nb = CRH_NB_RECORD)
	{
		$result = array();
		$this->db->select('SQL_CALC_FOUND_ROWS *', false)
				->from('categories');
		//-----------------------------
		// limit (if $nb = 0 -> get all)
		if($nb > 0) {
			$this->db->limit($nb, $limit);
		}
		//-----------------------------
		// filters
		if(isset($criteria['cactive']) && $criteria['cactive'] != 'all') {
			$this->db->where('category_active', $criteria['cactive']);
		}
		//-----------------------------
		// ordering
		$this->db->order_by('category_order asc');
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
	 * return active categories as array
	 * (used for dropdowns)
	 *
	 * @param bool $default, true if there must be a default value
	 *
	 * @return array $category_array (category_id => category_name)
	 */
	function getCategoriesAsArray($default = false)
	{
		$category_array = array();
		if($default) {
			$category_array['0'] = '--';
		}
		$results = self::getAll(array('cactive' => 1), 0, 0);
		foreach ($results['result'] as $category) {
			$category_array[$category->category_id] = $category->category_name;
		}
		return $category_array;
	}


	/**
	 * fetch only some categories
	 *
	 * @param array $filtered_categories
	 *
	 * @return array(category_id => category_name)
	 */
	function getFilteredCategoriesAsArray($filtered_categories)
	{
		$array = array();
		if(! empty($filtered_categories)) {
			$this->db->select('*')
					->from('categories')
					->where_in('category_id', $filtered_categories);
			$query = $this->db->get();
			if($this->db->_error_message()) {
				_criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
			}
			foreach ($query->result() as $row) {
				$array[$row->category_id] = $row->category_name;
			}
		}
		return $array;
	}


}

/* End of file categories_model.php */
/* Location: ./application/models/categories_model.php */