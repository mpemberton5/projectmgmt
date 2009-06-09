<?php
/* $Id: files.php,v 1.3 2009/06/05 18:16:39 markp Exp $ */

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/screen.php');

//
// The action handler
//
if (!isset($_REQUEST['action'])) {
	error('Files action handler', 'No request given');
}

// Files Action
switch($_REQUEST['action']) {

	case 'popupAdd':
		create_complete_top('Upload File', 4, 0, 'name', 1);
		include(BASE.'files/file_upload.php');
		create_bottom();
		break;

		//create a box with the current files
	case 'list':
		include(BASE.'files/file_list.php');
		break;

		//download a file
	case 'download':
		include(BASE.'files/file_download.php');
		break;

	case 'submit_del':
	case 'submit_upload':
	case 'submit_create':
	case 'submit_update':
		include(BASE.'files/file_submit.php');
		break;

//		//details a file
//	case 'details':
//		create_complete_top('Update File Details', 0, 'summary', 'summary');
//		include(BASE.'includes/mainmenu.php');
//		goto_main();
//		include(BASE.'files/file_details.php');
//		create_bottom();
//		break;
//
//		//view content
//	case 'view_content':
//		create_complete_top('View Document Content', 0, 'summary', 'summary');
//		include(BASE.'includes/mainmenu.php');
//		goto_main();
//		include(BASE.'files/file_view.php');
//		create_bottom();
//		break;
//
//		//create new content
//	case 'create':
//		create_complete_top('Create New Document:', 0, 'summary', 'summary');
//		include(BASE.'includes/mainmenu.php');
//		goto_main();
//		include(BASE.'files/file_create.php');
//		create_bottom();
//		break;
//
//		//upload a file
//	case 'upload':
//		create_complete_top('File to Upload:', 0, 'userfile', 'userfile');
//		include(BASE.'includes/mainmenu.php');
//		goto_main();
//		include(BASE.'files/file_upload.php');
//		create_bottom();
//		break;

//		//admin files
//	case 'admin':
//		create_complete_top('File Admin');
//		include(BASE.'includes/mainmenu.php');
//		include(BASE.'files/file_menubox.php');
//		goto_main();
//		include(BASE.'files/file_admin.php');
//		create_bottom();
//		break;

		//Error case
	default:
		error('File action handler', 'Invalid request given');
		break;
}

?>