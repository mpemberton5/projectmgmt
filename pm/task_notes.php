<?php
/* $Id$ */

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/screen.php');

//
// The action handler
//
if (!isset($_REQUEST['action'])) {
	error('Message action handler', 'No request given');
}

// Task Activity Action
switch($_REQUEST['action']) {

	//add a message
	case 'popupAdd':
	case 'popupEdit':
		create_complete_top('Edit Note', 4, 0, 'note', 1);
		include(BASE.'task_notes/note_edit.php');
		create_bottom();
		break;

		//submit to message engine
	case 'submit_insert':
	case 'submit_update':
	case 'submit_delete':
		include(BASE.'task_notes/note_submit.php');
		break;

		//Error case
	default:
		error('Message action handler', 'Invalid request given');
		break;
}

?>