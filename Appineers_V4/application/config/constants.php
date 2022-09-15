<?php
defined('BASEPATH') || exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') || define('SHOW_DEBUG_BACKTRACE', TRUE);

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
defined('FILE_READ_MODE')  || define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') || define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   || define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  || define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           || define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     || define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       || define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  || define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   || define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              || define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            || define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       || define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
defined('SG_APIKEY') 		   || define('SG_APIKEY','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');// SENDGRID API KEY		
defined('STRIPE_ACCOUNT') 	   || define('STRIPE_ACCOUNT','0'); // provide stripe account is present or not 		

/// api constants 

define('OK', '200');
define('CREATED', '201');
define('INTERNAL_SERVER_ERROR', '500');
define('NO_CONTENT', '204');
define('UNPROCESSABLE_ENTITY', '422');
define('RESPONSE_UNPROCESSABLE_ENTITY','422');


define('BAD_REQUEST', '400');
define('CONFLICT', '409');
define('RESOURSE_IS_FORBIDDEN', '403');
define('UNAUTHORIZED', '401');
define('RESOURSE_WAS_NOT_FOUND', '404');
define('SESSION_EXPIRE', '401');

//response code
define('SUCCESS_CODE', '1');
define('SUCCESS_CODE_TWO', '2');
define('FAILED_CODE', '0');
define('INVALID_CREDENTIAL', '3');


//S3 bucket folder constant 
define('USER_PROFILE', 'user_profile');




//user signup email api validation constant
define('FIRST_NAME_MIN_LENGTH', '1');
define('FIRST_NAME_MAX_LENGTH', '80');

define('LAST_NAME_MIN_LENGTH', '1');
define('LAST_NAME_MAX_LENGTH', '80');

define('USER_NAME_MIN_LENGTH', '1');
define('USER_NAME_MAX_LENGTH', '20');

define('MOBILE_NO_MIN_LENGTH', '10');
define('MOBILE_NO_MAX_LENGTH', '13');

define('PASSWORD_MIN_LENGTH', '6');
define('PASSWORD_MAX_LENGTH', '15');

define('ZIPCODE_MIN_LENGTH', '5');
define('ZIPCODE_MAX_LENGTH', '10');