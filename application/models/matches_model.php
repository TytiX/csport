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
 * matches management
 *
 * @version $Id: matches_model.php 91 2014-09-23 07:08:20Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 *
 */

class Matches_model extends CI_Model
{


    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * get matches
     *
     * @param array $criteria, a list of filters
     * @param int $limit, from number
     * @param int $nb, to number ; default CRH_NB_RECORD
     *
     * @return array $result (total, result)
     */
    public function getAllMAtches($criteria, $limit = 0, $nb = CRH_NB_RECORD)
    {
        $result = array();
        $this->db->select("SQL_CALC_FOUND_ROWS match_id, match_datetime,
					IF(DATE_FORMAT(match_datetime, '" . CRH_SQL_DATE_FORMAT . "') <> '00/00/0000', DATE_FORMAT(match_datetime, '" . CRH_SQL_DATE_FORMAT . "'), '')  AS match_date_format,
					IF(DATE_FORMAT(match_datetime, '" . CRH_SQL_TIME_FORMAT . "') <> '00h00', DATE_FORMAT(match_datetime, '" . CRH_SQL_TIME_FORMAT . "'), '')  AS match_time_format,
					match_team1, match_team1_status, match_team1_score, team1.team_name AS team1_name, team1.team_club_id AS team1_club_id,
					match_team2, match_team2_status, match_team2_score, team2.team_name AS team2_name, team2.team_club_id AS team2_club_id,
					match_scoresheet, match_complete, match_comments,
					cs_id, cs_name,
					category_id, category_name,
					place_id, place_name,
					match_referee1_club, match_referee1, match_referee1_status, club1.club_name AS referee1_club_name, referee1.user_name AS referee1_name,
					match_referee2_club, match_referee2, match_referee2_status, club2.club_name AS referee2_club_name, referee2.user_name AS referee2_name
					", false)
                ->from('matches')
                ->join('teams AS team1', 'team1.team_id = match_team1', 'left')
                ->join('teams AS team2', 'team2.team_id = match_team2', 'left')
                ->join('clubs AS club1', 'club1.club_id = match_referee1_club', 'left')
                ->join('clubs AS club2', 'club2.club_id = match_referee2_club', 'left')
                ->join('users AS referee1', 'referee1.user_id = match_referee1', 'left')
                ->join('users AS referee2', 'referee2.user_id = match_referee2', 'left')
                ->join('championships', 'cs_id = match_cs_id', 'left')
                ->join('categories', 'category_id = match_category_id', 'left')
                ->join('places', 'place_id = match_place_id', 'left');
        // limit (if $nb = 0 -> get all)
        if ($nb > 0) {
            $this->db->limit($nb, $limit);
        }

        //-----------------------------
        // filters
        //-----------------------------
        if (isset($criteria['club']) && ! empty($criteria['club'])) {
            if (isset($criteria['place']) && ! empty($criteria['place'])) {
                // receiving team
                if ($criteria['place'] == 1) {
                    $this->db->where('team1.team_club_id', $criteria['club']);
                }
                // received team
                elseif ($criteria['place'] == 2) {
                    $this->db->where('team2.team_club_id', $criteria['club']);
                }
            } else {
                $this->db->where('(team1.team_club_id = ' . $criteria['club'] . ' OR team2.team_club_id = ' . $criteria['club'] . ')');
            }
        }
        if (isset($criteria['team']) && ! empty($criteria['team'])) {
            if (isset($criteria['place']) && ! empty($criteria['place'])) {
                // receiving team
                if ($criteria['place'] == 1) {
                    $this->db->where('team1.team_id', $criteria['team']);
                }
                // received team
                elseif ($criteria['place'] == 2) {
                    $this->db->where('team2.team_id', $criteria['team']);
                }
            } else {
                $this->db->where('(team1.team_id = ' . $criteria['team'] . ' OR team2.team_id = ' . $criteria['team'] . ')');
            }
        } elseif (isset($criteria['teams']) && ! empty($criteria['teams'])) {
            if (is_array($criteria['teams'])) {
                $criteria['teams'] = array_flip($criteria['teams']);
                $criteria['teams'] = implode(',', $criteria['teams']);
            }
            $this->db->where('(team1.team_id IN (' . $criteria['teams'] . ') OR team2.team_id IN (' . $criteria['teams'] . '))');
        }
        if (isset($criteria['cat']) && ! empty($criteria['cat'])) {
            $this->db->where('(team1.team_category_id = ' . $criteria['cat'] . ' OR team2.team_category_id = ' . $criteria['cat'] . ')');
        } elseif (isset($criteria['categories']) && ! empty($criteria['categories'])) {
            if (is_array($criteria['categories'])) {
                $criteria['categories'] = array_flip($criteria['categories']);
                $criteria['categories'] = implode(',', $criteria['categories']);
            }
            $this->db->where('(team1.team_category_id IN (' . $criteria['categories'] . ') OR team2.team_category_id IN (' . $criteria['categories'] . '))');
        }
        if (isset($criteria['cs']) && ! empty($criteria['cs'])) {
            $this->db->where('(match_cs_id = ' . $criteria['cs'] . ')');
        }
        if (isset($criteria['referee']) && ! empty($criteria['referee'])) {
            $this->db->where('(match_referee1 = ' . $criteria['referee'] . ' OR match_referee2 = ' . $criteria['referee'] . ')');
        }
        if (isset($criteria['club_referee']) && $criteria['club_referee'] != '') {
            $this->db->where('(match_referee1_club = ' . $criteria['club_referee'] . ' OR match_referee2_club = ' . $criteria['club_referee'] . ')');
        }
        if (isset($criteria['date_from']) && ! empty($criteria['date_from'])) {
            $this->db->where('match_datetime >=', _date2ISO($criteria['date_from']) . ' 00:00:00');
        }
        if (isset($criteria['date_to']) &&  ! empty($criteria['date_to'])) {
            $this->db->where('match_datetime <=', _date2ISO($criteria['date_to']) . ' 23:59:59');
        }
        //-----------------------------
        // ordering
        if (isset($criteria['order_date']) && ! empty($criteria['order_date'])) {
            if ($criteria['order_date'] == 'desc') {
                $this->db->order_by('match_datetime desc');
            } else {
                $this->db->order_by('match_datetime asc');
            }
        } elseif (isset($criteria['order_cat']) && ! empty($criteria['order_cat'])) {
            if ($criteria['order_cat'] == 'desc') {
                $this->db->order_by('category_order', 'desc');
            } else {
                $this->db->order_by('category_order', 'asc');
            }
        } elseif (isset($criteria['order_cs']) && ! empty($criteria['order_cs'])) {
            if ($criteria['order_cs'] == 'desc') {
                $this->db->order_by('cs_order desc, cs_name desc');
            } else {
                $this->db->order_by('cs_order asc, cs_name asc');
            }
        } elseif (isset($criteria['order_team1']) && ! empty($criteria['order_team1'])) {
            if ($criteria['order_team1'] == 'desc') {
                $this->db->order_by('team1.team_name desc');
            } else {
                $this->db->order_by('team1.team_name asc');
            }
        } elseif (isset($criteria['order_team2']) && ! empty($criteria['order_team2'])) {
            if ($criteria['order_team2'] == 'desc') {
                $this->db->order_by('team2.team_name desc');
            } else {
                $this->db->order_by('team2.team_name asc');
            }
        } elseif (isset($criteria['order_referee1']) && ! empty($criteria['order_referee1'])) {
            if ($criteria['order_referee1'] == 'desc') {
                $this->db->order_by('referee1_club_name desc');
            } else {
                $this->db->order_by('referee1_club_name asc');
            }
        } elseif (isset($criteria['order_referee2']) && ! empty($criteria['order_referee2'])) {
            if ($criteria['order_referee2'] == 'desc') {
                $this->db->order_by('referee2_club_name desc');
            } else {
                $this->db->order_by('referee2_club_name asc');
            }
        } else {
            $this->db->order_by('match_datetime asc');
        }
        //--
        $query = $this->db->get();
        if ($this->db->_error_message()) {
            _criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
        }
        //echo $this->db->last_query();

        // get total number of records
        $total_query = $this->db->query('SELECT FOUND_ROWS() as total');
        $row_total = $total_query->row();
        $result['total'] = $row_total->total;
        // get users results
        $result['result'] = $query->result();
        return $result;
    }


    /**
     * get incomplete matches
     *
     * @param int $delay, number of days in which the match is programmed
     *
     * @return query results
     */
    public function getIncompleteMatches($delay)
    {
        $limit_date_min = date('Y-m-d H:i:s', mktime('00', '00', '00', date('n'), date('j') + $delay, date('Y')));
        $limit_date_max = date('Y-m-d H:i:s', mktime('23', '59', '59', date('n'), date('j') + $delay, date('Y')));
        $this->db->select("match_id, match_datetime,
				IF(DATE_FORMAT(match_datetime, '" . CRH_SQL_DATE_FORMAT . "') <> '00/00/0000', DATE_FORMAT(match_datetime, '" . CRH_SQL_DATE_FORMAT . "'), '')  AS match_date_format,
				IF(DATE_FORMAT(match_datetime, '" . CRH_SQL_TIME_FORMAT . "') <> '00h00', DATE_FORMAT(match_datetime, '" . CRH_SQL_TIME_FORMAT . "'), '')  AS match_time_format,
				match_team1, match_team1_status, team1.team_name AS team1_name, team1.team_club_id AS team1_club_id,
				match_team2, match_team2_status, team2.team_name AS team2_name, team2.team_club_id AS team2_club_id,
				match_place_id, place_name,
				match_cs_id, cs_name,
				match_category_id, category_name,
				match_referee1_club, match_referee1, match_referee1_status, club1.club_name AS referee1_club_name, referee1.user_name AS referee1_name,
				match_referee2_club, match_referee2, match_referee2_status, club2.club_name AS referee2_club_name, referee2.user_name AS referee2_name", false)
            ->from('matches')
            ->join('teams AS team1', 'team1.team_id = match_team1', 'left')
            ->join('teams AS team2', 'team2.team_id = match_team2', 'left')
            ->join('clubs AS club1', 'club1.club_id = match_referee1_club', 'left')
            ->join('clubs AS club2', 'club2.club_id = match_referee2_club', 'left')
            ->join('users AS referee1', 'referee1.user_id = match_referee1', 'left')
            ->join('users AS referee2', 'referee2.user_id = match_referee2', 'left')
            ->join('championships', 'cs_id = match_cs_id', 'left')
            ->join('categories', 'category_id = match_category_id', 'left')
            ->join('places', 'place_id = match_place_id', 'left')
            ->where('match_datetime >= ', $limit_date_min)
            ->where('match_datetime <= ', $limit_date_max)
            ->where('(match_datetime = "' . $limit_date_min . '"
				OR match_team2 = 0
				OR match_place_id = 0
				OR match_referee1 = 0 OR match_referee1 IS NULL)');
        $query = $this->db->get();
        if ($this->db->_error_message()) {
            _criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
        }
        //echo $this->db->last_query();
        return $query->result();
    }


    /**
     * get matches where teams and/or referees have not confirmed
     *
     * @param int $delay, number of days in which the match is programmed
     *
     * @return query results
     */
    public function getMatchesNotConfirmed($delay)
    {
        $limit_date_min = date('Y-m-d H:i:s', mktime('00', '00', '00', date('n'), date('j') + $delay, date('Y')));
        $limit_date_max = date('Y-m-d H:i:s', mktime('23', '59', '59', date('n'), date('j') + $delay, date('Y')));
        $this->db->select("match_id, match_datetime,
				IF(DATE_FORMAT(match_datetime, '" . CRH_SQL_DATE_FORMAT . "') <> '00/00/0000', DATE_FORMAT(match_datetime, '" . CRH_SQL_DATE_FORMAT . "'), '')  AS match_date_format,
				IF(DATE_FORMAT(match_datetime, '" . CRH_SQL_TIME_FORMAT . "') <> '00h00', DATE_FORMAT(match_datetime, '" . CRH_SQL_TIME_FORMAT . "'), '')  AS match_time_format,
				match_team1, match_team1_status, team1.team_name AS team1_name, team1.team_club_id AS team1_club_id,
				match_team2, match_team2_status, team2.team_name AS team2_name, team2.team_club_id AS team2_club_id,
				match_place_id, place_name,
				match_cs_id, cs_name,
				match_category_id, category_name,
				match_referee1_club, match_referee1, match_referee1_status, club1.club_name AS referee1_club_name, referee1.user_name AS referee1_name,
				match_referee2_club, match_referee2, match_referee2_status, club2.club_name AS referee2_club_name, referee2.user_name AS referee2_name", false)
            ->from('matches')
            ->join('teams AS team1', 'team1.team_id = match_team1', 'left')
            ->join('teams AS team2', 'team2.team_id = match_team2', 'left')
            ->join('clubs AS club1', 'club1.club_id = match_referee1_club', 'left')
            ->join('clubs AS club2', 'club2.club_id = match_referee2_club', 'left')
            ->join('users AS referee1', 'referee1.user_id = match_referee1', 'left')
            ->join('users AS referee2', 'referee2.user_id = match_referee2', 'left')
            ->join('championships', 'cs_id = match_cs_id', 'left')
            ->join('categories', 'category_id = match_category_id', 'left')
            ->join('places', 'place_id = match_place_id', 'left')
            ->where('match_datetime >= ', $limit_date_min)
            ->where('match_datetime <= ', $limit_date_max)
            ->where('(
				(match_team1 IS NOT NULL AND match_team1 <> 0 AND (match_team1_status = "C" OR match_team1_status IS NULL))
				OR
				(match_team2 IS NOT NULL AND match_team2 <> 0 AND (match_team2_status = "C" OR match_team2_status IS NULL))
				OR
				(match_referee1 IS NOT NULL AND match_referee1 <> 0 AND (match_referee1_status = "C" OR match_referee1_status IS NULL))
				OR
				(match_referee2 IS NOT NULL AND match_referee2 <> 0 AND (match_referee2_status = "C" OR match_referee2_status IS NULL))
				)');
        $query = $this->db->get();
        if ($this->db->_error_message()) {
            _criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
        }
        //echo $this->db->last_query();
        return $query->result();
    }


    /**
     * get a match by its id
     *
     * @param int $match_id
     *
     * @return query object
     */
    public function getMatch($match_id)
    {
        $this->db->select('match_id, match_datetime,
				IF(DATE_FORMAT(match_datetime, \'' . CRH_SQL_DATE_FORMAT . '\') <> \'00/00/0000\', DATE_FORMAT(match_datetime, \'' . CRH_SQL_DATE_FORMAT . '\'), \'\')  AS match_date_format,
				IF(DATE_FORMAT(match_datetime, \'' . CRH_SQL_TIME_FORMAT . '\') <> \'00h00\', DATE_FORMAT(match_datetime, \'' . CRH_SQL_TIME_FORMAT . '\'), \'\')  AS match_time_format,
				match_team1, match_team1_status, match_team1_score, team1.team_name AS team1_name, team1.team_club_id AS team1_club_id,
				match_team2, match_team2_status, match_team2_score, team2.team_name AS team2_name, team2.team_club_id AS team2_club_id,
				match_scoresheet, match_complete,
				match_cs_id, cs_id, cs_name,
				match_category_id, category_id, category_name,
				match_place_id, place_id, place_name,
				match_referee1_club, match_referee1, match_referee1_status, club1.club_name AS referee1_club_name, referee1.user_name AS referee1_name,
				match_referee2_club, match_referee2, match_referee2_status, club2.club_name AS referee2_club_name, referee2.user_name AS referee2_name', false)
            ->from('matches')
            ->join('teams AS team1', 'team1.team_id = match_team1', 'left')
            ->join('teams AS team2', 'team2.team_id = match_team2', 'left')
            ->join('clubs AS club1', 'club1.club_id = match_referee1_club', 'left')
            ->join('clubs AS club2', 'club2.club_id = match_referee2_club', 'left')
            ->join('users AS referee1', 'referee1.user_id = match_referee1', 'left')
            ->join('users AS referee2', 'referee2.user_id = match_referee2', 'left')
            ->join('championships', 'cs_id = match_cs_id', 'left')
            ->join('categories', 'category_id = match_category_id', 'left')
            ->join('places', 'place_id = match_place_id', 'left')
            ->where('match_id', $match_id);
        $query = $this->db->get();
        if ($this->db->_error_message()) {
            _criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
        }
        $match = $query->row();
        return $match;
    }


    /**
     * import matches
     *
     * @param string $fieldname, name of $_FILES field
     * @param string $new_name, the new name of imported file
     *
     * @return array( (bool) success, (string) message )
     */
    public function importMatches($fieldname, $new_name)
    {
        $this->load->model('files_model');
        $config['upload_path']   = CRH_PATH_TO_FILES;
        $config['overwrite']     = false;
        $config['allowed_types'] = 'csv';
        $config['file_name']     = $new_name;

        $result = Files_model::uploadFile($fieldname, $config);
        if ($result['success'] === false) {
            return array('success' => false, 'message' => $result['message']);
        }

        $handle = fopen(CRH_PATH_TO_FILES . $new_name, 'rb');
        if (! $handle) {
            return array('success' => false, 'message' => "Le fichier n'a pas pu être lu.");
        }
        $whole_content = fread($handle, filesize(CRH_PATH_TO_FILES . $new_name));
        rewind($handle);
        $to_encoding = 'UTF-8';
        $from_encoding = mb_detect_encoding($whole_content, Files_model::$supported_encoding, true);

        // get all championships
        $this->load->model('championships_model');
        $all_cs = Championships_model::getCsAsArray();
        // get all categories
        $this->load->model('categories_model');
        $all_categories = Categories_model::getCategoriesAsArray();
        // get all places
        $this->load->model('places_model');
        $all_places = Places_model::getPlacesAsArray();
        // get all clubs
        $this->load->model('clubs_model');
        $all_clubs = Clubs_model::getClubsAsArray(false, true);
        // get all users
        $this->load->model('users_model');
        $all_users = Users_model::getUsersAsArray();

        $i = 0; // total lines number
        $j = 1; // lines treated number
        while (($content = fgetcsv($handle, 0, Files_model::CSV_SEP)) !== false) {
            if (count($content) != 20) {
                return array('success' => false, 'message' => "Le fichier n'est pas au bon format ou ne contient pas le bon nombre de colonnes.");
            }
            if ($i > 0) { // ignore first line

                $match_id = protegeImport($content[0], $to_encoding, $from_encoding); // ID : if not empty, this is an update
                if ($match_id == '') {
                    $match_id = 0;
                }

                //----------------------------------------------------------
                // prepare data
                $cs = protegeImport($content[3], $to_encoding, $from_encoding);
                $cs_id = array_search($cs, $all_cs);
                if (empty($cs_id)) {
                    $cs_id = 0;
                }
                $category = protegeImport($content[4], $to_encoding, $from_encoding);
                $category_id = array_search($category, $all_categories);
                if (empty($category_id)) {
                    $category_id = 0;
                }

                $place = protegeImport($content[10], $to_encoding, $from_encoding);
                $place_id = array_search($place, $all_places);
                if (empty($place_id)) {
                    $place_id = 0;
                }

                $date = _date2ISO(protegeImport($content[1], $to_encoding, $from_encoding));
                $time = _hour2Iso(protegeImport($content[2], $to_encoding, $from_encoding));
                $datetime = $date . ' ' . $time;

                $team1_id = 0;
                $team1 = protegeImport($content[5], $to_encoding, $from_encoding);
                if (! empty($team1)) {
                    $tmp = explode('-', $team1);
                    $team1_id = intval(trim($tmp[0]));
                }
                $team1_status = protegeImport($content[6], $to_encoding, $from_encoding);
                if (empty($team1_status)) {
                    $team1_status = 'C';
                }
                $team2_id = 0;
                $team2 = protegeImport($content[8], $to_encoding, $from_encoding);
                if (! empty($team2)) {
                    $tmp = explode('-', $team2);
                    $team2_id = intval(trim($tmp[0]));
                }
                $team2_status = protegeImport($content[9], $to_encoding, $from_encoding);
                if (empty($team2_status)) {
                    $team2_status = 'C';
                }
                $score = protegeImport($content[7], $to_encoding, $from_encoding);
                $score_team1 = null;
                $score_team2 = null;
                if (! empty($score)) {
                    $tmp = explode('-', $score);
                    $score_team1 = trim($tmp[0]);
                    $score_team2 = trim($tmp[1]);
                }

                // referee 1
                $referee1_club = protegeImport($content[11], $to_encoding, $from_encoding);
                $referee1_club_id = array_search($referee1_club, $all_clubs);
                if (empty($referee1_club_id)) {
                    $referee1_club_id = 0;
                }
                $referee1 = protegeImport($content[12], $to_encoding, $from_encoding);
                $referee1_id = array_search($referee1, $all_users);
                if (empty($referee1_id)) {
                    $referee1_id = 0;
                }
                if (empty($referee1) && empty($referee1_club)) {
                    $referee1_status = null;
                } else {
                    $referee1_status = protegeImport($content[13], $to_encoding, $from_encoding);
                    if (empty($referee1_status)) {
                        $referee1_status = 'C';
                    }
                }

                // referee 2
                $referee2_club = protegeImport($content[14], $to_encoding, $from_encoding);
                $referee2_club_id = array_search($referee2_club, $all_clubs);
                if (empty($referee2_club_id)) {
                    $referee2_club_id = 0;
                }
                $referee2 = protegeImport($content[15], $to_encoding, $from_encoding);
                $referee2_id = array_search($referee2, $all_users);
                if (empty($referee2_id)) {
                    $referee2_id = 0;
                }
                if (empty($referee2) && empty($referee2_club)) {
                    $referee2_status = null;
                } else {
                    $referee2_status = protegeImport($content[16], $to_encoding, $from_encoding);
                    if (empty($referee2_status)) {
                        $referee2_status = 'C';
                    }
                }

                //----------------------------------------------------------
                // set match data
                $match = array(
                    'match_cs_id'           => $cs_id,
                    'match_category_id'     => $category_id,
                    'match_datetime'        => $datetime,
                    'match_team1'           => $team1_id,
                    'match_team1_status'    => $team1_status,
                    'match_team1_score'     => $score_team1,
                    'match_team2'           => $team2_id,
                    'match_team2_status'    => $team2_status,
                    'match_team2_score'     => $score_team2,
                    'match_place_id'        => $place_id,
                    'match_referee1_club'   => $referee1_club_id,
                    'match_referee1'        => $referee1_id,
                    'match_referee1_status' => $referee1_status,
                    'match_referee2_club'   => $referee2_club_id,
                    'match_referee2'        => $referee2_id,
                    'match_referee2_status' => $referee2_status,
                    'match_scoresheet'      => protegeImport($content[17], $to_encoding, $from_encoding),
                    'match_complete'        => protegeImport($content[18], $to_encoding, $from_encoding),
                    'match_comments'        => protegeImport($content[19], $to_encoding, $from_encoding),
                );

                // insert/update match
                if ($match_id == 0) {
                    self::_newMatch($match);
                } else {
                    self::_updateMatch($match_id, $match);
                }
                $j++;
            }
            $i++;
        }
        return array('success' => true, 'message' => ($j - 1) . " lignes(s) ont été importées ou mises à jour.");
    }


    /**
     * set a match
     *
     * @param int $match_id
     * @param object $match
     *
     * @return int $match_id
     */
    public function setMatch($match_id, $match)
    {
        if (empty($match)) {
            return CRH_ERROR_DATA_EMPTY;
        }
        unset($match->match_date_format, $match->match_time_format);
        if ($match_id == 0) {
            $match->match_team1_status = 'C';
            $match->match_team1_score = 0;
            $match->match_team2_status = 'C';
            $match->match_team2_score = 0;
            $match->match_scoresheet = 0;
            $match->match_complete = 0;
            $match_id = self::_newMatch($match);
        } else {
            $match_id = self::_updateMatch($match_id, $match);
        }
        return $match_id;
    }


    /**
     * insert a new match
     *
     * @param array $data
     *
     * @return int $match_id, the new ID
     */
    public function _newMatch($data)
    {
        $this->db->insert('matches', $data);
        if ($this->db->_error_message()) {
            _criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
        }
        $match_id = $this->db->insert_id();
        return $match_id;
    }


    /**
     * update a match
     *
     * @param int $match_id, match ID
     * @param array $data, data to update
     *
     * @return int $match_id
     */
    public function _updateMatch($match_id, $data)
    {
        $this->db->where('match_id', $match_id);
        $this->db->update('matches', $data);
        if ($this->db->_error_message()) {
            _criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
        }
        return $match_id;
    }


    /**
     * update several matches
     *
     * @param array $matches (id => data)
     *
     * @return void
     */
    public function massUpdate($matches)
    {
        foreach ($matches as $match_id => $match) {
            self::_updateMatch($match_id, $match);
        }
    }


    /**
     * delete a match by id
     *
     * @param int $match_id
     *
     * @return boolean
     */
    public function delMatch($match_id)
    {
        $this->db->from('matches')
            ->where('match_id', $match_id)
            ->delete();
        if ($this->db->_error_message()) {
            _criticalError($this->db->last_query() . '<br>' . $this->db->_error_message());
        }
        return true;
    }
}

/* End of file matches_model.php */
/* Location: ./application/models/matches_model.php */
