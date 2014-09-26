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
 * teams management
 *
 * @version $Id: teams.php 65 2014-03-03 09:01:25Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teams extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('teams_model');
	}


	/**
	 * default controller
	 */
	function index()
	{
		redirect('', 'location');
	}


	function getTeamsByClub()
	{
		Permissions_model::userHasAccess('team_list');

		$club = $this->input->post('data');
		$teams = Teams_model::getTeamsByClub($club);
		echo json_encode($teams);
	}


}

/* End of file teams.php */
/* Location: ./application/controllers/teams.php */