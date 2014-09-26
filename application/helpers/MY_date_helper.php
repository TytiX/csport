<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CSPORT
 * refereeing and matches management tool
 *
 * Copyright (c) 2012 Marie Kuntz - Lezard Rouge
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

// ------------------------------------------------------------------------

/**
 * CodeIgniter Date Helpers
 * extended
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 *
 * @version $Id: MY_date_helper.php 65 2014-03-03 09:01:25Z lezardro $
 * @author  Marie Kuntz / Lezard Rouge
 */

// ------------------------------------------------------------------------

/**
 * _splitDateIso
 * returns a ISO date/datetime (YYYY-mm-dd H:i:s) as an array
 *
 * @param	string $date
 *
 * @return	array('Y', 'm', 'd', 'H', 'i', 's')
 */
if ( ! function_exists('_splitDateIso'))
{
	function _splitDateIso($date)
	{
		$tabDate = array();
		if( ! empty($date)) {
			if(strlen($date) < 10) {
				return $tabDate;
			}
			$tabDate['d']  = substr($date, 8, 2);
			$tabDate['m']  = substr($date, 5, 2);
			$tabDate['Y']  = substr($date, 0, 4);
			$tabDate['H']  = '00';
			$tabDate['i']  = '00';
			$tabDate['s']  = '00';
			if(strlen($date) > 11) {
				$tabDate['H']  = substr($date, 11, 2);
				$tabDate['i']  = substr($date, 14, 2);
				$tabDate['s']  = substr($date, 17, 2);
			}
		}
		return $tabDate;
	}
}


/**
 * _splitDateFr
 * returns a french formatted date/datetime and returns it as an array
 *
 * @param	string $date
 *
 * @return	array('Y', 'm', 'd', 'H', 'i', 's')
 */
if ( ! function_exists('_splitDateFr'))
{
	function _splitDateFr($date)
	{
		$tabDate = array();
		if( ! empty($date)) {
			if(strlen($date) < 10) {
				return $tabDate;
			}
			$tabDate['d']  = substr($date, 0, 2);
			$tabDate['m']  = substr($date, 3, 2);
			$tabDate['Y']  = substr($date, 6, 4);
			$tabDate['H']  = '00';
			$tabDate['i']  = '00';
			$tabDate['s']  = '00';
			if(strlen($date) > 11) {
				$tabDate['H']  = substr($date, 11, 2);
				$tabDate['i']  = substr($date, 14, 2);
				$tabDate['s']  = substr($date, 17, 2);
			}
		}
		return $tabDate;
	}
}


/**
 * transforme une date ISO en une date FR
 *
 * @param str $date, la date/heure à transformer
 * @param str $prefix, le préfixe à mettre devant l'heure
 * @param bool $heure, s'il faut afficher l'heure (si présente) ; default TRUE
 *
 * @return str $date, la date transformée
 */
if ( ! function_exists('_date2Fr'))
{
	function _date2Fr($date, $prefix = '', $affiche_heure = true)
	{
		if( empty($date)) {
			return '';
		}
		$tab = _splitDateIso($date);
		if(count($tab) < 6) {
			return '';
		}
		$heure = '';
		if(strlen($date) > 10 && $affiche_heure) {
			$heure = ' ' . $prefix . ' ' . $tab['H'] . 'h' . $tab['i'] . ' ' . $tab['s'] . 's';
		}
		$date = $tab['d'] . '/' . $tab['m'] . '/' . $tab['Y'] . $heure;
		return $date;
	}
}


/**
 * transforme une date FR en date ISO
 *
 * @param str $date, la date à transformer
 *
 * @return str $date, la date transformée
 */
if ( ! function_exists('_date2ISO'))
{
	function _date2ISO($date)
	{
		if( empty($date)) {
			return '';
		}
		$tab = _splitDateFr($date);
		if(count($tab) < 6) {
			return '';
		}
		$date  = $tab['Y'] . "-" . $tab['m'] . "-" . $tab['d'];
		return $date;
	}
}

/**
 * transforms a fr formatted date/datetime into an ISO date/datetime
 *
 * @param str $date
 *
 * @return str $date
 */
if ( ! function_exists('_dateTime2ISO'))
{
	function _dateTime2ISO($date)
	{
		if( empty($date)) {
			return '';
		}
		$tab = _splitDateFr($date);
		if(count($tab) < 6) {
			return '';
		}
		$date  = $tab['Y'] . "-" . $tab['m'] . "-" . $tab['d'] . ' '
			. $tab['H'] . ':' . $tab['i'] . ':' . $tab['s'];
		return $date;
	}
}


/**
 * check a iso/fr date validity
 *
 * @param string $date
 * @param string $format, iso|fr
 *
 * @return bool
 */
if ( ! function_exists('_myCheckDate'))
{
	function _myCheckDate($date, $format)
	{
		if(empty($date)) {
			return false;
		}
		if($format == 'iso') {
			$tabDate = _splitDateIso($date);
		} else {
			$tabDate = _splitDateFr($date);
		}
		if (count($tabDate) < 3) {
			return false;
		}
		$check = checkdate($tabDate['m'], $tabDate['d'], $tabDate['Y']);
		return $check;
	}
}


/**
 * check if a date is not empty
 *
 * @param string $date
 *
 * @return bool true if not empty
 */
if ( ! function_exists('_dateNotEmpty'))
{
	function _dateNotEmpty($date)
	{
		if(empty($date) || $date == '0000-00-00' || $date == '00/00/0000') {
			return false;
		}
		return true;
	}
}

/**
 * transforms a HHhMM hour to HH:MM
 *
 * @param string $hour
 *
 * @return string $hour
 */
if ( ! function_exists('_hour2Iso'))
{
	function _hour2Iso($hour)
	{
		if(empty($hour)) {
			return '00:00:00';
		}
		$hour = str_replace('h', ':', $hour);
		$hour .= ':00';
		return $hour;
	}
}





/* End of file MY_date_helper.php */
/* Location: ./application/helpers/MY_date_helper.php */