<?php
/* $Id$ */

require_once('path.php');

//set some base variables
$database_connection = '';
$delim = '';
$epoch = 'UNIX_TIMESTAMP(';
$day_part = 'DAYOFMONTH(';

/**
 * connect to database
 *
 * @return unknown_type
 */
function db_connection() {

	global $database_connection;

	//make connection
	if (!($database_connection = @mysql_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD))) {
		error('No database connection',  'Sorry but there seems to be a problem in connecting to the database server');
	}

	//select database
	if (!@mysql_select_db(DATABASE_NAME, $database_connection)) {
		error('Database error', 'No connection to database tables');
	}

	//set transaction mode for innodb
	if (DATABASE_TYPE == 'mysql_innodb') {
		db_query('SET AUTOCOMMIT = 1');
	}

	//set timezone
	if (! mysql_query("SET time_zone='".sprintf('%+02d:%02d', TZ, (TZ - floor(TZ) )*60 )."'", $database_connection)) {
		error("Database error", "Not able to set timezone");
	}

	return;
}

/**
 * Provides a safe way to do a query
 *
 * @param $query
 * @param $dieonerror
 * @return unknown_type
 */
function db_query($query, $dieonerror=1) {

	global $database_connection, $db_error_message ;

	if (!$database_connection) db_connection();

	//do it
	if (!($result = @mysql_query($query, $database_connection))) {
		$db_error_message = mysql_error($database_connection);
		if ($dieonerror == 1) {
			error('Database query error', 'The following query :<br /><br /><b>'.$query.'</b><br /><br />Had the following error:<br /><b>'.mysql_error($database_connection).'</b>');
		}
	}

	//all was okay return resultset
	return $result;
}

/**
 * @example $firstname = simplequery("usertable", "firstname", "ID", $UserID);
 *
 * @param Table Name
 * @param Return Field
 * @param Search Field
 * @param Identifier to Search For
 * @return Return Field or blank
 * @example test
 */
function db_simplequery($table, $field, $haystack, $needle) {
	global $database_connection, $db_error_message ;

	if (!$database_connection) db_connection();

	$result = mysql_query("SELECT $field FROM $table WHERE $haystack = $needle LIMIT 1;", $database_connection);

	if ($result) {
		if (mysql_num_rows($result)) {
			$row = mysql_fetch_assoc($result);
			$retField = $row[$field];
			db_free_result($result);
			return $retField;
		}
	} else {
		return "";
	}
}

/**
 * escapes special characters in a string for use in a SQL statement
 *
 * @param $string
 * @return unknown_type
 */
function db_escape_string($string) {

	global $database_connection;

	if (!$database_connection) {
		db_connection();
	}

	$result = mysql_real_escape_string($string, $database_connection);

	return $result;
}

/**
 * number of rows in result
 *
 * @param $q
 * @return unknown_type
 */
function db_numrows($q) {

	$result = mysql_num_rows($q);

	return $result;
}

/**
 * get single result set
 *
 * @param $q
 * @param $row
 * @param $field
 * @return unknown_type
 */
function db_result($q, $row=0, $field=0) {

	if (db_numrows($q) > 0) {
		$result = mysql_result($q, $row, $field);
		return $result;
	} else {
		return;
	}
}

/**
 * fetch array result set
 *
 * @param $q
 * @param $row
 * @return unknown_type
 */
function db_fetch_array($q, $row=0) {

	$result_row = mysql_fetch_array($q, MYSQL_ASSOC);

	return $result_row;
}

/**
 * fetch enumerated array result set
 *
 * @param $q
 * @param $row
 * @return unknown_type
 */
function db_fetch_num($q, $row=0) {

	$result_row = mysql_fetch_row($q);

	return $result_row;
}

/**
 * fetch last oid
 *
 * @param $seq
 * @return unknown_type
 */
function db_lastoid($seq) {

	global $database_connection;

	$lastoid = mysql_insert_id($database_connection);

	if ($lastoid == 0) {
		error('Database lastoid error', 'The lastoid is zero.<br />MYSQL Error:<br /><b>'.mysql_error($database_connection).'</b>');
	}
	return $lastoid;
}

/**
 * return data pointer to begining of data set
 *
 * @param $q
 * @return unknown_type
 */
function db_data_seek($q) {

	if (mysql_num_rows($q) == 0)
	return TRUE;

	$result = mysql_data_seek($q, 0);

	return $result;
}

/**
 * free memory
 *
 * @param $q
 * @return unknown_type
 */
function db_free_result($q) {

	$result = mysql_free_result($q);

	return $result;
}

/**
 * begin transaction
 *
 * @return unknown_type
 */
function db_begin() {

	global $database_connection;

	//not used for ISAM tables
	if (DATABASE_TYPE == 'mysql') return true;

	$result = mysql_query('BEGIN');

	return $result;
}

/**
 * rollback transaction
 *
 * @return unknown_type
 */
function db_rollback() {

	global $database_connection;

	//not used for ISAM tables
	if (DATABASE_TYPE == 'mysql') return true;

	$result = mysql_query('ROLLBACK');

	return $result;
}

/**
 * commit transaction
 *
 * @return unknown_type
 */
function db_commit() {

	global $database_connection;

	//not used for ISAM tables
	if (DATABASE_TYPE == 'mysql') return true;

	$result = mysql_query('COMMIT');

	return $result;
}

/**
 * sets the required session client encoding
 *
 * @param $encoding
 * @return unknown_type
 */
function db_user_locale($encoding) {

	global $database_connection;

	if (!$database_connection) db_connection();

	switch(strtoupper($encoding)) {

		case 'ISO-8859-1':
			$my_encoding = 'latin1';
			break;

		case 'UTF-8':
			$my_encoding = 'utf8';
			break;

		case 'ISO-8859-2':
			$my_encoding = 'latin2';
			break;

		case 'ISO-8859-7':
			$my_encoding = 'greek';
			break;

		case 'ISO-8859-9':
			//ISO-8859-9 === latin5 in MySQL!!
			$my_encoding = 'latin5';
			break;

		case 'KOI8-R':
			$my_encoding = 'koi8r';
			break;

		case 'WINDOWS-1251':
			$my_encoding = 'cp1251';
			break;

		default:
			$my_encoding = 'latin1';
			break;
	}

	//set character set -- 1
	if (!mysql_query("SET NAMES '".$my_encoding."'", $database_connection)) {
		error("Database error", "Not able to set ".$my_encoding." client encoding");
	}

	//set character set -- 2
	if (!mysql_query("SET CHARACTER SET ".$my_encoding, $database_connection)) {
		error("Database error", "Not able to set CHARACTER SET : ".$my_encoding);
	}

	return true;
}

?>