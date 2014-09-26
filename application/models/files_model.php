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
 * manipulation of physicals files and directories
 *
 * @version $Id: files_model.php 81 2014-08-22 10:11:22Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 *
 */

class Files_model extends CI_Model
{

	/* field separator for CSV */
	const CSV_SEP = ";";
	/* CSV end of line */
	const CSV_END_OF_LINE = "\n";
	/* encoding detected */
	public static $supported_encoding = array(
		 "utf-8", "iso-8859-1", "iso-8859-15", "windows-1251", "windows-1252", "ASCII",
	);


	/**
	 * constructeur
	 */
	function __construct ()
	{
		parent::__construct();
	}


	//--------------------------------------------------------------------------
	//
	// GENERIC FUNCTIONS
	//
	//--------------------------------------------------------------------------


	/**
	 * return a file's content
	 *
	 * @param string $file, the path to file
	 * @param string $mode
	 *
	 * @return mixed bool|string
	 */
	function readFile($file, $mode = 'rb')
	{
		$handle = fopen($file, $mode);
		if( ! $handle) {
			return false;
		} else {
			$buffer = fread($handle, filesize($file));
			return $buffer;
		}
	}


	/**
	 * changes file permission
	 *
	 * @param string $file, path to file
	 * @param string $permissions, file permission in octal mode
	 *
	 * @return bool
	 */
	function setFilePermissions($file, $permissions)
	{
		if(file_exists($file)) {
			return chmod($file, $permissions);
		} else {
			return false;
		}
	}


	/**
	 * write content in a file
	 *
	 * @param string $path
	 * @param string $filename
	 * @param string $content
	 * @param string $mode
	 *
	 * @return bool
	 */
	function writeFile($path, $filename, $content = '', $mode = 'wb')
	{
		if(substr($path, -1) != '/') {
			$path .= '/';
		}
		$handle = fopen($path . $filename, $mode);
		$res = fwrite($handle, $content);
		fclose($handle);
		if($res === false) {
			return false;
		}
		return true;
	}


	/**
	 * move a file from source to destination
	 *
	 * @param string $src, the source
	 * @param string $dest, the destination
	 *
	 * @return bool $res
	 */
	function moveFile($src = '', $dest = '')
	{
		$res = false;
		if( ! empty($src) && ! empty($dest)) {
			$res = rename($src, $dest);
		}
		return $res;
	}


	/**
	 * delete a file
	 *
	 * @param string $complete_path, path to file
	 *
	 * @return bool
	 */
	function delFile($complete_path)
	{
		if(file_exists($complete_path)) {
			return unlink($complete_path);
		}
	}


	/**
	 * rename a file adding a unique ID
	 *
	 * @param string $orig_filename, the original file name
	 *
	 * @return string $new_name, the new file name
	 */
	function renameFile($orig_filename)
	{
		$array = pathinfo($orig_filename);
		// get ext
		$ext = $array['extension'];
		// get basename
		$filename = $array['filename'];
		// clean and shorten file name
		$clean_filename = Misc_model::protectFilename($filename, 150);
		// recreate name
		$new_name = $clean_filename . '_' . date('Y-m-d_His') . '.' . $ext;
		return $new_name;
	}


	/**
	 * upload a $_FILES field
	 *
	 * @param string $fieldname, name of $_FILES field
	 * @param array $config, the upload config
	 *
	 * @return array(success (bool), message (string))
	 */
	function uploadFile($fieldname, $config)
	{
		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if ( ! $this->upload->do_upload($fieldname)) {
			return array('success' => false, 'new_name' => '', 'message' => $this->upload->display_errors());
		} else {
			$result = $this->upload->data();
			self::setFilePermissions($result['full_path'], 0750);
			return array('success' => true, 'new_name' => $config['file_name']);
		}
	}


	/**
	 * sanitize a directory/file name
	 *
	 * @param string $filename, name of directory or file
	 * @param int    $length, max length of new name ; default 180 (upload crashes at 195)
	 *
	 * @return string $filename, new name
	 */
	function protectFilename($filename, $length = 180)
	{
		// convert special chars
		$filename = Misc_model::convertStrangeChars($filename);
		// delete spaces
		$filename = str_replace(' ', '_', $filename);
		// delete bad chars
		$filename = Misc_model::convertBadChars($filename);
		$filename = strtolower($filename);
		$filename = substr($filename, 0, $length);
		return $filename;
	}


	//--------------------------------------------------------------------------
	//
	// FONCTIONS METIER
	//
	//--------------------------------------------------------------------------


	/**
	 * logs
	 *
	 * @param string $content
	 *
	 * @return boolean
	 */
	function writeLog($content)
	{
		if(is_array($content)) {
			$tmp = $content;
			$content = '';
			foreach($tmp as $key => $value) {
				if(is_array($value)) {
					$value = implode(', ', $value);
				}
				$content .= $key . ' => ' . $value . "\n";
			}
		}
		$content = "\n-----------------------------------------------\n"
				. date('Y-m-d H:i:s') . ' ' . html_entity_decode($content, ENT_QUOTES);
		$res = self::writeFile(CRH_LOCAL_PATH . CRH_PATH_TO_LOGS, 'log_' . date("Ymd"), $content, 'a+b');
		if ( ! $res) {
			return false;
		}
		return true;
	}


	/**
	 * create the csv file with users
	 *
	 * @param query result $users
	 *
	 * @return mixed boolean|string
	 */
	function createCsvUsers($users)
	{
		$buffer = '';
		$filename = 'export_utilisateurs_' . time() . '.csv';
		$path = CRH_PATH_TO_FILES;
		// header
		$buffer .= '"ID"'       . self::CSV_SEP . '"Club ID"' . self::CSV_SEP
				. '"Club"'      . self::CSV_SEP . '"Nom"' . self::CSV_SEP
				. '"Adresse"'   . self::CSV_SEP . '"Email"' . self::CSV_SEP
				. '"Portable"'  . self::CSV_SEP . '"Fixe"' . self::CSV_SEP
				. '"Correspondant club"' . self::CSV_SEP
				. '"Correspondant Arbitres"' . self::CSV_SEP . '"Resp. U9"' . self::CSV_SEP
				. '"Resp. U11"' . self::CSV_SEP . '"Resp. U13"' . self::CSV_SEP
				. '"Resp. U15"' . self::CSV_SEP . '"Resp. U17"' . self::CSV_SEP
				. '"Resp. U20"' . self::CSV_SEP . '"Resp. R4"' . self::CSV_SEP
				. '"Resp. N3"'  . self::CSV_SEP . '"Resp. N2"' . self::CSV_SEP
				. '"Resp. N1"'  . self::CSV_SEP . '"Resp. N1F"' . self::CSV_SEP
				. '"Arbitre"'   . self::CSV_SEP . '"Degre arbitrage"' . self::CSV_SEP
				. '"licence"'   . self::CSV_SEP . '"Date naissance"' . self::CSV_END_OF_LINE;
		foreach ($users as $user) {
			// dirty and non-optimized way, please do better next time
			$info_user = Users_model::getUser($user->user_id);
			$corresp_club = '"NON"';
			$corresp_arbitre = '"NON"';
			$resp_u9 = '"NON"';
			$resp_u11 = '"NON"';
			$resp_u13 = '"NON"';
			$resp_u15 = '"NON"';
			$resp_u17 = '"NON"';
			$resp_u20 = '"NON"';
			$resp_R4 = '"NON"';
			$resp_N3 = '"NON"';
			$resp_N2 = '"NON"';
			$resp_N1 = '"NON"';
			$resp_N1F = '"NON"';
			if(isset($info_user->profiles) && count($info_user->profiles) > 0) {
				// simple member
				if(isset($info_user->profiles[8])) {
					// ok, all if already set
				} else {
					// corresp club
					if(isset($info_user->profiles[4])) {
						$corresp_club = '"OUI"';
					}
					// corresp arbitre
					if(isset($info_user->profiles[5])) {
						$corresp_arbitre = '"OUI"';
					}
					// team manager
					if(isset($info_user->profiles[6]) && isset($info_user->teams) && count($info_user->teams) > 0) {

						if(array_filter($info_user->teams, create_function('$a','return preg_match("#U9#", $a);') ) ) {
							$resp_u9 = '"OUI"';
						}
						if(array_filter($info_user->teams, create_function('$a','return preg_match("#U11#", $a);') ) ) {
							$resp_u11 = '"OUI"';
						}
						if(array_filter($info_user->teams, create_function('$a','return preg_match("#U13#", $a);') ) ) {
							$resp_u13 = '"OUI"';
						}
						if(array_filter($info_user->teams, create_function('$a','return preg_match("#U15#", $a);') ) ) {
							$resp_u15 = '"OUI"';
						}
						if(array_filter($info_user->teams, create_function('$a','return preg_match("#U17#", $a);') ) ) {
							$resp_u17 = '"OUI"';
						}
						if(array_filter($info_user->teams, create_function('$a','return preg_match("#U20#", $a);') ) ) {
							$resp_u20 = '"OUI"';
						}
						if(array_filter($info_user->teams, create_function('$a','return preg_match("#R4#", $a);') ) ) {
							$resp_R4 = '"OUI"';
						}
						if(array_filter($info_user->teams, create_function('$a','return preg_match("#N3#", $a);') ) ) {
							$resp_N3 = '"OUI"';
						}
						if(array_filter($info_user->teams, create_function('$a','return preg_match("#N2#", $a);') ) ) {
							$resp_N2 = '"OUI"';
						}
						if(array_filter($info_user->teams, create_function('$a','return preg_match("#N1-#", $a);') ) ) {
							$resp_N1 = '"OUI"';
						}
						if(array_filter($info_user->teams, create_function('$a','return preg_match("#N1F#", $a);') ) ) {
							$resp_N1F = '"OUI"';
						}
					}
				}
			}

			$buffer .= '"' . $user->user_id . '"'      . self::CSV_SEP . '"' . $user->user_club . '"'   . self::CSV_SEP
					. '"'  . protegeExport($user->club_name) . '"'    . self::CSV_SEP . '"'  . protegeExport($user->user_name) . '"'    . self::CSV_SEP
					. '"'  . protegeExport($user->user_address) . '"' . self::CSV_SEP . '"' . protegeExport($user->user_email) . '"'  . self::CSV_SEP
					. '"'  . protegeExport($user->user_mobile) . '"'   . self::CSV_SEP . '"' . protegeExport($user->user_phone) . '"' . self::CSV_SEP
					. $corresp_club                    . self::CSV_SEP
					. $corresp_arbitre                 . self::CSV_SEP . $resp_u9                       . self::CSV_SEP
					. $resp_u11                        . self::CSV_SEP . $resp_u13                      . self::CSV_SEP
					. $resp_u15                        . self::CSV_SEP . $resp_u17                      . self::CSV_SEP
					. $resp_u20                        . self::CSV_SEP . $resp_R4                       . self::CSV_SEP
					. $resp_N3                         . self::CSV_SEP . $resp_N2                       . self::CSV_SEP
					. $resp_N1                         . self::CSV_SEP . $resp_N1F                      . self::CSV_SEP
					. '"' . (($user->user_isreferee == 1)? 'OUI':'NON') . '"'. self::CSV_SEP
					. '"' . (($user->user_isreferee == 1)? $user->user_referee_degree:'') . '"'   . self::CSV_SEP
					. '"' . $user->user_licence . '"'  . self::CSV_SEP . '"' . $user->user_birthdate_format . '"' . self::CSV_END_OF_LINE;
		}

		$result = self::writeFile($path, $filename, $buffer);
		if( ! $result) {
			return false;
		} else {
			return ($path . $filename);
		}
	}


	/**
	 * create the csv file with matches
	 *
	 * @param query result $matches
	 * @param bool $is_public, indicate if the export is public or private
	 *
	 * @return mixed boolean|string
	 */
	function createCsvMatches($matches, $is_public = false)
	{
		$buffer = '';
		$filename = 'export_matches_' . time() . '.csv';
		$path = CRH_PATH_TO_FILES;
		// header
		if( ! $is_public) {
			$buffer .= '"ID"' . self::CSV_SEP;
		}
		$buffer .= '"Date"'          . self::CSV_SEP . '"Heure"'           . self::CSV_SEP
				. '"Compétition"'    . self::CSV_SEP . '"Catégorie"'       . self::CSV_SEP
				. '"Equipe 1"'       . self::CSV_SEP . '"Statut équipe 1"' . self::CSV_SEP
				. '"Score"'          . self::CSV_SEP
				. '"Equipe 2"'       . self::CSV_SEP . '"Statut équipe 2"' . self::CSV_SEP
				. '"Lieu"'           . self::CSV_SEP
				. '"Club arbitre 1"' . self::CSV_SEP;
		if( ! $is_public) {
			$buffer .= '"Arbitre 1"' . self::CSV_SEP;
		}
		$buffer .= '"Arbitre 1 statut"' . self::CSV_SEP
				. '"Club arbitre 2"' . self::CSV_SEP;

		if( ! $is_public) {
			$buffer .= '"Arbitre 2"' . self::CSV_SEP;
		}
		$buffer .= '"Arbitre 2 statut"';
		if( ! $is_public) {
			$buffer .=  self::CSV_SEP . '"Feuille de match"' . self::CSV_SEP . '"Validé"' . self::CSV_SEP . '"Commentaires"';
		}
		$buffer .= self::CSV_END_OF_LINE;

		foreach ($matches as $match) {

			if( ! $is_public) {
				$buffer .= '"' . $match->match_id . '"' . self::CSV_SEP;
			}
			$buffer .= '"' . $match->match_date_format . '"'  . self::CSV_SEP . '"' . $match->match_time_format . '"' . self::CSV_SEP
					. '"'  . protegeExport($match->cs_name) . '"'            . self::CSV_SEP . '"' . protegeExport($match->category_name) . '"'     . self::CSV_SEP
					. '"'  . protegeExport($match->match_team1) . ' - ' . $match->team1_name . '"' . self::CSV_SEP
					. '"'  . $match->match_team1_status . '"'  . self::CSV_SEP
					. '"'  . $match->match_team1_score . ' - '  . $match->match_team2_score . '"' . self::CSV_SEP
					. '"'  . protegeExport($match->match_team2) . ' - ' . $match->team2_name . '"' . self::CSV_SEP
					. '"'  . $match->match_team2_status . '"'  . self::CSV_SEP
					. '"'  . protegeExport($match->place_name) . '"'         . self::CSV_SEP
					. '"'  . protegeExport($match->referee1_club_name) . '"' . self::CSV_SEP;
			if( ! $is_public) {
				$buffer .= '"' . protegeExport($match->referee1_name) . '"' . self::CSV_SEP;
			}
			$buffer .= '"' . $match->match_referee1_status . '"'. self::CSV_SEP
					. '"'  . protegeExport($match->referee2_club_name) . '"' . self::CSV_SEP;
			if( ! $is_public) {
				$buffer .= '"' . protegeExport($match->referee2_name) . '"' . self::CSV_SEP;
			}
			$buffer .= '"' . $match->match_referee2_status . '"';
			if( ! $is_public) {
				$buffer .= self::CSV_SEP . '"' . $match->match_scoresheet . '"' . self::CSV_SEP . '"' . $match->match_complete . '"' . self::CSV_SEP
						. '"' . protegeExport($match->match_comments) . '"';
			}
			$buffer .= self::CSV_END_OF_LINE;
		}
		$result = self::writeFile($path, $filename, $buffer);
		if( ! $result) {
			return false;
		} else {
			return ($path . $filename);
		}
	}


	/**
	 * create the csv file with refereeing information
	 *
	 * @param query result $matches
	 *
	 * @return mixed boolean|string
	 */
	function createCsvRefereeing($matches)
	{
		$buffer = '';
		$filename = 'export_arbitrage_' . time() . '.csv';
		$path = CRH_PATH_TO_FILES;
		// header
		$buffer .= '"Date"'          . self::CSV_SEP . '"Heure"'           . self::CSV_SEP
				. '"Catégorie"'      . self::CSV_SEP
				. '"Equipe 1"'       . self::CSV_SEP . '"Statut équipe 1"' . self::CSV_SEP
				. '"Equipe 2"'       . self::CSV_SEP . '"Statut équipe 2"' . self::CSV_SEP
				. '"Lieu"'           . self::CSV_SEP
				. '"Club arbitre 1"' . self::CSV_SEP
				. '"Arbitre 1"'      . self::CSV_SEP . '"Arbitre 1 statut"' . self::CSV_SEP
				. '"Club arbitre 2"' . self::CSV_SEP
				. '"Arbitre 2"'      . self::CSV_SEP . '"Arbitre 2 statut"'
				. self::CSV_END_OF_LINE;

		foreach ($matches as $match) {

			$buffer .= '"' . $match->match_date_format . '"'  . self::CSV_SEP . '"' . $match->match_time_format . '"' . self::CSV_SEP
					. '"'  . protegeExport($match->category_name) . '"'       . self::CSV_SEP
					. '"'  . protegeExport($match->team1_name) . '"'         . self::CSV_SEP . '"' . $match->match_team1_status . '"'  . self::CSV_SEP
					. '"'  . protegeExport($match->team2_name) . '"'         . self::CSV_SEP . '"' . $match->match_team2_status . '"'  . self::CSV_SEP
					. '"'  . protegeExport($match->place_name) . '"'         . self::CSV_SEP
					. '"'  . protegeExport($match->referee1_club_name) . '"' . self::CSV_SEP
					. '"'  . protegeExport($match->referee1_name) . '"'       . self::CSV_SEP . '"' . $match->match_referee1_status . '"'. self::CSV_SEP
					. '"'  . protegeExport($match->referee2_club_name) . '"' . self::CSV_SEP
					. '"'  . protegeExport($match->referee2_name) . '"'       . self::CSV_SEP . '"' . $match->match_referee2_status . '"'
					. self::CSV_END_OF_LINE;
		}
		$result = self::writeFile($path, $filename, $buffer);
		if( ! $result) {
			return false;
		} else {
			return ($path . $filename);
		}
	}


	/**
	 * create the csv file with teams
	 *
	 * @param query result $teams
	 *
	 * @return mixed boolean|string
	 */
	function createCsvTeams($teams)
	{
		$buffer = '';
		$filename = 'export_teams_' . time() . '.csv';
		$path = CRH_PATH_TO_FILES;
		// header
		$buffer .= '"ID"'   . self::CSV_SEP . '"Equipe"'    . self::CSV_SEP
				. '"Club"'  . self::CSV_SEP . '"Catégorie"' . self::CSV_SEP
				. '"Actif"' . self::CSV_SEP . '"Managers"'
				. self::CSV_END_OF_LINE;

		foreach ($teams as $team) {
			// get team managers
			$managers = Users_model::getTeamManagers($team->team_id);
			$tmp = array();
			foreach($managers as $manager) {
				$tmp[] = $manager->user_id;
			}
			$team_managers = implode(',', $tmp);

			$buffer .= '"' . $team->team_id      . '"' . self::CSV_SEP . '"' . protegeExport($team->team_name) . '"' . self::CSV_SEP
					. '"'  . $team->team_club_id . '"' . self::CSV_SEP . '"' . $team->team_category_id . '"' . self::CSV_SEP
					. '"'  . $team->team_active  . '"' . self::CSV_SEP . '"' . $team_managers          . '"'
					. self::CSV_END_OF_LINE;
		}
		$result = self::writeFile($path, $filename, $buffer);
		if( ! $result) {
			return false;
		} else {
			return ($path . $filename);
		}
	}


}

/* End of file files_model.php */
/* Location: ./application/models/files_model.php */