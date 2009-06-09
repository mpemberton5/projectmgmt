<?php
/* $Id: task_notes.php,v 1.4 2009/06/05 18:16:39 markp Exp $ */

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
		include(BASE.'task_notes/note_submit.php');
		break;

		//Error case
	default:
		error('Message action handler', 'Invalid request given');
		break;
}

?>