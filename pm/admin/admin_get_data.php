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

switch ($_REQUEST['action']) {

	case 'admin_get_user_list':
		//query to get the children for this project_id
		$q = db_query('SELECT * FROM employees WHERE Department_ID='.$_REQUEST['dept_id'].' ORDER BY LastName,FirstName');

		if (db_numrows($q) > 0) {
			for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
				$content .= "<option>".$row['LastName'].", ".$row['FirstName']."</option>\n";
			}
		}
		db_free_result($q);
		echo $content;
		break;

		//error case
	default:
		error('Admin action handler', 'Invalid request given');
		break;
}

?>