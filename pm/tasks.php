<?php
/* $Id: tasks.php,v 1.9 2009/06/05 18:16:39 markp Exp $ */

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/screen.php');

//
// The action handler
//
if (!isset($_REQUEST['action'])) {
	error('Task action handler', 'No action given');
}

// Tasks Action
switch($_REQUEST['action']) {

	//show a task
	case 'showTopLevel':
	case 'showMilestoneLevel':
	case 'showTaskLevel':
		//catch & redirect hack for invalid entry from ProjectJump
		if (isset($_REQUEST['task_id']) && ($_REQUEST['task_id'] == -1)) {
			header('Location: '.BASE_URL.'index.php');
			die;
		}
		include(BASE.'tasks/task_show.php');
		break;

		//organize tasks
	case 'organize':
		create_complete_top('Organize', 4, 0, 'name', 0);
		include(BASE.'tasks/task_organize.php');
		create_bottom();
		break;

		//edit a task
	case 'edit':
		create_complete_top('Edit Task', 4, 0, 'name', 1);
		include(BASE.'tasks/task_edit.php');
		create_bottom();
		break;

		//edit a task
	case 'popupEdit':
	case 'popupAdd':
		create_complete_top('Edit Task', 4, 0, 'name', 1);
		include(BASE.'tasks/task_edit.php');
		create_bottom();
		break;

		//update task
	case 'submit_insert':
	case 'submit_update':
	case 'submit_invite':
	case 'submit_task_list_order':
	case 'submit_weight':
		include(BASE.'tasks/task_submit.php');
		break;

		//Error case
	default:
		error('Task action handler', 'Invalid request');
		break;
}

?>