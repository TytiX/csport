<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0640);
define('FILE_WRITE_MODE', 0660);
define('DIR_READ_MODE', 0750);
define('DIR_WRITE_MODE', 0770);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/*
|--------------------------------------------------------------------------
| constants for the application
|--------------------------------------------------------------------------
|
| always prefixed by CRH_
|
*/

// path to working directory
define('CRH_LOCAL_PATH', getcwd() . '/');
// testing : 1 / online : 0
define('CRH_DEBUG', 1);
// path to css
define('CRH_PATH_TO_CSS', 'public/css/');
// path to images
define('CRH_PATH_TO_IMG', 'public/images/');
// path to js
define('CRH_PATH_TO_JS', 'public/js/');
// path to generated files (CSV)
define('CRH_PATH_TO_FILES', 'public/files/');
//path to logs
define('CRH_PATH_TO_LOGS', 'public/logs/');
// number of records to show (pagination)
define('CRH_NB_RECORD', 20);
// datetime formats for MYSQL
define('CRH_SQL_DATE_FORMAT', '%d/%m/%Y');
define('CRH_SQL_DATETIME_FORMAT', '%d/%m/%Y %T');
define('CRH_SQL_TIME_FORMAT', '%Hh%i');
// VERSION
define('CRH_VERSION', '1.0');

// various error messages
define('CRH_ERROR_DATA_EMPTY', 'empty');
define('CRH_ERROR_LOGIN_UNAVAILABLE', 'unavailable');

// email config
define('CRH_FROM_NAME', '');
define('CRH_FROM_EMAIL', '');

// independant club
// define here the ID of an club for independant referees
define('INDEP_CLUB', 0);

/*
|--------------------------------------------------------------------------
| messages types
|--------------------------------------------------------------------------
|
| TYPE_MSG_ERROR	=>	type for error messages
| TYPE_MSG_WARNING	=>	type for warning messages
| TYPE_MSG_SUCCESS	=>	type for success messages
| CRH_TYPE_MSG_PERM_SUCCESS	=>	type for permanent success messages
| TYPE_MSG_INFO		=>	type for info messages
|
*/
define('CRH_TYPE_MSG_ERROR',   'danger');
define('CRH_TYPE_MSG_WARNING', 'warning');
define('CRH_TYPE_MSG_SUCCESS', 'success');
define('CRH_TYPE_MSG_PERM_SUCCESS', 'perm_success');
define('CRH_TYPE_MSG_INFO',    'info');

/* End of file constants.php */
/* Location: ./application/config/constants.php */
