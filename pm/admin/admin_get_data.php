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

switch ($_REQUEST['action']) {

	case 'admin_get_user_list':
//	//query to get the children for this project_id
//	$q = db_query('SELECT * FROM employees WHERE project_id='.$project_id.' AND task_id='.$task_id.' LIMIT 1');
//
//	//check for any posts
//	if (db_numrows($q) < 1) {
//		error("Task Edit", "Unable to Find Task Details");
//	}
//
//	//get the data
//	if (!($row = db_fetch_array($q, 0))) {
//		error('Task edit', 'The requested item has either been deleted, or is now invalid.');
//	}
		return "123";
		break;

		//error case
	default:
		error('Admin action handler', 'Invalid request given');
		break;
}

?>