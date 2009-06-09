<?php
/* $Id: charts_submit.php,v 1.2 2009/06/08 21:13:04 markp Exp $ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

//includes
require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/time.php');


// numeric inputs
$input_array = array('user_id');
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

// chart columns
$input_array = array('col0','col1','col2');
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

switch($_REQUEST['action']) {

	case 'savePos':
		// check if user currently has saved positions
		if (db_result(db_query('SELECT COUNT(*) FROM user_prefs WHERE user_ID='.$user_id.' AND pref_type="mgmtDesktop" LIMIT 1'), 0, 0) < 1) {
			// INSERT NEW ROW
			$SQL = "INSERT INTO user_prefs (user_ID,pref_type,value1,value2,value3) values ('$user_id','mgmtDesktop','$col0','$col1','$col2')";
		} else {
			// UPDATE EXISTING ROW
			$SQL = "UPDATE user_prefs SET value1='$col0', value2='$col1', value3='$col2' WHERE user_ID='$user_id' AND pref_type='mgmtDesktop'";
		}
		//start transaction
		db_begin();
		$q = db_query($SQL);

		// get taskid for the new task/project
		//$project_id = db_lastoid('project_id_seq');

		//transaction complete
		db_commit();
		// this is passed back to origination page if needed
		break;

	default:
		error('Task Submit','Invalid Request');
		break;
}

?>