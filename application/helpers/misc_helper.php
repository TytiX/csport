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
 * See LICENSE.TXT file for more information.
 *
 * @copyright  Copyright (c) 2013-2014 Marie Kuntz - Lezard Rouge (http://www.lezard-rouge.fr)
 * @license    GNU-GPL v3 http://www.gnu.org/licenses/gpl.html
 * @version    1.0
 * @author     Marie Kuntz - Lezard Rouge SARL - www.lezard-rouge.fr - info@lezard-rouge.fr
 */

// ------------------------------------------------------------------------

/**
 * Miscellaneous Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 *
 *
 * @version $Id: misc_helper.php 82 2014-08-22 10:12:50Z lezardro $
 * @author  Marie Kuntz / Lezard Rouge
 */

// ------------------------------------------------------------------------

/**
 * _set_result_message
 *
 * format a message
 *
 * @access	private
 *
 * @param	string $message
 * @param	string $type : error|warning|info|success|perm_success
 *
 * @return	string
 */
if ( ! function_exists('_set_result_message'))
{
	function _set_result_message($message, $type)
	{
		$buffer = '';
		if( ! empty($message)) {
			$buffer = '<div class=" alert alert-' . $type . '">' . $message . '</div>';
		}
		return $buffer;
	}
}

// ------------------------------------------------------------------------

/**
 * _criticalError
 *
 * display an critical error and stop
 *
 * @access	private
 *
 * @param	string $message
 *
 * @return void (echo)
 */
if ( ! function_exists('_criticalError'))
{
	function _criticalError($message)
	{
		echo _set_result_message($message, CRH_TYPE_MSG_ERROR);
		exit;
	}
}


// -----------------------------------------------------------------------------

/**
 * return $nb first words of a sentence
 *
 * @param str $str, the string to cut
 * @param int $nb, number of words to return
 *
 * @return str $new_str
 */
function _getFirstWords ($str, $nb)
{
	$new_str = '';
	$tmp = explode(' ', $str);
	$limit = $nb;
	if(count($tmp) < $nb) {
		$limit = count($tmp);
	}
	for ($i = 0; $i < $limit; $i++) {
		$new_str .= $tmp[$i] . ' ';
	}
	$new_str .= '[...]';
	return $new_str;
}

// -----------------------------------------------------------------------------

/**
 * var_dump in ugly box
 *
 * @param mixed $object
 *
 * @return void (echo)
 */
function dump($object)
{
	echo '<pre style="background-color: #EDEDED; border: 1px solid #999; padding: 10px; ">';
	var_dump($object);
	echo '</pre>';
}

// -----------------------------------------------------------------------------

/**
 * var_dump in smooth box
 *
 * @param mixed $object
 *
 * @return void (echo)
 */
function dumpSmooth($object)
{
	echo '<pre style="background-color: #FFF7A5; border: 1px solid #999; padding: 10px; ">';
	var_dump($object);
	echo '</pre>';
}

// -----------------------------------------------------------------------------

/**
 * protect exported data
 *
 * @param str $str
 *
 * @return str $str
 */
function protegeExport($str)
{
	$str = trim($str);
	$str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
	$str = str_replace(";",",",$str);
	$str = str_replace("\n"," ",$str);
	$str = str_replace("\r"," ",$str);
	if(is_numeric($str) && substr($str,0,1) == "0" && substr($str, -1) != '.') {
		$str = $str.".";
	}
	if($str=="") {
		$str = " ";
	}
	return $str;
}

// -----------------------------------------------------------------------------

/**
 * protect imported data
 *
 * @param str $str
 *
 * @return str $str
 */
function protegeImport($str, $to_encoding, $from_encoding)
{
	$str = trim($str);
	$str = convertEncoding($str, $to_encoding, $from_encoding);
	$str = htmlentities($str, ENT_QUOTES, $to_encoding);
	$str = str_replace("\n"," ",$str);
	$str = str_replace("\r"," ",$str);
	return $str;
}

// -----------------------------------------------------------------------------

/**
 * replace \n by html <br>
 *
 * @param str $str
 *
 * @return str $str
 */
function n2br($str)
{
	return str_replace("\n", "<br>", $str);
}

// -----------------------------------------------------------------------------

/**
 * personal ucfirst with mb_* functions
 *
 * @param string $string
 *
 * @return string
 */
function mb_ucfirst($string)
{
	return mb_strtoupper(mb_substr($string, 0, 1)).mb_strtolower(mb_substr($string, 1));
}

// -----------------------------------------------------------------------------

/**
 * convert a string encoding to another
 *
 * @param string $string, the string to convert
 * @param string $to_encoding, the new encoding
 * @param string $from_encoding, the original encoding
 *
 * @return string $string, the encoded string
 */
function convertEncoding($string, $to_encoding, $from_encoding)
{
   if( ! empty($from_encoding) && ! empty($to_encoding)
	   && $from_encoding != $to_encoding) {
	   $string = mb_convert_encoding($string, $to_encoding, $from_encoding);
   }
   return $string;
}

// -----------------------------------------------------------------------------

/**
 * create a login from name
 *
 * @param string $name
 *
 * @return string $login
 */
function createLogin($name)
{
	$name = mb_strtolower($name);
	$tmp = explode(' ', $name);
	if(count($tmp) > 1) {
		$login = substr($tmp[1], 0, 1) . $tmp[0];
	} else {
		$login = $name;
	}
	return $login;
}



// -----------------------------------------------------------------------------

/* End of file misc_helper.php */
/* Location: ./application/helpers/misc_helper.php */