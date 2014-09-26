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
 * CodeIgniter URL Helpers
 * extended
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/url_helper.html
 *
 *
 * @version $Id: MY_url_helper.php 65 2014-03-03 09:01:25Z lezardro $
 * @author  Marie Kuntz / Lezard Rouge
 */

// ------------------------------------------------------------------------

/**
 * Link to document
 *
 * Creates a link based on the local URL
 *
 * @access	public
 * @param	string	the URL
 * @param	string	the link title
 * @param	mixed	any attributes
 * @return	string
 */
if ( ! function_exists('_anchor_doc'))
{
	function _anchor_doc($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;

		if ( ! is_array($uri))
		{
			$site_url = ( ! preg_match('!^\w+://! i', $uri)) ? (base_url() . $uri) : $uri;
		}
		else
		{
			$site_url = base_url() . $uri;
		}

		if ($title == '')
		{
			$title = $site_url;
		}

		if ($attributes != '')
		{
			$attributes = _parse_attributes($attributes);
		}

		return '<a href="' . $site_url . '"' . $attributes . '>' . $title . '</a>';
	}
}

// ------------------------------------------------------------------------

/**
 * recrée une url
 *
 * @access	public
 * @param	int		$nb_segments
 * @param	array	$segments_array
 * @return	string
 */
if ( ! function_exists('_recreate_uri'))
{
	function _recreate_uri($nb_segments, $segments_array) {

		$current_page_url = '';
		for($i = 1; $i <= $nb_segments; $i++) {
			if(isset($segments_array[$i])) {
				if( ! empty($current_page_url)) {
					$current_page_url .= '/';
				}
				$current_page_url .= $segments_array[$i];
			}

		}
		return $current_page_url;

	}
}

// ------------------------------------------------------------------------

/**
 * recreate an url with no suffix (based on site_url)
 *
 * @access	private
 * @param	int
 * @param	array
 * @return	string
 */
if ( ! function_exists('_another_site_url'))
{
	function _another_site_url($segment) {

		$CI =& get_instance();
		if ($segment == '')
		{
			return $CI->config->slash_item('base_url') . $CI->config->item('index_page');
		}

		if ($CI->config->item('enable_query_strings') == FALSE)
		{
			return base_url() . $CI->config->slash_item('index_page') . $segment;
		}
		else
		{
			return base_url . $CI->config->item('index_page') . '?' . $segment;
		}

	}
}

// ------------------------------------------------------------------------

/* End of file MY_url_helper.php */
/* Location: ./application/helpers/MY_url_helper.php */