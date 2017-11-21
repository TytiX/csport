<?php  if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|   example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|   http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|   $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|   $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "matches/matchList/simple/list";
$route['404_override'] = '';


/*
| -------------------------------------------------------------------------
| MY URI ROUTING
| -------------------------------------------------------------------------
*/

// access
$route['connexion'] = 'access/login';
$route['deconnexion/(:any)'] = 'access/logout/$1';
$route['acces-refuse'] = 'access/access_denied';

// imports
$route['importer-matches']      = "import_export/import_matches";
$route['importer-utilisateurs'] = "import_export/import_users";
$route['importer-equipes']      = "import_export/import_teams";
// exports
$route['exporter-equipes']      = "import_export/export_teams";
$route['exporter-utilisateurs'] = "import_export/export_users";
$route['exporter-matches']      = "import_export/export_matches_public/1";
$route['matches/exporter']      = "import_export/export_matches_public/0";
$route['planning/exporter']     = "import_export/export_matches_planning";
$route['club/exporter']         = "import_export/export_matches_private";
$route['equipes/exporter']      = "import_export/export_matches_private";
$route['planning-arbitrage/exporter'] = "import_export/export_refereeing_planning";

// matches (public list)
$route['matches/t(:num)']   = "matches/teamMatchList/simple/list/$1";
$route['matches/liste-simple']           = "matches/matchList/simple/list";
$route['matches/liste-simple/p(:num)']   = "matches/matchList/simple/list/$1";
$route['matches/liste-complete']         = "matches/matchList/complete/list";
$route['matches/liste-complete/p(:num)'] = "matches/matchList/complete/list/$1";
$route['matches/recherche-simple']           = "matches/matchList/simple/search";
$route['matches/recherche-simple/p(:num)']   = "matches/matchList/simple/search/$1";
$route['matches/recherche-complete']         = "matches/matchList/complete/search";
$route['matches/recherche-complete/p(:num)'] = "matches/matchList/complete/search/$1";
$route['matches/tri-simple/([a-z12]+)-([a-z]+)']           = "matches/matchList/simple/order/1/$1/$2";
$route['matches/tri-simple/p(:num)/([a-z12]+)-([a-z]+)']   = "matches/matchList/simple/order/$1/$2/$3";
$route['matches/tri-complete/([a-z12]+)-([a-z]+)']         = "matches/matchList/complete/order/1/$1/$2";
$route['matches/tri-complete/p(:num)/([a-z12]+)-([a-z]+)'] = "matches/matchList/complete/order/$1/$2/$3";
// matches (management)
$route['planning/gestion-matches']         = "matches/managePlanningMatches/list";
$route['planning/gestion-matches/p(:num)'] = "matches/managePlanningMatches/list/$1";
$route['planning/recherche-matches']         = "matches/managePlanningMatches/search";
$route['planning/recherche-matches/p(:num)'] = "matches/managePlanningMatches/search/$1";
$route['planning/tri-matches/([a-z12]+)-([a-z]+)']         = "matches/managePlanningMatches/order/1/$1/$2";
$route['planning/tri-matches/p(:num)/([a-z12]+)-([a-z]+)'] = "matches/managePlanningMatches/order/$1/$2/$3";
$route['planning/enregistrer-matches'] = "matches/validatePlanningMatches";
// delete match
$route['planning/supprimer-match-(:num)'] = "matches/del/$1";
// match form
$route['nouveau-match'] = 'matches/form/0';
$route['enregistrer-match'] = 'matches/validate';
// match team planning
$route['equipes/gestion-matches']           = 'matches/manageTeamMatches/list';
$route['equipes/gestion-matches/p(:num)']   = "matches/manageTeamMatches/list/$1";
$route['equipes/recherche-matches']         = "matches/manageTeamMatches/search";
$route['equipes/recherche-matches/p(:num)'] = "matches/manageTeamMatches/search/$1";
$route['equipes/tri-matches/([a-z12]+)-([a-z]+)']         = "matches/manageTeamMatches/order/1/$1/$2";
$route['equipes/tri-matches/p(:num)/([a-z12]+)-([a-z]+)'] = "matches/manageTeamMatches/order/$1/$2/$3";
$route['equipes/enregistrer-matches'] = "matches/validateTeamMatches";
// match club planning
$route['club/gestion-matches']           = 'matches/manageClubMatches/list';
$route['club/gestion-matches/p(:num)']   = "matches/manageClubMatches/list/$1";
$route['club/recherche-matches']         = "matches/manageClubMatches/search";
$route['club/recherche-matches/p(:num)'] = "matches/manageClubMatches/search/$1";
$route['club/tri-matches/([a-z12]+)-([a-z]+)']         = "matches/manageClubMatches/order/1/$1/$2";
$route['club/tri-matches/p(:num)/([a-z12]+)-([a-z]+)'] = "matches/manageClubMatches/order/$1/$2/$3";
$route['club/enregistrer-matches'] = "matches/validateClubMatches";


// refereeing general manager
$route['planning-arbitrage/liste']             = "referees/managePlanning/list";
$route['planning-arbitrage/liste/p(:num)']     = "referees/managePlanning/list/$1";
$route['planning-arbitrage/recherche']         = "referees/managePlanning/search";
$route['planning-arbitrage/recherche/p(:num)'] = "referees/managePlanning/search/$1";
$route['planning-arbitrage/tri/([a-z12]+)-([a-z]+)']         = "referees/managePlanning/order/1/$1/$2";
$route['planning-arbitrage/tri/p(:num)/([a-z12]+)-([a-z]+)'] = "referees/managePlanning/order/$1/$2/$3";
$route['planning-arbitrage/enregistrer'] = "referees/validatePlanning";
// refereeing club manager
$route['arbitrage-club/liste']             = "referees/manageClub/list";
$route['arbitrage-club/liste/p(:num)']     = "referees/manageClub/list/$1";
$route['arbitrage-club/recherche']         = "referees/manageClub/search";
$route['arbitrage-club/recherche/p(:num)'] = "referees/manageClub/search/$1";
$route['arbitrage-club/tri/([a-z12]+)-([a-z]+)']         = "referees/manageClub/order/1/$1/$2";
$route['arbitrage-club/tri/p(:num)/([a-z12]+)-([a-z]+)'] = "referees/manageClub/order/$1/$2/$3";
$route['arbitrage-club/enregistrer'] = "referees/validatePlanning";
// referee
$route['arbitre/liste']             = "referees/manageMatch/list";
$route['arbitre/liste/p(:num)']     = "referees/manageMatch/list/$1";
$route['arbitre/recherche']         = "referees/manageMatch/search";
$route['arbitre/recherche/p(:num)'] = "referees/manageMatch/search/$1";
$route['arbitre/tri/([a-z12]+)-([a-z]+)']         = "referees/manageMatch/order/1/$1/$2";
$route['arbitre/tri/p(:num)/([a-z12]+)-([a-z]+)'] = "referees/manageMatch/order/$1/$2/$3";
$route['arbitre/enregistrer'] = "referees/validateMatch";


// users
$route['utilisateurs/liste']         = "users/userList/list";
$route['utilisateurs/liste/p(:num)'] = "users/userList/list/$1";
$route['utilisateurs/recherche']         = "users/userList/search";
$route['utilisateurs/recherche/p(:num)'] = "users/userList/search/$1";
$route['utilisateurs/tri/([a-z]+)-([a-z]+)']         = "users/userList/order/1/$1/$2";
$route['utilisateurs/tri/p(:num)/([a-z]+)-([a-z]+)'] = "users/userList/order/$1/$2/$3";
//
$route['utilisateurs/modifier-(:num)'] = "users/form/$1";
$route['utilisateurs/nouveau'] = "users/form/0";
$route['utilisateurs/enregistrer'] = 'users/validate';
$route['utilisateurs/fiche-(:num)'] = "users/detail/$1";
// send all passwords
$route['envoyer-mots-passe'] = "users/sendPasswords";
// send a user password
$route['envoyer-mot-passe-(:num)'] = "users/sendUserPassword";

// misc
$route['equipes/liste-par-club'] = "teams/getTeamsByClub";
$route['changer-mot-passe'] = "users/changePwd";


// notifications
$route['convocation-generale'] = 'notifications/notifyAll';
$route['convocation-match-(:num)'] = 'notifications/notifyMatch/$1';

// routines
$route['alerte-match-incomplet-(:num)'] = 'routines/checkMatchesIncompleted/$1';
$route['alerte-match-non-confirme-(:num)'] = 'routines/checkTeamRefereeNotConfirmed/$1';
$route['rappel-match'] = 'routines/matchReminder';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
