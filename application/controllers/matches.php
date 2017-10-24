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
 * matches management
 *
 * @version $Id: matches.php 85 2014-08-27 10:18:51Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 */

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Matches extends MY_Controller
{


    /**
     * constructeur
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('matches_model');
    }


    /**
     * default controller
     */
    public function index()
    {
        redirect('', 'location');
    }


    /**
     * matches list (public view)
     *
     * @param string $view, view type (simple|complete)
     * @param string $display, display type (list|search|order)
     * @param int $page_number
     * @param string $orderby, sort column
     * @param string $order, order asc|desc
     */
    public function matchList($view, $display, $page_number = 1, $orderby = '', $order = 'asc')
    {
        $data = array('filters' => array(), 'show_search' => false);
        $more_params = '';

        $this->load->helper('form');
        $this->load->library('form_validation');

        $message = $this->session->flashdata('message');
        $type_message =$this->session->flashdata('type_message');
        $data['message'] = _set_result_message($message, $type_message);

        if ($display == 'search'
            or $display == 'order') {
            $data['show_search'] = true;

            // get search filters
            $this->form_validation->set_rules('f_club', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_cat', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_team', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_referee', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_where', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_date_from', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_date_to', '', 'trim|xss_clean');

            if ($this->form_validation->run() === false) {

                // the user has ordered the results
                // and/or is browsing on multiple pages after a search
                // => must get search filters (stored in session)
                $f_club      = $this->session->userdata('f_club');
                $f_cat       = $this->session->userdata('f_cat');
                $f_team      = $this->session->userdata('f_team');
                $f_referee   = $this->session->userdata('f_referee');
                $f_where     = $this->session->userdata('f_where');
                $f_date_from = $this->session->userdata('f_date_from');
                $f_date_to   = $this->session->userdata('f_date_to');
            } else {

                // user just pushed search button so get fresh filters
                $f_club      = set_value('f_club', '');
                $f_cat       = set_value('f_cat', '');
                $f_team      = set_value('f_team', '');
                $f_referee   = set_value('f_referee', '');
                $f_where     = set_value('f_where', '0');
                $f_date_from = set_value('f_date_from', '');
                $f_date_to   = set_value('f_date_to', '');
                // store in session
                $this->session->set_userdata(array(
                    'f_club'      => $f_club,
                    'f_cat'       => $f_cat,
                    'f_team'      => $f_team,
                    'f_referee'   => $f_referee,
                    'f_where'     => $f_where,
                    'f_date_from' => $f_date_from,
                    'f_date_to'   => $f_date_to
                ));
            }

            $data['filters'] = array(
                'club'         => $f_club,
                'cat'          => $f_cat,
                'team'         => $f_team,
                'club_referee' => $f_referee,
                'place'        => $f_where,
                'date_from'    => $f_date_from,
                'date_to'      => $f_date_to,
            );

            // if visitor has sorted the list
            if ($display == 'order') {
                if ($orderby == 'date') {
                    $order_type = 'order_date';
                } elseif ($orderby == 'cat') {
                    $order_type = 'order_cat';
                } elseif ($orderby == 'equ1') {
                    $order_type = 'order_team1';
                } elseif ($orderby == 'equ2') {
                    $order_type = 'order_team2';
                } elseif ($orderby == 'referee1') {
                    $order_type = 'order_referee1';
                } elseif ($orderby == 'referee2') {
                    $order_type = 'order_referee2';
                } else {
                    $order_type = 'order_date';
                }
                if ($order != 'desc') {
                    $order = 'asc';
                }
                $data['filters'][$order_type] = $order;
                $more_params = '/' . $orderby . '-' . $order;
            }
        }
        // if $display = list
        else {
            // reset search filters
            $this->session->unset_userdata(array(
                'f_date_from' => '',
                'f_date_to'   => '',
                'f_club'      => '',
                'f_cat'       => '',
                'f_team'      => '',
                'f_where'     => '',
                'f_referee'   => '',
            ));
            $this->session->set_userdata(array('f_where' => 0, 'f_date_from' => date('d/m/Y')));
            $data['filters'] = array('place' => 0, 'date_from' => date('d/m/Y'));
        }
        //-- end filters

        $limit = ($page_number - 1) * CRH_NB_RECORD;
        $Matches_model = new Matches_model();
        $matches = $Matches_model->getAllMatches($data['filters'], $limit, CRH_NB_RECORD);
        $data['matches'] = $matches['result'];

        $data['title'] = "Prochains matches";
        if ($view == 'simple') {
            $tpl = 'match_list_public_light';
            $data['form_url'] = site_url('matches/recherche-simple');
            $data['form_raz'] = site_url('matches/liste-simple');
            $current_url = 'matches/liste-simple';
        } else {
            $tpl = 'match_list_public_complete';
            $data['form_url'] = site_url('matches/recherche-complete');
            $data['form_raz'] = site_url('matches/liste-complete');
            $current_url = 'matches/liste-complete';
        }

        // pagination
        $data['current_page_url'] = _recreate_uri(2, $this->uri->segment_array());
        if (empty($data['current_page_url'])) {
            $data['current_page_url'] = $current_url;
        }
        $nb_tot_pages = ceil($matches['total'] / CRH_NB_RECORD);
        $Misc_model = new Misc_model();
        $data['pagination'] = $Misc_model->pagination(
                $data['current_page_url'],
                $nb_tot_pages,
                $page_number,
                $more_params
            );

        $this->load->model('clubs_model');
        $Clubs_model = new Clubs_model();
        $data['clubs'] = $Clubs_model->getClubsAsArray(true);
        $data['club_referee'] = $Clubs_model->getClubsAsArray(true, true);
        $this->load->model('categories_model');
        $Categories_model = new Categories_model();
        $data['categories'] = $Categories_model->getCategoriesAsArray(true);
        $this->load->model('teams_model');
        $Teams_model = new Teams_model();
        $data['teams'] = $Teams_model->getTeamsAsArray(true);
        $data['status'] = $Teams_model->getTeamStatus();

        //-----------------------------
        // template
        //-----------------------------
        $this->template->write('title', 'Matches');
        $login_menu = $this->_getLoginMenu();
        $this->template->write('login_menu', $login_menu);
        $menu = $this->_getMenu('match');
        $this->template->write('menu', $menu);
        $this->template->write_view('main_content', 'matches/partial_filters_public', $data);
        $this->template->write_view('main_content', 'matches/' . $tpl, $data);
        $this->template->render();
    }


    /**
     * manage matches
     *
     * @param string $display, display type (list|search|order)
     * @param int $page_number
     * @param string $orderby, sort column
     * @param string $order, order asc|desc
     */
    public function managePlanningMatches($display, $page_number = 1, $orderby = '', $order = 'asc')
    {
        $Permissions_model = new Permissions_model();
        $Permissions_model->userHasAccess('match_list');

        $data = array('filters' => array());
        $more_params = '';

        $this->load->helper('form');
        $this->load->library('form_validation');

        $message = $this->session->flashdata('message');
        $type_message =$this->session->flashdata('type_message');
        $data['message'] = _set_result_message($message, $type_message);

        if ($display == 'search'
            or $display == 'order') {

            // get search filters
            $this->form_validation->set_rules('fp_club', '', 'trim|xss_clean');
            $this->form_validation->set_rules('fp_cat', '', 'trim|xss_clean');
            $this->form_validation->set_rules('fp_referee', '', 'trim|xss_clean');
            $this->form_validation->set_rules('fp_cs', '', 'trim|xss_clean');
            $this->form_validation->set_rules('fp_date_from', '', 'trim|xss_clean');
            $this->form_validation->set_rules('fp_date_to', '', 'trim|xss_clean');

            if ($this->form_validation->run() === false) {

                // the user has ordered the results
                // and/or is browsing on multiple pages after a search
                // => must get search filters (stored in session)
                $f_club      = $this->session->userdata('fp_club');
                $f_cat       = $this->session->userdata('fp_cat');
                $f_referee   = $this->session->userdata('fp_referee');
                $f_cs        = $this->session->userdata('fp_cs');
                $f_date_from = $this->session->userdata('fp_date_from');
                $f_date_to   = $this->session->userdata('fp_date_to');
            } else {

                // user just pushed search button so get fresh filters
                $f_club      = set_value('fp_club', '');
                $f_cat       = set_value('fp_cat', '');
                $f_referee   = set_value('fp_referee', '');
                $f_cs        = set_value('fp_cs', '0');
                $f_date_from = set_value('fp_date_from', '');
                $f_date_to   = set_value('fp_date_to', '');
                // store in session
                $this->session->set_userdata(array(
                    'fp_club'      => $f_club,
                    'fp_cat'       => $f_cat,
                    'fp_referee'   => $f_referee,
                    'fp_cs'        => $f_cs,
                    'fp_date_from' => $f_date_from,
                    'fp_date_to'   => $f_date_to
                ));
            }

            $data['filters'] = array(
                'club'         => $f_club,
                'cat'          => $f_cat,
                'club_referee' => $f_referee,
                'cs'           => $f_cs,
                'date_from'    => $f_date_from,
                'date_to'      => $f_date_to,
            );

            // if visitor has sorted the list
            if ($display == 'order') {
                if ($orderby == 'date') {
                    $order_type = 'order_date';
                } elseif ($orderby == 'cat') {
                    $order_type = 'order_cat';
                } elseif ($orderby == 'cs') {
                    $order_type = 'order_cs';
                } elseif ($orderby == 'equ1') {
                    $order_type = 'order_team1';
                } elseif ($orderby == 'equ2') {
                    $order_type = 'order_team2';
                } else {
                    $order_type = 'order_date';
                }
                if ($order != 'desc') {
                    $order = 'asc';
                }
                $data['filters'][$order_type] = $order;
                $more_params = '/' . $orderby . '-' . $order;
            }
        }
        // if $display = list
        else {
            // reset search filters
            $this->session->unset_userdata(array(
                'fp_date_from' => '',
                'fp_date_to'   => '',
                'fp_club'      => '',
                'fp_cat'       => '',
                'fp_cs'        => '',
                'fp_referee'   => '',
            ));
            $this->session->set_userdata(array('fp_date_from' => date('d/m/Y')));
            $data['filters'] = array('date_from' => date('d/m/Y'));
        }
        //-- end filters

        $nb_per_page = 10;
        $limit = ($page_number - 1) * $nb_per_page;
        $Matches_model = new Matches_model();
        $matches = $Matches_model->getAllMatches($data['filters'], $limit, $nb_per_page);
        $data['matches'] = $matches['result'];

        // pagination
        $data['current_page_url'] = _recreate_uri(2, $this->uri->segment_array());
        $nb_tot_pages = ceil($matches['total'] / $nb_per_page);
        $Misc_model = new Misc_model();
        $data['pagination'] = $Misc_model->pagination(
                $data['current_page_url'],
                $nb_tot_pages,
                $page_number,
                $more_params
            );

        $this->load->model('clubs_model');
        $Clubs_model = new Clubs_model();
        $data['clubs'] = $Clubs_model->getClubsAsArray(true);
        $data['club_referee'] = $Clubs_model->getClubsAsArray(true, true);
        $this->load->model('categories_model');
        $Categories_model = new Categories_model();
        $data['categories'] = $Categories_model->getCategoriesAsArray(true);
        $this->load->model('championships_model');
        $Championships_model = new Championships_model();
        $data['cs'] = $Championships_model->getCsAsArray(true);
        $this->load->model('places_model');
        $Places_model = new Places_model();
        $data['places'] = $Places_model->getPlacesAsArray(true);
        $this->load->model('teams_model');
        $Teams_model = new Teams_model();
        $data['teams'] = $Teams_model->getTeamsAsArray(true);
        $data['status'] = $Teams_model->getTeamStatus();

        //-----------------------------
        // template
        //-----------------------------
        $this->template->write('title', 'Gestion des matches');
        $menu = $this->_getMenu('planning');
        $this->template->write('menu', $menu);
        $this->template->write_view('main_content', 'matches/match_general_planning', $data);
        $this->template->render();
    }


    /**
     * records matches modifications
     */
    public function validatePlanningMatches()
    {
        $Permissions_model = new Permissions_model();
        $Permissions_model->userHasAccess('match_edit');
        $this->load->library('form_validation');
        $data = array();

        $this->form_validation->set_rules('match_date', 'Date du match', 'xss_clean');
        $this->form_validation->set_rules('match_time', 'Heure du match', 'xss_clean');
        $this->form_validation->set_rules('match_place', 'Lieu du match', 'xss_clean');
        $this->form_validation->set_rules('match_cs', 'Compétition', 'xss_clean');
        $this->form_validation->set_rules('match_cat', 'Catégorie', 'xss_clean');
        $this->form_validation->set_rules('match_team1', 'Equipe 1', 'xss_clean');
        $this->form_validation->set_rules('match_team2', 'Equipe 2', 'xss_clean');
        $this->form_validation->set_rules('match_team1_status', 'Statut équipe 1', 'xss_clean');
        $this->form_validation->set_rules('match_team2_status', 'Statut équipe 2', 'xss_clean');

        if ($this->form_validation->run() === true) {
            foreach ($_POST['match_date'] as $match_id => $match_date) {
                $date = set_value('match_date');
                if (_myCheckDate($date, 'fr')) {
                    $data[$match_id]->match_datetime = _date2ISO($date);
                }
            }
            foreach ($_POST['match_time'] as $match_id => $match_time) {
                $time = set_value('match_time');
                if (isset($data[$match_id]->match_datetime)) {
                    $data[$match_id]->match_datetime .= ' ' . _hour2Iso($time);
                }
            }
            foreach ($_POST['match_place'] as $match_id => $match_place) {
                $place = set_value('match_place');
                $data[$match_id]->match_place_id = $place;
            }
            foreach ($_POST['match_cs'] as $match_id => $match_cs) {
                $cs = set_value('match_cs');
                $data[$match_id]->match_cs_id = $cs;
            }
            foreach ($_POST['match_cat'] as $match_id => $match_cat) {
                $cat = set_value('match_cat');
                $data[$match_id]->match_category_id = $cat;
            }
            foreach ($_POST['match_team1'] as $match_id => $match_team1) {
                $team1 = set_value('match_team1');
                $data[$match_id]->match_team1 = $team1;
            }
            foreach ($_POST['match_team2'] as $match_id => $match_team2) {
                $team2 = set_value('match_team2');
                $data[$match_id]->match_team2 = $team2;
            }
            foreach ($_POST['match_team1_status'] as $match_id => $match_team1_status) {
                $team1 = set_value('match_team1_status');
                $data[$match_id]->match_team1_status = $team1;
            }
            foreach ($_POST['match_team2_status'] as $match_id => $match_team2_status) {
                $team2_status = set_value('match_team2_status');
                $data[$match_id]->match_team2_status = $team2_status;
            }
            $Matches_model = new Matches_model();
            $Matches_model->massUpdate($data);

            $this->session->set_flashdata('message', 'Les matches ont été mis à jour.');
            $this->session->set_flashdata('type_message', CRH_TYPE_MSG_SUCCESS);
        } else {
            $this->session->set_flashdata('message', 'Une erreur est survenue.');
            $this->session->set_flashdata('type_message', CRH_TYPE_MSG_ERROR);
        }
        $referer = $_SERVER['HTTP_REFERER'];
        redirect($referer);
    }


    /**
     * manage matches only for some teams
     *
     * @param string $display, display type (list|search|order)
     * @param int $page_number
     * @param string $orderby, sort column
     * @param string $order, order asc|desc
     */
    public function manageTeamMatches($display, $page_number = 1, $orderby = '', $order = 'asc')
    {
        $Permissions_model = new Permissions_model();
        $Permissions_model->userHasAccess('match_edit');

        $user_id = $this->session->userdata('sess_user_id');
        $club_id = $this->session->userdata('sess_club_id');
        $club_name = $this->session->userdata('sess_club_name');
        $this->load->model('teams_model');
        $Teams_model = new Teams_model();
        $tmp_teams = $Teams_model->getTeamsByUser($user_id);
        $teams = array();
        $tmp_categories = array();
        foreach ($tmp_teams as $row) {
            $teams[$row->team_id] = $row->team_name;
            $tmp_categories[] = $row->team_category_id;
        }
        $this->load->model('categories_model');
        $Categories_model = new Categories_model();
        $categories = $Categories_model->getFilteredCategoriesAsArray($tmp_categories);

        $data = array('filters' => array(), 'show_search' => false);
        $more_params = '';

        $this->load->helper('form');
        $this->load->library('form_validation');

        $message = $this->session->flashdata('message');
        $type_message =$this->session->flashdata('type_message');
        $data['message'] = _set_result_message($message, $type_message);

        if ($display == 'search'
            or $display == 'order') {

            // get search filters
            $this->form_validation->set_rules('f_club', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_cat', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_team', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_referee', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_where', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_date_from', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_date_to', '', 'trim|xss_clean');

            if ($this->form_validation->run() === false) {

                // the user has ordered the results
                // and/or is browsing on multiple pages after a search
                // => must get search filters (stored in session)
                $fp_club      = $this->session->userdata('fp_club');
                $fp_cat       = $this->session->userdata('fp_cat');
                $fp_team      = $this->session->userdata('fp_team');
                $fp_referee   = $this->session->userdata('fp_referee');
                $fp_where     = $this->session->userdata('fp_where');
                $fp_date_from = $this->session->userdata('fp_date_from');
                $fp_date_to   = $this->session->userdata('fp_date_to');
            } else {

                // user just pushed search button so get fresh filters
                $fp_club      = set_value('f_club', '');
                $fp_cat       = set_value('f_cat', '');
                $fp_team      = set_value('f_team', '');
                $fp_referee   = set_value('f_referee', '');
                $fp_where     = set_value('f_where', '0');
                $fp_date_from = set_value('f_date_from', '');
                $fp_date_to   = set_value('f_date_to', '');
                // store in session
                $this->session->set_userdata(array(
                    'fp_club'      => $fp_club,
                    'fp_cat'       => $fp_cat,
                    'fp_cats'      => $categories,
                    'fp_team'      => $fp_team,
                    'fp_teams'     => $teams,
                    'fp_referee'   => $fp_referee,
                    'fp_where'     => $fp_where,
                    'fp_date_from' => $fp_date_from,
                    'fp_date_to'   => $fp_date_to
                ));
            }

            $data['filters'] = array(
                'club'         => $fp_club,
                'cat'          => $fp_cat,
                'categories'   => $categories,
                'team'         => $fp_team,
                'teams'        => $teams,
                'club_referee' => $fp_referee,
                'place'        => $fp_where,
                'date_from'    => $fp_date_from,
                'date_to'      => $fp_date_to,
            );

            // if visitor has sorted the list
            if ($display == 'order') {
                if ($orderby == 'date') {
                    $order_type = 'order_date';
                } elseif ($orderby == 'cat') {
                    $order_type = 'order_cat';
                } elseif ($orderby == 'equ1') {
                    $order_type = 'order_team1';
                } elseif ($orderby == 'equ2') {
                    $order_type = 'order_team2';
                } elseif ($orderby == 'referee1') {
                    $order_type = 'order_referee1';
                } elseif ($orderby == 'referee2') {
                    $order_type = 'order_referee2';
                } else {
                    $order_type = 'order_date';
                }
                if ($order != 'desc') {
                    $order = 'asc';
                }
                $data['filters'][$order_type] = $order;
                $more_params = '/' . $orderby . '-' . $order;
            }
        }
        // if $display = list
        else {
            // reset search filters
            $this->session->unset_userdata(array(
                'fp_date_from' => '',
                'fp_date_to'   => '',
                'fp_club'      => '',
                'fp_cat'       => '',
                'fp_cats'      => '',
                'fp_team'      => '',
                'fp_teams'     => '',
                'fp_where'     => '',
                'fp_referee'   => '',
            ));
            $this->session->set_userdata(array('fp_club' => $club_id, 'fp_teams' => $teams, 'fp_cats' => $categories, 'fp_where' => 0, 'fp_date_from' => date('d/m/Y')));
            $data['filters'] = array('club' => $club_id, 'teams' => $teams, 'categories' => $categories, 'place' => 0, 'date_from' => date('d/m/Y'));
        }
        //-- end filters

        $nb_per_page = 10;
        $limit = ($page_number - 1) * $nb_per_page;
        $Matches_model = new Matches_model();
        $matches = $Matches_model->getAllMatches($data['filters'], $limit, $nb_per_page);
        $data['matches'] = $matches['result'];

        // pagination
        $data['current_page_url'] = _recreate_uri(2, $this->uri->segment_array());
        $nb_tot_pages = ceil($matches['total'] / $nb_per_page);
        $Misc_model = new Misc_model();
        $data['pagination'] = $Misc_model->pagination(
                $data['current_page_url'],
                $nb_tot_pages,
                $page_number,
                $more_params
            );

        $this->load->model('clubs_model');
        $data['clubs'] = array($club_id => $club_name);
        $Clubs_model = new Clubs_model();
        $data['club_referee'] = $Clubs_model->getClubsAsArray(true, true);
        $data['categories'] = $categories;
        $this->load->model('teams_model');
        $data['teams'] = $teams;
        $Teams_model = new Teams_model();
        $data['status'] = $Teams_model->getTeamStatus();
        $data['actions'] = $Teams_model->getTeamActions(true);

        $data['title'] = "Matches de mes équipes";
        $data['form_url'] = site_url("equipes/recherche-matches");
        $data['form_raz'] = site_url('equipes/gestion-matches');

        //-----------------------------
        // template
        //-----------------------------
        $this->template->write('title', $data['title']);
        $menu = $this->_getMenu('team');
        $this->template->write('menu', $menu);
        $this->template->write_view('main_content', 'matches/partial_filters_public', $data);
        $this->template->write_view('main_content', 'matches/match_team_planning', $data);
        $this->template->render();
    }


    /**
     * records matches modifications
     */
    public function validateTeamMatches()
    {
        $Permissions_model = new Permissions_model();
        $Permissions_model->userHasAccess('match_edit');
        $this->load->model('notifications_model');
        $this->load->library('form_validation');
        $data = array();
        $notification_data = array();

        $this->form_validation->set_rules('match_team1_status', 'Statut équipe 1', 'xss_clean');
        $this->form_validation->set_rules('match_team1_previous_status', 'Précédent statut équipe 1', 'xss_clean');
        $this->form_validation->set_rules('match_team2_status', 'Statut équipe 2', 'xss_clean');
        $this->form_validation->set_rules('match_team2_previous_status', 'Précédent statut équipe 2', 'xss_clean');

        if ($this->form_validation->run() === true) {
            $previous_team1_status = array();
            foreach ($_POST['match_team1_previous_status'] as $match_id => $team_status) {
                $previous_status = set_value('match_team1_previous_status');
                $previous_team1_status[$match_id] = $previous_status;
            }
            foreach ($_POST['match_team1_status'] as $match_id => $match_status) {
                $status = set_value('match_team1_status');
                $data[$match_id]->match_team1_status = $status;
                // send an alert if team asked defer or cancel
                if (($status == 'R' && (! isset($previous_team1_status[$match_id]) || $previous_team1_status[$match_id] != 'R'))
                    || ($status == 'F' && (! isset($previous_team1_status[$match_id]) || $previous_team1_status[$match_id] != 'F'))) {
                    $notification_data[$match_id] = array(
                        'team' => 1,
                        'status' => $status
                    );
                }
            }
            $previous_team2_status = array();
            foreach ($_POST['match_team2_previous_status'] as $match_id => $team_status) {
                $previous_status = set_value('match_team2_previous_status');
                $previous_team2_status[$match_id] = $previous_status;
            }
            foreach ($_POST['match_team2_status'] as $match_id => $match_status) {
                $status = set_value('match_team2_status');
                $data[$match_id]->match_team2_status = $status;
                // send an alert if team asked defer or cancel
                if (($status == 'R' && (! isset($previous_team2_status[$match_id]) || $previous_team2_status[$match_id] != 'R'))
                    || ($status == 'F' && (! isset($previous_team2_status[$match_id]) || $previous_team2_status[$match_id] != 'F'))) {
                    $notification_data[$match_id] = array(
                        'team' => 2,
                        'status' => $status
                    );
                }
            }
            $Matches_model = new Matches_model();
            $Matches_model->massUpdate($data);
            $Notifications_model = new Notifications_model();
            $Notifications_model->massAlertTeamNok($notification_data);

            $this->session->set_flashdata('message', 'Les matches ont été mis à jour.');
            $this->session->set_flashdata('type_message', CRH_TYPE_MSG_SUCCESS);
        } else {
            $this->session->set_flashdata('message', 'Une erreur est survenue.');
            $this->session->set_flashdata('type_message', CRH_TYPE_MSG_ERROR);
        }
        $referer = $_SERVER['HTTP_REFERER'];
        redirect($referer);
    }


    /**
     * manage matches only for a club
     *
     * @param string $display, display type (list|search|order)
     * @param int $page_number
     * @param string $orderby, sort column
     * @param string $order, order asc|desc
     */
    public function manageClubMatches($display, $page_number = 1, $orderby = '', $order = 'asc')
    {
        $Permissions_model = new Permissions_model();
        $Permissions_model->userHasAccess('match_edit');

        $club_id = $this->session->userdata('sess_club_id');
        $club_name = $this->session->userdata('sess_club_name');
        $this->load->model('teams_model');
        $Teams_model = new Teams_model();
        $teams = $Teams_model->getTeamsByClub($club_id);

        $data = array('filters' => array(), 'current_club' => $club_id, 'show_search' => false);
        $more_params = '';

        $this->load->helper('form');
        $this->load->library('form_validation');

        $message = $this->session->flashdata('message');
        $type_message =$this->session->flashdata('type_message');
        $data['message'] = _set_result_message($message, $type_message);

        if ($display == 'search'
            or $display == 'order') {

            // get search filters
            $this->form_validation->set_rules('f_club', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_cat', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_team', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_referee', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_where', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_date_from', '', 'trim|xss_clean');
            $this->form_validation->set_rules('f_date_to', '', 'trim|xss_clean');

            if ($this->form_validation->run() === false) {

                // the user has ordered the results
                // and/or is browsing on multiple pages after a search
                // => must get search filters (stored in session)
                $f_club      = $this->session->userdata('fp_club');
                $f_cat       = $this->session->userdata('fp_cat');
                $f_team      = $this->session->userdata('fp_team');
                $teams       = $this->session->userdata('fp_teams');
                $f_referee   = $this->session->userdata('fp_referee');
                $f_where     = $this->session->userdata('fp_where');
                $f_date_from = $this->session->userdata('fp_date_from');
                $f_date_to   = $this->session->userdata('fp_date_to');
            } else {

                // user just pushed search button so get fresh filters
                $f_club      = set_value('f_club', '');
                $f_cat       = set_value('f_cat', '');
                $f_team      = set_value('f_team', '');
                $f_referee   = set_value('f_referee', '');
                $f_where     = set_value('f_where', '0');
                $f_date_from = set_value('f_date_from', '');
                $f_date_to   = set_value('f_date_to', '');
                // store in session
                $this->session->set_userdata(array(
                    'fp_club'      => $f_club,
                    'fp_cat'       => $f_cat,
                    'fp_team'      => $f_team,
                    'fp_teams'     => $teams,
                    'fp_referee'   => $f_referee,
                    'fp_where'     => $f_where,
                    'fp_date_from' => $f_date_from,
                    'fp_date_to'   => $f_date_to
                ));
            }

            $data['filters'] = array(
                'club'         => $f_club,
                'cat'          => $f_cat,
                'team'         => $f_team,
                'teams'        => $teams,
                'club_referee' => $f_referee,
                'place'        => $f_where,
                'date_from'    => $f_date_from,
                'date_to'      => $f_date_to,
            );

            // if visitor has sorted the list
            if ($display == 'order') {
                if ($orderby == 'date') {
                    $order_type = 'order_date';
                } elseif ($orderby == 'cat') {
                    $order_type = 'order_cat';
                } elseif ($orderby == 'equ1') {
                    $order_type = 'order_team1';
                } elseif ($orderby == 'equ2') {
                    $order_type = 'order_team2';
                } elseif ($orderby == 'referee1') {
                    $order_type = 'order_referee1';
                } elseif ($orderby == 'referee2') {
                    $order_type = 'order_referee2';
                } else {
                    $order_type = 'order_date';
                }
                if ($order != 'desc') {
                    $order = 'asc';
                }
                $data['filters'][$order_type] = $order;
                $more_params = '/' . $orderby . '-' . $order;
            }
        }
        // if $display = list
        else {
            // reset search filters
            $this->session->unset_userdata(array(
                'fp_date_from' => '',
                'fp_date_to'   => '',
                'fp_club'      => '',
                'fp_cat'       => '',
                'fp_team'      => '',
                'fp_teams'     => '',
                'fp_where'     => '',
                'fp_referee'   => '',
            ));
            $this->session->set_userdata(array('fp_club' => $club_id, 'fp_teams' => $teams, 'fp_where' => 0, 'fp_date_from' => date('d/m/Y')));
            $data['filters'] = array('club' => $club_id, 'teams' => $teams, 'place' => 0, 'date_from' => date('d/m/Y'));
        }
        //-- end filters

        $nb_per_page = 10;
        $limit = ($page_number - 1) * $nb_per_page;
        $Matches_model = new Matches_model();
        $matches = $Matches_model->getAllMatches($data['filters'], $limit, $nb_per_page);
        $data['matches'] = $matches['result'];

        // pagination
        $data['current_page_url'] = _recreate_uri(2, $this->uri->segment_array());
        $nb_tot_pages = ceil($matches['total'] / $nb_per_page);
        $Misc_model = new Misc_model();
        $data['pagination'] = $Misc_model->pagination(
                $data['current_page_url'],
                $nb_tot_pages,
                $page_number,
                $more_params
            );

        $this->load->model('clubs_model');
        $data['clubs'] = array($club_id => $club_name);
        $Clubs_model = new Clubs_model();
        $data['club_referee'] = $Clubs_model->getClubsAsArray(true, true);
        $this->load->model('categories_model');
        $Categories_model = new Categories_model();
        $data['categories'] = $Categories_model->getCategoriesAsArray(true);
        $this->load->model('places_model');
        $Places_model = new Places_model();
        $data['places'] = $Places_model->getPlacesAsArray(true);
        $this->load->model('teams_model');
        $data['teams'] = $teams;
        $Teams_model = new Teams_model();
        $data['status'] = $Teams_model->getTeamStatus();
        $data['actions'] = $Teams_model->getTeamActions(true);

        $data['title'] = "Matches de mon club";
        $data['form_url'] = site_url("club/recherche-matches");
        $data['form_raz'] = site_url('club/gestion-matches');

        //-----------------------------
        // template
        //-----------------------------
        $this->template->write('title', $data['title']);
        $menu = $this->_getMenu('club');
        $this->template->write('menu', $menu);
        $this->template->write_view('main_content', 'matches/partial_filters_public', $data);
        $this->template->write_view('main_content', 'matches/match_club_planning', $data);
        $this->template->render();
    }


    /**
     * records club matches modifications
     */
    public function validateClubMatches()
    {
        $Permissions_model = new Permissions_model();
        $Permissions_model->userHasAccess('match_edit');

        $this->load->model('notifications_model');
        $this->load->library('form_validation');
        $data = array();
        $notification_data = array();

        $this->form_validation->set_rules('match_date', 'Date du match', 'xss_clean');
        $this->form_validation->set_rules('match_time', 'Heure du match', 'xss_clean');
        $this->form_validation->set_rules('match_place', 'Lieu du match', 'xss_clean');
        $this->form_validation->set_rules('match_team1_status', 'Statut équipe 1', 'xss_clean');
        $this->form_validation->set_rules('match_team1_previous_status', 'Précédent statut équipe 1', 'xss_clean');
        $this->form_validation->set_rules('match_team2_status', 'Statut équipe 2', 'xss_clean');
        $this->form_validation->set_rules('match_team2_previous_status', 'Précédent statut équipe 2', 'xss_clean');

        if ($this->form_validation->run() === true) {
            foreach ($_POST['match_date'] as $match_id => $match_date) {
                $date = set_value('match_date');
                if (_myCheckDate($date, 'fr')) {
                    $data[$match_id]->match_datetime = _date2ISO($date);
                }
            }
            foreach ($_POST['match_time'] as $match_id => $match_time) {
                $time = set_value('match_time');
                if (isset($data[$match_id]->match_datetime)) {
                    $data[$match_id]->match_datetime .= ' ' . _hour2Iso($time);
                }
            }
            foreach ($_POST['match_place'] as $match_id => $match_place) {
                $place = set_value('match_place');
                $data[$match_id]->match_place_id = $place;
            }
            $previous_team1_status = array();
            foreach ($_POST['match_team1_previous_status'] as $match_id => $team_status) {
                $previous_status = set_value('match_team1_previous_status');
                $previous_team1_status[$match_id] = $previous_status;
            }
            foreach ($_POST['match_team1_status'] as $match_id => $match_status) {
                $status = set_value('match_team1_status');
                $data[$match_id]->match_team1_status = $status;
                // send an alert if team asked defer or cancel
                if (($status == 'R' && (! isset($previous_team1_status[$match_id]) || $previous_team1_status[$match_id] != 'R'))
                    || ($status == 'F' && (! isset($previous_team1_status[$match_id]) || $previous_team1_status[$match_id] != 'F'))) {
                    $notification_data[$match_id] = array(
                        'team' => 1,
                        'status' => $status
                    );
                }
            }
            $previous_team2_status = array();
            foreach ($_POST['match_team2_previous_status'] as $match_id => $team_status) {
                $previous_status = set_value('match_team2_previous_status');
                $previous_team2_status[$match_id] = $previous_status;
            }
            foreach ($_POST['match_team2_status'] as $match_id => $match_status) {
                $status = set_value('match_team2_status');
                $data[$match_id]->match_team2_status = $status;
                // send an alert if team asked defer or cancel
                if (($status == 'R' && (! isset($previous_team2_status[$match_id]) || $previous_team2_status[$match_id] != 'R'))
                    || ($status == 'F' && (! isset($previous_team2_status[$match_id]) || $previous_team2_status[$match_id] != 'F'))) {
                    $notification_data[$match_id] = array(
                        'team' => 2,
                        'status' => $status
                    );
                }
            }
            $Matches_model = new Matches_model();
            $Matches_model->massUpdate($data);
            $Notifications_model = new Notifications_model();
            $Notifications_model->massAlertTeamNok($notification_data);

            $this->session->set_flashdata('message', 'Les matches ont été mis à jour.');
            $this->session->set_flashdata('type_message', CRH_TYPE_MSG_SUCCESS);
        } else {
            $this->session->set_flashdata('message', 'Une erreur est survenue.');
            $this->session->set_flashdata('type_message', CRH_TYPE_MSG_ERROR);
        }
        $referer = $_SERVER['HTTP_REFERER'];
        redirect($referer);
    }


    /**
     * add/edit a match
     *
     * @param int $match_id, 0 if new
     */
    public function form($match_id = 0)
    {
        $Permissions_model = new Permissions_model();
        if ($match_id == 0) {
            $Permissions_model->userHasAccess('match_add');
        } else {
            $Permissions_model->userHasAccess('match_edit');
        }
        $this->load->helper(array('form'));
        $data = array();
        $data['match_id'] = $match_id;

        $message = $this->session->flashdata('message');
        $type_message =$this->session->flashdata('type_message');
        $data['message'] = _set_result_message($message, $type_message);

        $title = 'Ajouter';
        if ($match_id > 0) {
            $Matches_model = new Matches_model();
            $data['match'] = $Matches_model->getMatch($match_id);
            $title = 'Modifier';
        }
        $data['title'] = $title . ' un match';

        $this->load->model('categories_model');
        $Categories_model = new Categories_model();
        $data['categories'] = $Categories_model->getCategoriesAsArray(true);
        $this->load->model('championships_model');
        $Championships_model = new Championships_model();
        $data['cs'] = $Championships_model->getCsAsArray(true);
        $this->load->model('places_model');
        $Places_model = new Places_model();
        $data['places'] = $Places_model->getPlacesAsArray(true);
        $this->load->model('teams_model');
        $Teams_model = new Teams_model();
        $data['teams'] = $Teams_model->getTeamsAsArray(true);

        //-----------------------------
        // template
        //-----------------------------
        $this->template->write('title', $data['title']);
        $menu = $this->_getMenu('planning');
        $this->template->write('menu', $menu);
        $this->template->write_view('main_content', 'matches/match_form', $data);
        $this->template->render();
    }


    /**
     * add/edit form validation
     */
    public function validate()
    {
        $match_id = $this->input->post('match_id', true);
        $Permissions_model = new Permissions_model();
        if ($match_id == 0) {
            $Permissions_model->userHasAccess('match_add');
        } else {
            $Permissions_model->userHasAccess('match_edit');
        }
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $data = array();

        // validation rules
        $this->form_validation->set_rules('match_id', 'match_id', 'integer');
        $this->form_validation->set_rules('date', 'Date', 'trim|xss_clean');
        $this->form_validation->set_rules('time', 'Heure', 'trim|xss_clean');
        $this->form_validation->set_rules('place', 'Lieu', 'trim|xss_clean');
        $this->form_validation->set_rules('cs', 'Compétition', 'trim|required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('cat', 'Catégorie', 'trim|required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('team1', 'Equipe 1', 'trim|required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('team2', 'Equipe 2', 'trim|required|is_natural_no_zero|xss_clean');
        $this->form_validation->set_rules('comments', 'Commentaires', 'trim|xss_clean');

        // run form validation
        $result = $this->form_validation->run();

        $data['match_id'] = set_value('match_id', 0);
        $date_format = set_value('date');
        $time_format = set_value('time');
        $data['match']->match_date_format = $date_format;
        $data['match']->match_time_format = $time_format;
        $data['match']->match_datetime = _date2ISO($data['match']->match_date_format) . ' ' . _hour2Iso($data['match']->match_time_format);
        $data['match']->match_place_id = set_value('place');
        $data['match']->match_cs_id = set_value('cs');
        $data['match']->match_category_id = set_value('cat');
        $data['match']->match_team1 = set_value('team1');
        $data['match']->match_team2 = set_value('team2');
        $data['match']->match_comments = set_value('comments');

        //-------------------------------------
        if ($result !== false) {
            $Matches_model = new Matches_model();
            $data['match_id'] = $Matches_model->setMatch($data['match_id'], $data['match']);
            if ($data['match_id'] == CRH_ERROR_DATA_EMPTY) {
                $this->session->set_flashdata('message', 'Aucune donnée à enregistrer.');
                $this->session->set_flashdata('type_message', CRH_TYPE_MSG_ERROR);
            } else {
                $message = "Le match a bien été enregistré.";
                $type_message = CRH_TYPE_MSG_SUCCESS;
            }
        } else {
            $message = 'Veuillez vérifier votre saisie.';
            $type_message = CRH_TYPE_MSG_ERROR;
        }

        $data['match']->match_date_format = $date_format;
        $data['match']->match_time_format = $time_format;

        $data['message'] = _set_result_message($message, $type_message);

        $title = 'Ajouter';
        if ($data['match_id'] > 0) {
            $title = 'Modifier un';
        }
        $data['title'] = $title . ' un match';

        $this->load->model('categories_model');
        $Categories_model = new Categories_model();
        $data['categories'] = $Categories_model->getCategoriesAsArray(true);
        $this->load->model('championships_model');
        $Championships_model = new Championships_model();
        $data['cs'] = $Championships_model->getCsAsArray(true);
        $this->load->model('places_model');
        $Places_model = new Places_model();
        $data['places'] = $Places_model->getPlacesAsArray(true);
        $this->load->model('teams_model');
        $Teams_model = new Teams_model();
        $data['teams'] = $Teams_model->getTeamsAsArray(true);

        //-----------------------------
        // template
        //-----------------------------
        $this->template->write('title', $data['title']);
        $menu = $this->_getMenu('planning');
        $this->template->write('menu', $menu);
        $this->template->write_view('main_content', 'matches/match_form', $data);
        $this->template->render();
    }


    /**
     * delete a match
     */
    public function del($match_id)
    {
        $Permissions_model = new Permissions_model();
        $Permissions_model->userHasAccess('match_del');
        if (! empty($match_id)) {
            $Matches_model = new Matches_model();
            $Matches_model->delMatch($match_id);
            $referer = $_SERVER['HTTP_REFERER'];
            redirect($referer);
        }
    }
}

/* End of file matches.php */
/* Location: ./application/controllers/matches.php */
