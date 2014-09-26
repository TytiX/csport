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
 * Import and export CSV files
 *
 * @version $Id: import_export.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import_export extends MY_Controller
{


	/**
	 * constructeur
	 *
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('files_model');
	}


	/**
	 * default controller
	 */
	function index()
	{
		redirect('', 'location');
	}


	/**
	 * import the matches csv file
	 */
	public function import_matches()
	{
		Permissions_model::userHasAccess('import_match');

		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$this->load->model('matches_model');

		$data = array(
			'title' => 'Importer des matches',
			'action' => 'importer-matches'
		);

		$message = $this->session->flashdata('message');
		$type_message = $this->session->flashdata('type_message');
		$data['message'] = _set_result_message($message, $type_message);

		$instructions = "Vous devez importer un fichier au format CSV (pour avoir
			un exemple, exportez la liste des matches). Respectez l'ordre des champs,
			n'enlevez aucune colonne. Laissez la première ligne d'entête.<br>
			Dans la première colonne, s'il s'agit d'un ajout, indiquez 0 (zéro) ou laissez vide.
			S'il s'agit d'une modification, indiquez l'ID du match à modifier (cet
			ID est disponible dans l'export des matches).";
		$data['instructions'] = _set_result_message($instructions, CRH_TYPE_MSG_INFO);

		$this->form_validation->set_rules('submit_form', 'submit', '');
		$form_validation = $this->form_validation->run();

		if ($form_validation !== false) {
			$new_name = 'matches_' . time() . '.csv';
			$result = Matches_model::importMatches('uploaded_file', $new_name);
			if($result['success'] === true) {
				$data['message'] = _set_result_message($result['message'], CRH_TYPE_MSG_PERM_SUCCESS);
			} else {
				$data['message'] = _set_result_message($result['message'], CRH_TYPE_MSG_ERROR);
			}
		}

		//-----------------------------
		// template
		//-----------------------------
		$this->template->write('title', $data['title']);
		$menu = $this->_getMenu('import');
		$this->template->write('menu', $menu);
		$this->template->write_view('main_content', 'templates/generic_import', $data);
		$this->template->render();

	}


	/**
	 * import the users csv file
	 */
	public function import_users()
	{
		Permissions_model::userHasAccess('import_user');

		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$this->load->model('users_model');

		$data = array(
			'title' => 'Importer des utilisateurs',
			'action' => 'importer-utilisateurs'
		);

		$message = $this->session->flashdata('message');
		$type_message = $this->session->flashdata('type_message');
		$data['message'] = _set_result_message($message, $type_message);

		$instructions = "Vous devez importer un fichier au format CSV (pour avoir
			un exemple, exportez la liste des utilisateurs). Respectez l'ordre des champs,
			n'enlevez aucune colonne. Laissez la première ligne d'entête.<br>
			Dans la première colonne, s'il s'agit d'un ajout, indiquez 0 (zéro) ou laissez vide.
			S'il s'agit d'une modification, indiquez l'ID de l'utilisateur à modifier (cet
			ID est disponible dans l'export des utilisateurs).";
		$data['instructions'] = _set_result_message($instructions, CRH_TYPE_MSG_INFO);

		$this->form_validation->set_rules('submit_form', 'submit', '');
		$form_validation = $this->form_validation->run();

		if ($form_validation !== false) {
			$new_name = 'users_' . time() . '.csv';
			$result = Users_model::importUsers('uploaded_file', $new_name);
			if($result['success'] === true) {
				$data['message'] = _set_result_message($result['message'], CRH_TYPE_MSG_PERM_SUCCESS);
			} else {
				$data['message'] = _set_result_message($result['message'], CRH_TYPE_MSG_ERROR);
			}
		}

		//-----------------------------
		// template
		//-----------------------------
		$this->template->write('title', $data['title']);
		$menu = $this->_getMenu('import');
		$this->template->write('menu', $menu);
		$this->template->write_view('main_content', 'templates/generic_import', $data);
		$this->template->render();

	}


	/**
	 * import the teams csv file
	 */
	public function import_teams()
	{
		Permissions_model::userHasAccess('import_team');

		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$this->load->model('teams_model');

		$data = array(
			'title' => 'Importer les équipes',
			'action' => 'importer-equipes'
		);

		$message = $this->session->flashdata('message');
		$type_message = $this->session->flashdata('type_message');
		$data['message'] = _set_result_message($message, $type_message);

		$instructions = "Vous devez importer un fichier au format CSV ("
			. anchor(site_url('exporter-equipes'), 'cliquez ici pour avoir un modèle', 'class="alert-link"')
			. "). Respectez l'ordre des champs,
			n'enlevez aucune colonne. Laissez la première ligne d'entête.<br>
			Dans la première colonne, s'il s'agit d'un ajout, indiquez 0 (zéro) ou laissez vide.
			S'il s'agit d'une modification, indiquez l'ID de l'équipe à modifier.";
		$data['instructions'] = _set_result_message($instructions, CRH_TYPE_MSG_INFO);

		$this->form_validation->set_rules('submit_form', 'submit', '');
		$form_validation = $this->form_validation->run();

		if ($form_validation !== false) {
			$new_name = 'teams_' . time() . '.csv';
			$result = Teams_model::importTeams('uploaded_file', $new_name);
			if($result['success'] === true) {
				$data['message'] = _set_result_message($result['message'], CRH_TYPE_MSG_PERM_SUCCESS);
			} else {
				$data['message'] = _set_result_message($result['message'], CRH_TYPE_MSG_ERROR);
			}
		}

		//-----------------------------
		// template
		//-----------------------------
		$this->template->write('title', $data['title']);
		$menu = $this->_getMenu('import');
		$this->template->write('menu', $menu);
		$this->template->write_view('main_content', 'templates/generic_import', $data);
		$this->template->render();

	}


	/**
	 * export a list of users
	 *
	 */
	public function export_users()
	{
		Permissions_model::userHasAccess('user_export');

		$this->load->model('users_model');
		$this->load->model('files_model');

		// get search filters
		$f_name      = $this->session->userdata('f_name');
		$f_club      = $this->session->userdata('f_club');
		$f_isreferee = $this->session->userdata('f_isreferee');
		$criteria = array(
			'uname'    => $f_name,
			'club'     => $f_club,
			'ureferee' => $f_isreferee,
		);

		$users = Users_model::getAllUsers($criteria, 0, 0);
		$result = Files_model::createCsvUsers($users['result']);
		if($result === false) {
			$data = array('message' => _set_result_message("L'export a échoué.", CRH_TYPE_MSG_ERROR));
			$this->template->write('title', 'Erreur');
			$menu = $this->_getMenu('user');
			$this->template->write('menu', $menu);
			$this->template->write_view('main_content', 'templates/partial_error', $data);
			$this->template->render();
			exit;
		} else {
			header('Content-Description: File Transfer');
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename=' . basename($result));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($result));
			readfile($result);
			unlink($result);
		}
	}


	/**
	 * export list of teams
	 */
	public function export_teams()
	{
		Permissions_model::userHasAccess('team_export');

		$this->load->model('teams_model');
		$this->load->model('files_model');

		$teams = Teams_model::getAll(array(), 0, 0);
		$result = Files_model::createCsvTeams($teams['result']);
		if($result === false) {
			$data = array('message' => _set_result_message("L'export a échoué.", CRH_TYPE_MSG_ERROR));
			$this->template->write('title', 'Erreur');
			$menu = $this->_getMenu('import');
			$this->template->write('menu', $menu);
			$this->template->write_view('main_content', 'templates/partial_error', $data);
			$this->template->render();
			exit;
		} else {
			header('Content-Description: File Transfer');
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename=' . basename($result));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($result));
			readfile($result);
			unlink($result);
		}
	}


	/**
	 * export a list of matches
	 *
	 * @param array $criteria, the search filters
	 * @param int $is_public, indicates if the export is public view or not
	 */
	function _export_matches($criteria, $is_public = 0)
	{
		if($is_public != 1) {
			Permissions_model::userHasAccess('match_export');
		}
		$this->load->model('matches_model');
		$this->load->model('files_model');

		$matches = Matches_model::getAllMatches($criteria, 0, 0);
		$result = Files_model::createCsvMatches($matches['result'], $is_public);
		if($result === false) {
			$data = array('message' => _set_result_message("L'export a échoué.", CRH_TYPE_MSG_ERROR));
			$this->template->write('title', 'Erreur');
			$menu = $this->_getMenu('match');
			$this->template->write('menu', $menu);
			$this->template->write_view('main_content', 'templates/partial_error', $data);
			$this->template->render();
			exit;
		} else {
			header('Content-Description: File Transfer');
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename=' . basename($result));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($result));
			readfile($result);
			unlink($result);
		}
	}


	/*
	 * export a list of matches
	 *
	 * @param int $is_public, indicates if the export is public view or not
	 */
	public function export_matches_public($is_public = 0)
	{
		if($is_public != 1) {
			Permissions_model::userHasAccess('match_export');
		} 
		$this->load->model('matches_model');
		$this->load->model('files_model');

		// get search filters
		$f_club      = $this->session->userdata('f_club');
		$f_cat       = $this->session->userdata('f_cat');
		$f_cs        = $this->session->userdata('f_cs');
		$f_team      = $this->session->userdata('f_team');
		$f_referee   = $this->session->userdata('f_referee');
		$f_where     = $this->session->userdata('f_where');
		$f_date_from = $this->session->userdata('f_date_from');
		$f_date_to   = $this->session->userdata('f_date_to');
		$criteria = array(
			'club'         => $f_club,
			'cat'          => $f_cat,
			'cs'           => $f_cs,
			'team'         => $f_team,
			'club_referee' => $f_referee,
			'place'        => $f_where,
			'date_from'    => $f_date_from,
			'date_to'      => $f_date_to,
		);
		return $this->_export_matches($criteria, $is_public);
	}

	/*
	 * export a list of matches
	 *
	 */
	public function export_matches_private()
	{
		Permissions_model::userHasAccess('match_export');
		$this->load->model('matches_model');
		$this->load->model('files_model');

		// get search filters
		$f_club      = $this->session->userdata('fp_club');
		$f_cat       = $this->session->userdata('fp_cat');
		$f_cs        = $this->session->userdata('fp_cs');
		$f_team      = $this->session->userdata('fp_team');
		$f_referee   = $this->session->userdata('fp_referee');
		$f_where     = $this->session->userdata('fp_where');
		$f_date_from = $this->session->userdata('fp_date_from');
		$f_date_to   = $this->session->userdata('fp_date_to');
		$criteria = array(
			'club'         => $f_club,
			'cat'          => $f_cat,
			'cs'           => $f_cs,
			'team'         => $f_team,
			'club_referee' => $f_referee,
			'place'        => $f_where,
			'date_from'    => $f_date_from,
			'date_to'      => $f_date_to,
		);
		return $this->_export_matches($criteria, false);
	}


	/**
	 * export a list of matches
	 *
	 */
	public function export_matches_planning()
	{
		Permissions_model::userHasAccess('match_export');
		$this->load->model('matches_model');
		$this->load->model('files_model');

		// get search filters
		$f_club      = $this->session->userdata('fp_club');
		$f_cat       = $this->session->userdata('fp_cat');
		$f_cs        = $this->session->userdata('fp_cs');
		$f_referee   = $this->session->userdata('fp_referee');
		$f_date_from = $this->session->userdata('fp_date_from');
		$f_date_to   = $this->session->userdata('fp_date_to');
		$criteria = array(
			'club'         => $f_club,
			'cat'          => $f_cat,
			'cs'           => $f_cs,
			'club_referee' => $f_referee,
			'date_from'    => $f_date_from,
			'date_to'      => $f_date_to,
		);

		return $this->_export_matches($criteria, false);

	}


	/**
	 * export a list of matches
	 *
	 * @param array $criteria, the search filters
	 */
	function _export_refereeing($criteria)
	{
		$this->load->model('matches_model');
		$this->load->model('files_model');

		$matches = Matches_model::getAllMatches($criteria, 0, 0);
		$result = Files_model::createCsvRefereeing($matches['result']);
		if($result === false) {
			$data = array('message' => _set_result_message("L'export a échoué.", CRH_TYPE_MSG_ERROR));
			$this->template->write('title', 'Erreur');
			$menu = $this->_getMenu('match');
			$this->template->write('menu', $menu);
			$this->template->write_view('main_content', 'templates/partial_error', $data);
			$this->template->render();
			exit;
		} else {
			header('Content-Description: File Transfer');
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename=' . basename($result));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($result));
			readfile($result);
		}
	}


	/**
	 * export referreing planning
	 *
	 */
	public function export_refereeing_planning()
	{
		Permissions_model::userHasAccess('referee_export');

		// get search filters
		$f_club         = $this->session->userdata('fa_club');
		$f_cat          = $this->session->userdata('fa_cat');
		$f_referee      = $this->session->userdata('fa_referee');
		$f_club_referee = $this->session->userdata('fa_club_referee');
		$f_date_from    = $this->session->userdata('fa_date_from');
		$f_date_to      = $this->session->userdata('fa_date_to');

		$criteria = array(
			'club'         => $f_club,
			'cat'          => $f_cat,
			'club_referee' => $f_club_referee,
			'referee'      => $f_referee,
			'date_from'    => $f_date_from,
			'date_to'      => $f_date_to,
		);

		return $this->_export_refereeing($criteria, false);
	}


}


/* End of file import_export.php */
/* Location: ./application/controllers/import_export.php */