<?php
/* $Id:$ */

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

if ($_REQUEST['action']=="admin_get_user_list") {

	//query to get the children for this project_id
	$q = db_query('SELECT * FROM employees WHERE Department_ID='.$_REQUEST['dept_id'].' ORDER BY LastName,FirstName');

	if (db_numrows($q) > 0) {
		for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
			$content .= "<option>".$row['LastName'].", ".$row['FirstName']."</option>\n";
		}
	}
	db_free_result($q);
	echo $content;
	die();
}

// numeric inputs
$input_array = array('employee_id','Department_ID','Level_ID','pm_SiteAdmin','mgmt','active');
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
$input_array = array('MedCtrLogin','LastName','FirstName','EMail','JobTitle','Phone');
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

// long text inputs
$input_array = array('Notes');
foreach($input_array as $var) {
	if (isset($_POST[$var]) and !empty($_POST[$var])) {
		if (!@safe_data_long($_POST[$var])) {
			error('Task submit', 'Variable '.$var.' is not set correctly');
		}
		${$var} = $_POST[$var];
	} else {
		${$var} = "";
	}
}

switch ($_REQUEST['action']) {

	case 'user_submit_insert':
		//start transaction
		db_begin();
		$q = db_query("INSERT INTO employees (MedCtrLogin,LastName,FirstName,EMail,JobTitle,Phone,Department_ID,Level_ID,pm_SiteAdmin,mgmt,active,Notes)
		              values('$MedCtrLogin','$LastName','$FirstName','$EMail','$JobTitle','$Phone','$Department_ID','$Level_ID','$pm_SiteAdmin','$mgmt','$active','$Notes')");

		// get taskid for the new task/project
		$employee_id = db_lastoid('employee_id_seq');

		//transaction complete
		db_commit();
		// this is passed back to origination page if needed
		echo $employee_id;
		break;

	case 'user_submit_update':
		//special case: project_id cannot be zero
		if ($employee_id == 0) {
			error('Projects submit', 'Variable project_id is not correctly set');
		}

		//begin transaction
		db_begin();

		//change the info
		db_query('UPDATE employees
			SET MedCtrLogin=\''.$MedCtrLogin.'\',
			LastName=\''.$LastName.'\',
			FirstName=\''.$FirstName.'\',
			EMail=\''.$EMail.'\',
			JobTitle=\''.$JobTitle.'\',
			Phone=\''.$Phone.'\',
			Department_ID=\''.$Department_ID.'\',
			Level_ID='.$Level_ID.',
			pm_SiteAdmin='.$pm_SiteAdmin.',
			mgmt='.$mgmt.',
			active='.$active.',
			Notes=\''.$Notes.'\'
			WHERE employee_ID='.$employee_id);

		//transaction complete
		db_commit();
		break;

		//error case
	default:
		error('Admin action handler', 'Invalid request given');
		break;
}

?>