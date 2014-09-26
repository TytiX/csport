<?php
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


/**
 * MODEL
 * miscellaneous functions
 *
 * @version $Id: misc_model.php 65 2014-03-03 09:01:25Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 *
 */

class Misc_model extends CI_Model
{

	
	function __construct ()
	{
		parent::__construct();
	}


	/**
	 * converts strange chars to standard latin alphabetic chars
	 *
	 * @param string $string, string to sanitize
	 *
	 * @return str $string, sanitized string
	 */
	function convertStrangeChars($string)
	{
		$conversion_array = array(
			'À'=>'A','�?'=>'A','Â'=>'A','Ã'=>'A','Ä'=>'Ae', '&Auml;'=>'A',
			'Å'=>'A','Ā'=>'A','Ą'=>'A','Ă'=>'A', 'Æ'=>'Ae',
			'Ç'=>'C','Ć'=>'C','Č'=>'C','Ĉ'=>'C','Ċ'=>'C',
			'Ď'=>'D','�?'=>'D','�?'=>'D',
			'È'=>'E','É'=>'E','Ê'=>'E','Ë'=>'E','Ē'=>'E',
			'Ę'=>'E','Ě'=>'E','Ĕ'=>'E','Ė'=>'E',
			'Ĝ'=>'G','Ğ'=>'G','Ġ'=>'G','Ģ'=>'G',
			'Ĥ'=>'H','Ħ'=>'H',
			'Ì'=>'I','�?'=>'I','Î'=>'I','�?'=>'I','Ī'=>'I', 'Ĩ'=>'I','Ĭ'=>'I','Į'=>'I','İ'=>'I',
			'Ĳ'=>'IJ','Ĵ'=>'J','Ķ'=>'K',
			'�?'=>'K','Ľ'=>'K','Ĺ'=>'K','Ļ'=>'K','Ŀ'=>'K',
			'Ñ'=>'N','Ń'=>'N','Ň'=>'N','Ņ'=>'N','Ŋ'=>'N',
			'Ò'=>'O','Ó'=>'O','Ô'=>'O','Õ'=>'O','Ö'=>'Oe',
			'&Ouml;'=>'Oe', 'Ø'=>'O','Ō'=>'O','�?'=>'O','Ŏ'=>'O',
			'Œ'=>'OE', 'Ŕ'=>'R','Ř'=>'R','Ŗ'=>'R',
			'Ś'=>'S','Š'=>'S','Ş'=>'S','Ŝ'=>'S','Ș'=>'S',
			'Ť'=>'T','Ţ'=>'T','Ŧ'=>'T','Ț'=>'T',
			'Ù'=>'U','Ú'=>'U','Û'=>'U','Ü'=>'Ue','Ū'=>'U',
			'&Uuml;'=>'Ue', 'Ů'=>'U','Ű'=>'U','Ŭ'=>'U','Ũ'=>'U','Ų'=>'U',
			'Ŵ'=>'W', '�?'=>'Y','Ŷ'=>'Y','Ÿ'=>'Y', 'Ź'=>'Z','Ž'=>'Z','Ż'=>'Z',
			'Þ'=>'T','Þ'=>'T', 'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'ae',
			'&auml;'=>'ae', 'å'=>'a','�?'=>'a','ą'=>'a','ă'=>'a',
			'æ'=>'ae', 'ç'=>'c','ć'=>'c','�?'=>'c','ĉ'=>'c','ċ'=>'c',
			'�?'=>'d','đ'=>'d','ð'=>'d', 'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e','ē'=>'e',
			'ę'=>'e','ě'=>'e','ĕ'=>'e','ė'=>'e', 'ƒ'=>'f',
			'�?'=>'g','ğ'=>'g','ġ'=>'g','ģ'=>'g', 'ĥ'=>'h','ħ'=>'h',
			'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i','ī'=>'i', 'ĩ'=>'i','ĭ'=>'i','į'=>'i','ı'=>'i',
			'ĳ'=>'ij', 'ĵ'=>'j', 'ķ'=>'k','ĸ'=>'k', 'ł'=>'l','ľ'=>'l','ĺ'=>'l','ļ'=>'l','ŀ'=>'l',
			'ñ'=>'n','ń'=>'n','ň'=>'n','ņ'=>'n','ŉ'=>'n', 'ŋ'=>'n',
			'ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'oe', '&ouml;'=>'oe',
			'ø'=>'o','�?'=>'o','ő'=>'o','�?'=>'o', 'œ'=>'oe', 'ŕ'=>'r','ř'=>'r','ŗ'=>'r',
			'š'=>'s', 'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'ue','ū'=>'u', '&uuml;'=>'ue',
			'ů'=>'u','ű'=>'u','ŭ'=>'u','ũ'=>'u','ų'=>'u', 'ŵ'=>'w',
			'ý'=>'y','ÿ'=>'y','ŷ'=>'y', 'ž'=>'z','ż'=>'z','ź'=>'z', 'þ'=>'t', 'ß'=>'ss', 'ſ'=>'ss',
			'ä'=>'ae', 'ö'=>'oe', 'ü'=>'ue', 'Ä'=>'Ae', 'Ö'=>'Oe', 'Ü'=>'Ue'
			);
		$string = strtr($string, $conversion_array);
		return $string;
	}


	/**
	 * delete forbidden chars
	 *
	 * @param str $string, string to sanitize
	 *
	 * @return str $string, snaitized string
	 */
	function convertBadChars($string)
	{
		$bad = array(
			'\'', '"', '`',
			'<', '>',
			'{', '}', '[', ']', '(', ')',
			'!', '?', ',', ':', ';',
			'@', '#', '$', '%', '^', '&', '*',
			'=', '+', '|', '/', '\\'
		);
		$string = str_replace($bad, '', $string);
		return $string;
	}


	/**
	 * displays pagination for lists
	 *
	 * @param string $page, url to go
	 * @param int $nb_pages, total number of pages (nb results / nb results to display)
	 * @param int $current_page
	 * @param string $more_params, potential params to add to url
	 *
	 * @return string $buffer, html to display
	 */
	function pagination($page, $nb_pages, $current_page, $more_params)
	{
		$buffer = '';
		if($nb_pages > 1) {
			// first page to display
			$min_page = 1;
			// last page to display
			$max_page = $nb_pages;
			// more params ?
			$get_sup = '';
			if( ! empty($more_params)) {
				$get_sup = $more_params;
			}
			$url = $page . '/p%d' . $get_sup;
			// buffer
			$buffer .= '<div class="clearfix text-center"><ul class="pagination pagination-sm">';
			for($i = $min_page; $i <= $max_page; $i++) {
				if($i == $current_page) {
					$buffer .= '<li class="active"><a href="#">' . $i . '<span class="sr-only">(current)</span></a></li> ';
				} else {
					$buffer .= '<li><a href="' . site_url(sprintf($url, $i)) . '">' . $i . '</a></li>';
				}
			}
			$buffer .= '</ul></div>';

		}
		return $buffer;
	}



	/**
	 * create a password
	 *
	 * @param int $length
	 *
	 * @return string $password
	*/
	function createPassword($length = 8)
	{
		$password = '';
		// some chars are not used to prevent wrong interpretation (ie 0 and O, l and 1)
		$permitted = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M',
					'N', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
					'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n',
					'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
					'1', '2', '3', '4', '5', '6', '7', '8', '9',
					'1', '2', '3', '4', '5', '6', '7', '8', '9'
			);
		shuffle($permitted);
		$rand_keys = array_rand ($permitted, $length);
		foreach($rand_keys as $value) {
			$password .= $permitted[$value];
		}
		$password = self::cryptStr($password);
		return $password;
	}


	/**
	 * encrypt a string
	 *
	 * @param str $str
	 *
	 * @return str $encrypted_str
	 */
	function cryptStr($str)
	{
		$this->load->library('encrypt');
		$encrypted_str = $this->encrypt->encode($str);
		return $encrypted_str;
	}



	/**
	 * un-crypt a string
	 *
	 * @param str $str
	 *
	 * @return str $decrypted_str
	 */
	function decryptStr($str)
	{
		$this->load->library('encrypt');
		$decrypted_str = $this->encrypt->decode($str);
		return $decrypted_str;
	}


	/**
	 * format referees display on emails
	 *
	 * @param object $match
	 * 
	 * @return string $referees
	 */
	function formatReferees($match)
	{
		$referees = '';
		if( ! empty($match->referee1_club_name)) {
			$referees .= $match->referee1_club_name;
			if( ! empty($match->referee1_name)) {
				$referees .= ' : ' . $match->referee1_name;
			}
			if( ! empty($match->match_referee1_status)) {
				$referees .= ' (' . $match->match_referee1_status . ')';
			}
		}
		if( ! empty($match->referee2_club_name)) {
			if( !empty($referees)) {
				$referees = '<br>Arbitre 1 : ' . $referees . '<br>Arbitre 2 : ';
			}
			$referees .= $match->referee2_club_name;
			if( ! empty($match->referee2_name)) {
				$referees .= ' : ' . $match->referee2_name;
			}
			if( ! empty($match->match_referee2_status)) {
				$referees .= ' (' . $match->match_referee2_status . ')';
			}
		}
		return $referees;
	}

}

/* End of file misc_model.php */
/* Location: ./application/models/misc_model.php */