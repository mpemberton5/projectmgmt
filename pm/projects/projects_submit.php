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

////
//// Recursive function to find chldren tasks and reset their projectid's
////
//function reparent_children($project_id) {
//
//	global $projectid;
//
//	//find the children tasks - if any
//	$q = db_query('SELECT id FROM tasks WHERE parent='.$project_id);
//
//	if (db_numrows($q) == 0) {
//		return;
//	}
//
//	for ($i=0; $row = @db_fetch_num($q, $i); ++$i) {
//		db_query('UPDATE tasks SET projectid='.$projectid.' WHERE id='.$row[0]);
//		//recursion to find anymore children
//		reparent_children($row[0]);
//	}
//
//	return;
//}

// numeric inputs
$input_array = array('task_id','project_id','milestone_id','parent_task_id','assigned_to','percentcomplete','CE','managed','contingency','watch_flag','client_id','parent_project_id','parent_milestone_id','pt_id','selected_project_id','selected_template_id');
foreach($input_array as $var) {
	if (isset($_POST[$var]) and !empty($_POST[$var])) {
		if (!@safe_integer($_POST[$var])) {
			error('Task submit', 'Variable '.$var.' is not set correctly ['.$_POST[$var].']');
		}
		${$var} = $_POST[$var];
	} else {
		${$var} = 0;
	}
}

// text inputs
$input_array = array('name','status','priority','startdate','enddate','contact','impact','clientcontact');
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
$input_array = array('description');
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
		// Retrieve passed variables
		if (empty($_POST['name'])) {
			error("Task Submit", "Project Name must not be blank!");
		}

		//start transaction
		db_begin();
		$q = db_query("INSERT INTO projects (Project_Name,Description,CreationDate,StartDate,EndDate,Priority,Status,Client_ID,Owner_ID,CE,Managed,Contingency,Impact,ClientContact)
		              values('".db_escape_string($name)."','".db_escape_string($description)."',now(),'".database_date($startdate,1)."','".database_date($enddate,1)."','$priority','$status','$client_id','$assigned_to','$CE','$managed','$contingency','$impact','".db_escape_string($clientcontact)."')");

		// get taskid for the new task/project
		$project_id = db_lastoid('project_id_seq');

		// For Parent Projects
		if ($parent_project_id>0) {
			$q = db_query("INSERT INTO tasks(task_name,parent_task_ID,weight,Description,order_num,Assigned_To_ID,Start_Date,End_Date,Priority,Status,PercentComplete,Project_id,ParentProjectLink_ID)
					values ('".db_escape_string($name)."','$parent_milestone_id','1','','1','$assigned_to','".database_date($startdate,1)."','".database_date($enddate,1)."','$priority','$status','$percentcomplete','$parent_project_id','$project_id')");
		}

		if ($selected_template_id>0) {
			// loop through whole template and add milestones/tasks
			//first get list of milestones
			$m_q = db_query('SELECT * FROM proj_template_details where pt_id='.$selected_template_id.' and ptd_parent_id=0 ORDER BY order_num');
			for ($i=0; $m_row = @db_fetch_array($m_q, $i); ++$i) {
				// Insert Milestone
//				db_query("INSERT INTO tasks(task_name,parent_task_ID,weight,Description,order_num,Assigned_To_ID,Start_Date,End_Date,Priority,Status,PercentComplete,Project_id,ParentProjectLink_ID)
//					values ('".db_escape_string($name)."','$parent_milestone_id','1','','1','$assigned_to','".database_date($startdate,1)."','".database_date($enddate,1)."','$priority','$status','$percentcomplete','$parent_project_id','$project_id')");

				db_query("INSERT INTO tasks(task_name,parent_task_ID,weight,Description,order_num,Assigned_To_ID,Start_Date,End_Date,Priority,Status,PercentComplete,Project_id)
					values ('".db_escape_string($m_row['ptd_name'])."','0','1','','1','$assigned_to','".database_date($startdate,1)."','".database_date($enddate,1)."','$priority','$status','$percentcomplete','$project_id')");
				// get taskid for the new milestone
				$milestone_id = db_lastoid('milestone_id_seq');

				// Query for all tasks related to this milestone
				$tSQL = "SELECT * FROM proj_template_details where pt_id=".$selected_template_id." and ptd_parent_id=".$m_row['ptd_id']." ORDER BY order_num";
				$t_q = db_query($tSQL);
				for ($j=0; $t_row = @db_fetch_array($t_q, $j); ++$j) {
					// Insert Task
//					db_query("INSERT INTO tasks(task_name,parent_task_ID,weight,Description,order_num,Assigned_To_ID,Start_Date,End_Date,Priority,Status,PercentComplete,Project_id,ParentProjectLink_ID)
//						values ('".db_escape_string($name)."','$parent_milestone_id','1','','1','$assigned_to','".database_date($startdate,1)."','".database_date($enddate,1)."','$priority','$status','$percentcomplete','$parent_project_id','$project_id')");

					db_query("INSERT INTO tasks(task_name,parent_task_ID,weight,Description,order_num,Assigned_To_ID,Start_Date,End_Date,Priority,Status,PercentComplete,Project_id)
						values ('".db_escape_string($t_row['ptd_name'])."','$milestone_id','1','','1','$assigned_to','".database_date($startdate,1)."','".database_date($enddate,1)."','$priority','$status','$percentcomplete','$project_id')");
					// get taskid for the new task
					$task_id = db_lastoid('tasks_id_seq');
					if ($t_row['order_num']==0) {
						// Be sure to change Curr_Task_ID = to task_id (FOR FIRST TASK ONLY)
						db_query("UPDATE tasks SET Curr_Task_ID=".$task_id." WHERE task_id=".$milestone_id." AND Project_id=".$project_id);
					}
				}
			}
		}

		//transaction complete
		db_commit();
		// this is passed back to origination page if needed
		echo $project_id;
		break;

	case 'submit_update':
		// Retrieve passed variables
		if (empty($_POST['name'])) {
			error("Task Submit", "Project Name must not be blank!");
		}

		//special case: project_id cannot be zero
		if ($project_id == 0) {
			error('Projects submit', 'Variable project_id is not correctly set');
		}

		//begin transaction
		db_begin();

		//change the info
		db_query('UPDATE projects
			SET Project_Name=\''.db_escape_string($name).'\',
			Description=\''.db_escape_string($description).'\',
			Priority=\''.$priority.'\',
			StartDate=\''.database_date($startdate,1).'\',
			EndDate=\''.database_date($enddate,1).'\',
			Status=\''.$status.'\',
			Owner_ID=\''.$assigned_to.'\',
			Client_ID=\''.$client_id.'\',
			CE=\''.$CE.'\',
			Managed=\''.$managed.'\',
			Contingency=\''.$contingency.'\',
			Impact=\''.$impact.'\',
			LastUpdated=now(),
			ClientContact=\''.db_escape_string($clientcontact).'\'
			WHERE project_ID='.$project_id);

		//transaction complete
		db_commit();
		break;

	case 'submit_quick_insert':
		//start transaction
		db_begin();
		$q = db_query("INSERT INTO projects (Project_Name,Description,CreationDate,StartDate,EndDate,Priority,Status,Client_ID,Owner_ID,CE,Managed,Contingency,Impact)
		              values('".db_escape_string($name)."','".db_escape_string($description)."',now(),'".database_date($startdate,1)."','".database_date($enddate,1)."','$priority','$status','$client_id','$assigned_to','$CE','$managed','$contingency','$impact')");

		// get taskid for the new project
		$project_id = db_lastoid('project_id_seq');

		// Add Milestone
		$q = db_query("INSERT INTO tasks(task_name,parent_task_ID,weight,Description,order_num,Assigned_To_ID,Start_Date,End_Date,Priority,Status,PercentComplete,Project_id)
					values ('".db_escape_string($name)."','0','1','','1','$assigned_to','".database_date($startdate,1)."','".database_date($enddate,1)."','$priority','$status','$percentcomplete','$project_id')");

		// get taskid for the new milestone
		$milestone_id = db_lastoid('milestone_id_seq');

		// Add Task
		$q = db_query("INSERT INTO tasks(task_name,parent_task_ID,weight,Description,order_num,Assigned_To_ID,Start_Date,End_Date,Priority,Status,PercentComplete,Project_id)
					values ('".db_escape_string($name)."','$milestone_id','1','','1','$assigned_to','".database_date($startdate,1)."','".database_date($enddate,1)."','$priority','$status','$percentcomplete','$project_id')");

		// get taskid for the new task
		$task_id = db_lastoid('tasks_id_seq');

		// Be sure to change Curr_Task_ID = to task_id
		db_query("UPDATE tasks SET Curr_Task_ID=".$task_id." WHERE task_id=".$milestone_id." AND Project_id=".$project_id);

		//transaction complete
		db_commit();
		// this is passed back to origination page if needed
		echo $project_id;
		break;

	case 'submit_delete':
		//TODO: confirm
		//delete tasks/milestones
		//delete project
		db_begin();
		db_query("DELETE FROM tasks WHERE Project_ID=".$project_id);
		db_query("DELETE FROM projects WHERE project_ID=".$project_id);
		db_commit();
		break;

	case 'submit_watch':
		db_begin();
		
		if ($watch_flag==0) {
			// Not Watching Now, so start watching - add record
			$q = db_query("INSERT INTO user_prefs (user_ID,pref_type,value1) values ('".$_SESSION['UID']."', 'watchedProject', '$project_id')");
		} else {
			// currently catching, delete record
			db_query("DELETE FROM user_prefs WHERE user_ID=".$_SESSION['UID']." and pref_type='watchedProject' and value1=".$project_id);
		}

		db_commit();
		break;

	case 'savePL':
		//parent_project_id,parent_milestone_id,selected_project_id
		// insert
		//$SQL = "SELECT * FROM projects where project_ID=".$selected_project_id;
		$proj_row = db_fetch_array(db_query("SELECT * FROM projects where project_ID=".$selected_project_id),0);
			//find the children tasks - if any
		db_begin();
		$r = db_query("INSERT INTO tasks(task_name,parent_task_ID,weight,Description,order_num,Assigned_To_ID,Start_Date,End_Date,Priority,Status,PercentComplete,Project_ID,ParentProjectLink_ID)
					values ('".$proj_row['Project_Name']."','$milestone_id','1','','1','".$proj_row['Owner_ID']."','".$proj_row['StartDate']."','".$proj_row['EndDate']."','".$proj_row['Priority']."','".$proj_row['Status']."','".$proj_row['PercentComplete']."','$project_id','$selected_project_id')");

//		$q = db_query("INSERT INTO tasks(task_name,parent_task_ID,weight,Description,order_num,Assigned_To_ID,Start_Date,End_Date,Priority,Status,PercentComplete,Project_id,ParentProjectLink_ID)
//					values ('".$p_row['Project_Name']."','$parent_milestone_id','1','','1','".$p_row['Owner_ID']."','".$p_row['StartDate']."','".$p_row['EndDate']."','".$p_row['Priority']."','".$p_row['Status']."','".$p_row['PercentComplete']."','$parent_project_id','$selected_project_id')");
		db_commit();
		break;

	case 'deletePL':
		db_begin();
		db_query("DELETE FROM tasks WHERE task_ID=".$task_id);
		db_commit();
		break;

	default:
		error('Task Submit','Invalid Request');
		break;
}

?>