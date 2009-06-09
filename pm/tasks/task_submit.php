<?php
/* $Id: task_submit.php,v 1.16 2009/06/05 18:16:39 markp Exp $ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

//includes
require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/time.php');

if ($_REQUEST['action']=="submit_task_list_order") {
	if (isset($_POST['project_id'])) {
		$project_id = $_POST['project_id'];
		if (db_result(db_query('SELECT COUNT(*) FROM projects WHERE project_id='.$project_id.' LIMIT 1'), 0, 0) < 1) {
			error('Database integrity check', 'Input data does not match - no project for task');
		}
		$task = $_POST['task'];
		for ($i = 0; $i < count($task); $i++) {
			$SQL = "UPDATE tasks SET order_num=".$i." WHERE task_ID=".$task[$i];
			print $SQL . "\n";
			mysql_query($SQL) or die(mysql_error());
		}
	} else {
		error("error","no project_id");
	}
	die();
}

if ($_REQUEST['action']=="submit_weight") {
	if (isset($_POST['project_id'])) {
		$project_id = $_POST['project_id'];
		if (db_result(db_query('SELECT COUNT(*) FROM projects WHERE project_id='.$project_id.' LIMIT 1'), 0, 0) < 1) {
			error('Database integrity check', 'Input data does not match - no project for task');
		}
		$task_id = "";
		foreach($_POST as $name => $value) {
			if (substr($name,0,5)=="taskw") {
				$SQL = "UPDATE tasks SET weight=".$value." WHERE task_ID=".substr($name,6);
				$task_id=substr($name,6);
				print $SQL . "\n";
				mysql_query($SQL) or die(mysql_error());
			}
		}
		// update total weight in Milestone
		db_query('call spUpdateMilestoneTotalWeight('.$task_id.')');
	} else {
		error("error","no project_id");
	}
	die();
}

// numeric inputs
$input_array = array('task_id','project_id','parent_task_id','assigned_to','percentcomplete');
foreach($input_array as $var) {
	if (isset($_POST[$var])) {
		if (!@safe_integer($_POST[$var])) {
			error('Task submit', 'Variable '.$var.' is not set correctly');
		}
		${$var} = $_POST[$var];
	} else {
		${$var} = 0;
	}
}

// text inputs
$input_array = array('name','status','priority','startdate','enddate','weight');
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
$input_array = array('text');
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

//check task name is present
if (empty($name)) {
	error("Task Submit", "Project Name must not be blank!");
}

switch($_REQUEST['action']) {

	case 'submit_insert':

		if (db_result(db_query('SELECT COUNT(*) FROM projects WHERE project_id='.$project_id.' LIMIT 1'), 0, 0) < 1) {
			error('Database integrity check', 'Input data does not match - no project for task');
		}

		//carry out some data consistency checking
		/*
		 if ($parent_id != 0) {
			if (db_result(db_query('SELECT COUNT(*) FROM tasks WHERE id='.$parent_id.' LIMIT 1'), 0, 0) < 1) {
			error('Database integrity check', 'Input data does not match - no parent task for subtask');
			}
			}
			*/
		//start transaction
		db_begin();

		$ord_num = db_result(db_query('SELECT max(order_num)+1 FROM tasks WHERE project_id='.$project_id.' and parent_task_ID='.$parent_task_id.' LIMIT 1'),0,0);
		//		$ord_num = db_simplequery("tasks","max(order_num)+1","project_id",$project_id);
		$q = db_query("INSERT INTO tasks(task_name,parent_task_ID,weight,Description,order_num,Assigned_To_ID,Start_Date,End_Date,Priority,Status,PercentComplete,Project_id)
					values ('".db_escape_string($name)."','$parent_task_id','$weight','".db_escape_string($text)."','$ord_num','$assigned_to','".database_date($startdate,1)."','".database_date($enddate,1)."','$priority','$status','$percentcomplete','$project_id')");

		// get taskid for the new task/project
		$task_id = db_lastoid('tasks_id_seq');

		db_query('call spUpdateMilestoneTotalWeight('.$task_id.')');

		//transaction complete
		db_commit();
		break;

	case 'submit_update':

		//special case: task_id cannot be zero
		if ($task_id == 0) {
			error('Task submit', 'Variable task_id is not correctly set');
		}

		//begin transaction
		db_begin();

		//change the info
		db_query('UPDATE tasks
					SET task_name=\''.db_escape_string($name).'\',
					Description=\''.db_escape_string($text).'\',
					weight='.$weight.',
					Assigned_To_ID='.$assigned_to.',
					Start_Date=\''.database_date($startdate,1).'\',
					End_Date=\''.database_date($enddate,1).'\',
					Priority=\''.$priority.'\',
					Status=\''.$status.'\',
					LastUpdated=now()
					WHERE task_id='.$task_id.' AND Project_id='.$project_id);

		db_query('call spUpdateMilestoneTotalWeight('.$task_id.')');

		//transaction complete
		db_commit();
		break;

	case 'submit_invite':
		//mandatory numeric inputs
		$input_array = array('task_id', 'project_id');
		foreach($input_array as $var) {
			if (!@safe_integer($_POST[$var])) {
				error('Task submit', 'Variable '.$var.' is not correctly set');
			}
			${$var} = $_POST[$var];
		}

		$message = $_POST['message'];

		//Get Data
		if (!($row = db_fetch_array( db_query('SELECT * FROM tasks WHERE id='.$task_id.' AND creator='.UID), 0))) {
			error('Task Invite', 'There is no task information available for that contact');
		}

		if ($row['assigned_to'] == 0) {
			error("Task Invite","There is no assigned_to contact");
		} else {
			//get user information
			if (!($contact_row = db_fetch_array( db_query('SELECT * FROM contacts WHERE id='.$row['assigned_to']), 0))) {
				error('Task Invite', 'There is no contact information for that contact');
			}
		}

		// SEND EMAIL
		if ($contact_row['email'] <> "") {
			//begin transaction
			db_begin();

			//change status of assigned_to_status as invited
			db_query('UPDATE tasks
							SET last_edited=now(),
							assigned_to_status=12
							WHERE id='.$task_id.' AND project_id='.$project_id);

			// SEND EMAIL
			$token = md5(uniqid(rand(),1));
			db_query('INSERT INTO link_table(token,method,project_id,task_id,max_tries) VALUES("'.$token.'", "V", '.$project_id.', '.$task_id.', 5)');
			email($contact_row['email'], "CAPP: Invitation to a new Project Task", "Follow this link to review the invitation.  ".BASE_URL."/?qid=".$token."\n\nPersonal Note: ".$message);

			//transaction complete
			db_commit();
		}

		break;

	default:
		error('Task Submit','Invalid Request');
		break;
}

?>