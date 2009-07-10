<?php
/* $Id$ */

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/screen.php');

//
// The action handler
//
if (!isset($_REQUEST['action'])) {
	error('Task action handler', 'No action given');
}

// Project Action
switch($_REQUEST['action']) {

	//Main Project List
	case 'list':
		create_complete_top('Home');
		include(BASE.'projects/projects_list.php');
		create_bottom();
		break;

	case 'show':
		create_complete_top('Project Details');
		include(BASE.'projects/projects_show.php');
		create_bottom();
		break;

	case 'submit_insert':
	case 'submit_delete':
	case 'submit_quick_insert':
	case 'submit_update':
	case 'submit_watch':
		include(BASE.'projects/projects_submit.php');
		break;

		//Project Popup Edit
	case 'popupAdd':
	case 'popupEdit':
		create_complete_top('Edit Project', 4, 0, 'name', 1);
		include(BASE.'projects/projects_edit.php');
		create_bottom();
		break;

	case 'popupQuickAdd':
		create_complete_top('Quick Add Project', 4, 0, 'name', 1);
		include(BASE.'projects/projects_quick_add.php');
		create_bottom();
		break;

	case 'popupTemplate':
		create_complete_top('Project Templates', 4, 0, 'name', 1);
		include(BASE.'projects/project_templates.php');
		create_bottom();
		break;

	case 'template_details':
		include(BASE.'projects/projects_getdata.php');
		break;

	case 'popupProjectPrint':
		include(BASE.'includes/print.php');
		break;

//	case 'getMonitoredProjects':
//		include(BASE.'projects/projects_getMonitoredProjects.php');
//		break;

		//Error case
	default:
		error('Project action handler', 'Invalid request');
		break;
}

?>