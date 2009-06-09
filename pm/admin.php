<?php
/* $Id: admin.php,v 1.2 2009/06/08 21:13:04 markp Exp $ */

require_once('path.php');
require_once(BASE.'includes/security.php' );
include_once(BASE.'includes/screen.php' );

//
// The action handler
//
if (!isset($_REQUEST['action'])) {
	error('Admin action handler', 'No request given' );
}

switch ($_REQUEST['action']) {

	case 'admin':
		create_complete_top('Administration');
		include(BASE.'admin/admin_main.php');
		create_bottom();
		break;

	case 'users':
		create_complete_top('Administration');
		include(BASE.'admin/admin_main.php');
		include(BASE.'admin/admin_user_list.php');
		create_bottom();
		break;

	case 'user_depts':
		create_complete_top('Administration');
		include(BASE.'admin/admin_main.php');
		include(BASE.'admin/admin_user_depts.php');
		create_bottom();
		break;

	case 'user_level':
		create_complete_top('Administration');
		include(BASE.'admin/admin_main.php');
		include(BASE.'admin/admin_user_level.php');
		create_bottom();
		break;

	case 'admin_get_user_list':
		include(BASE.'admin/admin_get_data.php');
		break;

		//error case
	default:
		error('Admin action handler', 'Invalid request given');
		break;
}

?>