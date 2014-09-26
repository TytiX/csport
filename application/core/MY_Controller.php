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
 * See LICENCE.TXT file for more information.
 *
 * @copyright  Copyright (c) 2013-2014 Marie Kuntz - Lezard Rouge (http://www.lezard-rouge.fr)
 * @license    GNU-GPL v3 http://www.gnu.org/licenses/gpl.html
 * @version    1.0
 * @author     Marie Kuntz - Lezard Rouge SARL - www.lezard-rouge.fr - info@lezard-rouge.fr
 */

/**
 * CONTROLLER
 * extends the controller core
 * (once again thanks Phil http://philsturgeon.co.uk/blog/2010/02/CodeIgniter-Base-Classes-Keeping-it-DRY)
 *
 * @version $Id: MY_Controller.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */


class MY_Controller extends CI_Controller {


	/**
	 * constructeur
	 *
	 */
	function __construct()
	{
		parent::__construct();
	}


	/**
	 * get the menu to display
	 *
	 * @param string $current_item, the menu element to highlight
	 *
	 * @return string $buffer
	 */
	function _getMenu($current_item = '')
	{
		$this->load->model('profiles_model');
		$token = $this->session->userdata('sess_token');
		$user_id = $this->session->userdata('sess_user_id');
		$is_referee = $this->session->userdata('sess_user_isreferee');
		$buffer = '';
		if( ! empty($token) && ! empty($user_id)) {
			$buffer = '<div class="navbar navbar-default" role="navigation">
        <div class="container">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span>Menu</span>
			    </button>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
				<li' . ($current_item == 'match' ? ' class="active"':'') . '>' . anchor(site_url('matches/liste-simple'), 'Voir les matches') . '</li>';
			if(Permissions_model::userHasAccess('match_list', false)) {
				$buffer .= '<li' . ($current_item == 'planning' ? ' class="active"':'') . '>' . anchor(site_url('planning/gestion-matches'), 'Modifier le planning') . '</li>';
			}
			if(Permissions_model::userHasAccess('match_edit', false) && Profiles_model::isClubPlanningManager()) {
				$buffer .= '<li' . ($current_item == 'club' ? ' class="active"':'') . '>' . anchor(site_url('club/gestion-matches'), 'Mon club') . '</li>';
			}
			if(Permissions_model::userHasAccess('match_edit', false) && Profiles_model::isTeamManager()) {
				$buffer .= '<li' . ($current_item == 'team' ? ' class="active"':'') . '>' . anchor(site_url('equipes/gestion-matches'), 'Mes équipes') . '</li>';
			}
			if(Permissions_model::userHasAccess('referee_edit', false)
					&& (Profiles_model::isGeneralRefereeManager() || Profiles_model::isClubRefereeManager())) {
				$buffer .= '<li class="dropdown' . ($current_item == 'referee' ? ' active':'') . '">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Arbitres <b class="caret"></b></a>
					<ul class="dropdown-menu">';
				if(Profiles_model::isClubRefereeManager()) {
					$buffer .= '<li>' . anchor(site_url('arbitrage-club/liste'), 'Matches de mon club à arbitrer') . '</li>';
				}
				if(Profiles_model::isGeneralRefereeManager()) {
					$buffer .= '<li>' . anchor(site_url('planning-arbitrage/liste'), 'Responsable général') . '</li>';
				}
				$buffer .= '</ul>
				  </li>';
			}
			if($is_referee == 1) {
				$buffer .= '<li' . ($current_item == 'isreferee' ? ' class="active"':'') . '>' . anchor(site_url('arbitre/liste'), 'Mes matches à arbitrer') . '</li>';
			}
			if(Permissions_model::userHasAccess('import_user', false) || Permissions_model::userHasAccess('import_match', false) || Permissions_model::userHasAccess('import_team', false)) {
				$buffer .= '<li class="dropdown' . ($current_item == 'import' ? ' active':'') . '">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Imports <b class="caret"></b></a>
					<ul class="dropdown-menu">';
				if(Permissions_model::userHasAccess('import_user', false)) {
					$buffer .= '<li>' . anchor(site_url('importer-utilisateurs'), 'Importer des utilisateurs') . '</li>';
				}
				if(Permissions_model::userHasAccess('import_match', false)) {
					$buffer .= '<li>' . anchor(site_url('importer-matches'), 'Importer des matches') . '</li>';
					}
				if(Permissions_model::userHasAccess('import_team', false)) {
					$buffer .= '<li>' . anchor(site_url('importer-equipes'), 'Importer des équipes') . '</li>';
				}
				$buffer .= '</ul>
				  </li>';
			}
			if(Permissions_model::userHasAccess('user_list', false)) {
				$buffer .= '<li' . ($current_item == 'user' ? ' class="active"':'') . '>' . anchor(site_url('utilisateurs/liste'), 'Utilisateurs') . '</li>';
			}
			if(Permissions_model::userHasAccess('user_pwd', false)) {
				$buffer .= '<li' . ($current_item == 'pwd' ? ' class="active"':'') . '>' . anchor(site_url('changer-mot-passe'), 'Mot de passe') . '</li>';
			}
			$buffer .= '<li>' . anchor(site_url('deconnexion/' . $token), 'Déconnexion') . '</li>
				</ul>
			</div></div>
	</div><!-- .navbar -->';
		}
		return $buffer;
	}


	/**
	 * display the login menu
	 * 
	 * @return string
	 */
	function _getLoginMenu()
	{
		$token = $this->session->userdata('sess_token');
		$user_id = $this->session->userdata('sess_user_id');
		$buffer = '';
		if(empty($token) || empty($user_id)) {
			$buffer = '<div class="col-sm-1">' . anchor(site_url('connexion'), 'Connexion') . '</div>';
		}
		return $buffer;
	}



}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */