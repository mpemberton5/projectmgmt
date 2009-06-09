<?php
/* $Id: database.php,v 1.1 2009/04/22 00:05:07 markp Exp $ */

if (!defined('DATABASE_TYPE')) {
  die('Config file not loaded properly for database');
}

switch(DATABASE_TYPE) {

  case 'mysql':
  case 'mysql_innodb':
    include(BASE.'database/mysql_database.php');
    break;

  case 'postgresql':
    include(BASE.'database/pgsql_database.php');
    break;

  case 'mysqli':
    include(BASE.'database/mysqli_database.php');
    break;

  default:
    die('No database type specified in configuration file');
    break;
}

?>
