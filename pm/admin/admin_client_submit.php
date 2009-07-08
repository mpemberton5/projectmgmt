<?php
/* $Id$ */

require_once('path.php');
require_once(BASE.'includes/security.php' );
include_once(BASE.'database/database.php' );

//
// The action handler
//
if (!isset($_REQUEST['action'])) {
	error('Admin action handler', 'No request given' );
}

$content = "";

// numeric inputs
$input_array = array('client_id');
foreach($input_array as $var) {
	if (isset($_POST[$var]) and !empty($_POST[$var])) {
		if (!@safe_integer($_POST[$var])) {
			error('Task submit', 'Variable '.$var.' is not set correctly ['.$_POST[$var].']');
		}
		${$var} = $_POST[$var];
	} else {
		${$var} = 0;
	}
}

// text inputs
$input_array = array('client_full_name');
foreach($input_array as $var) {
	if (isset($_POST[$var]) and !empty($_POST[$var])) {
		if (!@safe_data($_POST[$var])) {
			error('Task submit', 'Variable '.$var.' is not set correctly');
		}
		${$var} = $_POST[$var];
	} else {
		${$var} = "";
	}
}

switch ($_REQUEST['action']) {

	case 'client_submit_insert':
		//start transaction
		db_begin();
		$q = db_query("INSERT INTO clients (client_full_name) values ('".$client_full_name."')");

		// get taskid for the new task/project
		$client_id = db_lastoid('client_ID');

		//transaction complete
		db_commit();
		// this is passed back to origination page if needed
		echo $client_id;
		break;

	case 'client_submit_update':
		//special case: project_id cannot be zero
		if ($client_id == 0) {
			error('Client submit', 'Variable $client_id is not correctly set');
		}

		//begin transaction
		db_begin();

		//change the info
		db_query('UPDATE clients SET client_full_name="'.$client_full_name.'" WHERE client_ID='.$client_id);

		//transaction complete
		db_commit();
		break;

		//error case
	default:
		error('Admin action handler', 'Invalid request given');
		break;
}

?>