<?php
/* $Id$ */

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
		$parent_task_id = $_POST['parent_task_id'];
		if (db_result(db_query('SELECT COUNT(*) FROM projects WHERE project_id='.$project_id.' LIMIT 1'), 0, 0) < 1) {
			error('Database integrity check', 'Input data does not match - no project for task');
		}
		$task = $_POST['task'];
		for ($i = 0; $i < count($task); $i++) {
			$SQL = "UPDATE tasks SET order_num=".$i." WHERE task_ID=".$task[$i];
			print $SQL . "\n";
			mysql_query($SQL) or die(mysql_error());
		}
		// go ahead and recalculate current task on Milestone just in case
		db_query('call spSetCurrTaskInMilestone('.$parent_task_id.')');
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
$input_array = array('task_id','project_id','parent_task_id','assigned_to','percentcomplete','weight');
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
$input_array = array('name','status','priority','startdate','enddate');
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

switch($_REQUEST['action']) {

	case 'submit_insert':

		//check task name is present
		if (empty($name)) {
			error("Task Submit", "Project Name must not be blank!");
		}

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

		// gets next available order_num in milestone
		$ord_num = db_result(db_query('SELECT max(order_num)+1 FROM tasks WHERE project_id='.$project_id.' and parent_task_ID='.$parent_task_id.' LIMIT 1'),0,0);

		$q = db_query("INSERT INTO tasks(task_name,parent_task_ID,weight,Description,order_num,Assigned_To_ID,Start_Date,End_Date,Priority,Status,PercentComplete,Project_id)
					values ('".db_escape_string($name)."','$parent_task_id','$weight','".db_escape_string($text)."','$ord_num','$assigned_to','".database_date($startdate,1)."','".database_date($enddate,1)."','$priority','$status','$percentcomplete','$project_id')");

		// get taskid for the new task/project
		$task_id = db_lastoid('tasks_id_seq');

		if ($ord_num==0) {
			// Make task the on-deck task if first task
			db_query("UPDATE tasks SET Curr_Task_ID=".$task_id." WHERE task_id=".$parent_task_id." AND Project_id=".$project_id);
		}

		db_query('call spUpdateMilestoneTotalWeight('.$task_id.')');

		//transaction complete
		db_commit();
		break;

	case 'submit_update':

		//check task name is present
		if (empty($name)) {
			error("Task Submit", "Project Name must not be blank!");
		}

		//special case: task_id cannot be zero
		if ($task_id == 0) {
			error('Task submit', 'Variable task_id is not correctly set');
		}

		//begin transaction
		db_begin();

		//change the info
		db_query('UPDATE tasks SET task_name=\''.db_escape_string($name).'\',
					Description=\''.db_escape_string($text).'\',
					weight='.$weight.',
					Assigned_To_ID='.$assigned_to.',
					Start_Date=\''.database_date($startdate,1).'\',
					End_Date=\''.database_date($enddate,1).'\',
					Priority=\''.$priority.'\',
					Status=\''.$status.'\',
					LastUpdated=now()
					WHERE task_id='.$task_id.' AND Project_id='.$project_id);

		// call sp to recalculate pct completes
		db_query('call spUpdateMilestoneTotalWeight('.$task_id.')');

		//transaction complete
		db_commit();
		break;

	case 'submit_delete':
		//delete task or milestone+tasks
		db_begin();
		// if this is a task
		if ($task_id>0) {
			// first delete task
			db_query("DELETE FROM tasks WHERE task_ID=".$task_id);
			// go ahead and recalculate current task on Milestone just in case
			db_query('call spSetCurrTaskInMilestone('.$parent_task_id.')');
			// reorder existing tasks for milestone
			db_query('call spUpdateTaskOrder('.$parent_task_id.')');
		} else {
			// first delete all tasks in milestone
			db_query("DELETE FROM tasks WHERE parent_task_ID=".$parent_task_id);
			// next delete milestone
			db_query("DELETE FROM tasks WHERE task_ID=".$parent_task_id);
			// reorder existing milestones in Project
			db_query('call spUpdateMilestoneOrder('.$project_id.')');
		}
		// call sp to recalculate pct completes
		db_query('call spUpdateMilestoneTotalWeight('.$task_id.')');
		db_commit();
		break;

	default:
		error('Task Submit','Invalid Request');
		break;
}

?>