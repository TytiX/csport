<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * LIBRARY extended
 * Form_validation
 *
 * @version $Id: MY_Form_validation.php 65 2014-03-03 09:01:25Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

class MY_Form_validation extends CI_Form_validation {


	/**
	 * constructeur
	 * load the new config set in config/form_validation.php
	 *
	 * @param $config
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		parent::set_error_delimiters('<div class="alert alert-danger">','</div>');
	}


	/**
	 * check if the input is a valid iso date
	 *
	 * @param str $date_fr (YYYY-mm-dd)
	 */
	function isValidDateIso($date_iso)
	{
		$this->set_message('isValidDateIso', 'Le champ %s ne contient pas une date valide.');
		$this->CI->load->helper('date');
		$dateArray = _splitDateIso($date_iso);

		if( ! checkdate($dateArray['m'], $dateArray['d'], $dateArray['Y'])) {
			return false;
		}
		return true;
	}


	/**
	 * check if the input is a valid fr date
	 *
	 * @param str $date_fr (dd/mm/YYYY)
	 */
	function isValidDateFr($date_fr)
	{
		$this->set_message('isValidDateFr', 'Le champ %s ne contient pas une date valide.');
		$this->CI->load->helper('date');
		$dateArray = _splitDateFr($date_fr);
		if( ! checkdate($dateArray['m'], $dateArray['d'], $dateArray['Y'])) {
			return false;
		}
		return true;
	}


}

/* End of file MY_Form_validation.php */
/* Location: ./application/libraries/MY_Form_validation.php */