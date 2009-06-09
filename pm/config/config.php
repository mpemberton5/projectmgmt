<?php

//-- Title and Location parameters --

  define('BASE_SUB', '/pm/');

  //You need to add the full webserver name and directory to CAPP here. For example:
  //'http://www.your-url-here.com/backend/org/' (do not forget the tailing slash)
  define('BASE_URL', 'http://localhost'.BASE_SUB);

  //The name of the site
  define('MANAGER_NAME', 'WFUBMC Project Management');

  //The abbreviated name for the site (for use in email subject lines)
  define('ABBR_MANAGER_NAME', 'WFUBMC PM');

//-- Database parameters --

  define('DATABASE_NAME', 'ProjectMgmt');
  define('DATABASE_USER', 'pm');
  define('DATABASE_PASSWORD', 'pm123');

  //Database type (valid options are 'mysql', 'postgresql' and 'mysql_innodb')
  define('DATABASE_TYPE', 'mysql');

  //Database host (usually 'localhost')
  define('DATABASE_HOST', '192.168.15.111');

    /*Note:
      For PostgreSQL DATABASE_HOST should not be changed from localhost.
      To use remote tcp/ip connections with PostgreSQL:
       - Edit pg_hba.conf (PostgreSQL config file) to allow tcp/ip connections
       - Start PostgreSQL postmaster with -i option
       - Change DATABASE_HOST as required
    */

//-- File upload parameters --

  //upload to what directory ?
  define('FILE_BASE', 'c:/wamp/www/pm/files/filebase');
  //define('FILE_BASE', '/home/www/pm/files/filebase');

	//current file folder default
  define('FOLDER_DEFAULT', 75);

  //max file size in bytes
  define('FILE_MAXSIZE', '10000000');

    /*Note:
      1. Make sure the file_base directory exists, and is writeable by the webserver, or you
         will not be able to upload any files.
      2. The filebase directory should be outside your webserver root directory to maintain file
         security.  This is important to prevent users navigating to the file directory with
         their web browsers, and viewing all the files.  (The default location given is NOT outside
         the webserver root, but it makes first-time setup easier).
    */

//-- Timezone --

  //timezone offset from GMT/UTC (hours)
  define('TZ', '-5' );

//-- Email --

  //enable email to send messages? (Values are 'Y' or 'N')
  define('USE_EMAIL', 'Y' );

  //location of SMTP server (IP address or FQDN)
  define('SMTP_HOST', 'localhost' );

  //mail transport (leave as SMTP for standard WebCollab)
  define('MAIL_TRANSPORT', 'SMTP' );
  //SMTP port (leave as 25 for ordinary mailservers)
  define('SMTP_PORT', 25 );

  //use smtp auth? ('Y' or 'N')
  define('SMTP_AUTH', 'N' );
  //if using SMTP_AUTH give username & password
  define('MAIL_USER', '' );
  define('MAIL_PASSWORD', '' );
  //use TLS encryption? (requires PHP 5.1+)
  define('TLS', 'N' );

//------------------------------------------------------------------------------
// Less important items below this line

//-- MINOR CONFIG PARAMETERS --

//-- These items need to be edited directly from this file --

  //Style sheets (CSS) Note: Setup always uses 'default.css' stylesheet for CSS_MAIN. (Place your CSS into /css directory)
	//default_corporate.css
	//default_danskmambo.css
	//default_losersjuegos.css
	//default_polish.css
	//default.css
	//default_gray.css
	//default_pastel.css

  define('CSS_MAIN', 'tablesort.css' );
  define('CSS_CALENDAR', 'calendar.css' );
  define('CSS_PRINT', 'print.css' );

  //If an error occurs, who do you want the error to be mailed to ?
  define('EMAIL_ERROR', '' );

  //session timeout in hours
  define('SESSION_TIMEOUT', 1 );

  //number of days that new or updated tasks should be highlighted as 'New' or 'Updated'
  define('NEW_TIME', 14 );

  //custom image to replace the banner on splash page (base directory is /images)
  define('SITE_IMG', 'wfubmc_logo.gif' );

  //show full debugging messages on the screen when errors occur (values are 'N', or 'Y')
  define('DEBUG', 'N' );

  //ini_set("error_reporting","E_ALL|E_NOTICE|E_STRICT");

  //Do not show full error message on the screen - just a 'sorry, try again' message (values are 'N', or 'Y')
  define('NO_ERROR', 'N' );

  //Use external webserver authorization to login (values are 'N', or 'Y')
  define('WEB_AUTH', 'N' );

  //PM version string
  define('PM_VERSION', '1.00');

  define('UNICODE_VERSION', 'N' );

?>
