<?php
/*
 $Id: note_submit.php,v 1.6 2009/06/03 20:18:07 markp Exp $
 */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

//includes
require_once('path.php');
require_once(BASE.'includes/security.php');

/**
 * Function for listing all posts of a task
 * @param $message_id
 * @return unknown_type
 */
function find_posts($message_id) {

	global $post_array, $parent_array, $match_array, $index, $post_count;

	$post_array   = array();
	$parent_array = array();
	$match_array  = array();
	$parent_count = 0;
	$post_count   = 0;
	$index = 0;

	$project_id = db_result(db_query('SELECT project_id FROM messages WHERE id='.$message_id), 0, 0);

	$q = db_query('SELECT id, parent_id FROM messages WHERE project_id='.$project_id);

	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {

		//put values into array
		$post_array[$i]['id'] = $row['id'];
		$post_array[$i]['parent_id'] = $row['parent_id'];
		++$post_count;

		//if this is a subpost, store the parent id
		if ($row['parent_id'] != 0) {
			$parent_array[$parent_count] = $row['parent_id'];
			++$parent_count;
		}
	}

	//record first match
	$match_array[$index] = $message_id;
	++$index;

	//if selected post has children (subposts), iterate recursively to find them
	if (in_array($message_id, (array)$parent_array)) {
		find_children($message_id);
	}

	return;
}

//
// List subposts (recursive function)
//
function find_children($parent_id) {

	global $post_array, $parent_array, $match_array, $index, $post_count;

	for ($i=0; $i < $post_count; ++$i) {

		if ($post_array[$i]['parent_id'] != $parent_id) {
			continue;
		}
		$match_array[$index] = $post_array[$i]['id'];
		++$index;

		//if this post has children (subposts), iterate recursively to find them
		if (in_array($post_array[$i]['id'], (array)$parent_array)) {
			find_children($post_array[$i]['id']);
		}
	}
	return;
}

/**
 * Perform delete of all messages messages in the thread below the selected message
 * @param $message_id
 * @return null
 */
function delete_messages($message_id) {

	global $match_array, $index;

	find_posts($message_id);

	// perform the delete - delete from newest post first to oldest post last to prevent database referential errors
	for ($i=0; $i < $index; ++$i) {
		db_query('DELETE FROM messages WHERE id='.$match_array[($index - 1) - $i]);

	}
	return;
}

if (!isset($_REQUEST['action'])) {
	error('Task Note submit', 'No request given');
}

//secure variables
$mail_list = array();

//if user aborts, let the script carry onto the end
ignore_user_abort(TRUE);

switch($_REQUEST['action']) {

	case 'submit_insert':

		//if all values are filled in correctly we can submit the messages-item
		$input_array = array('project_id', 'task_id', 'percentcomplete');
		foreach ($input_array as $var) {
			if (!@safe_integer($_POST[$var])) {
				error('Task Note submit', "Variable $var is not set");
			}
			if ($_POST[$var]==0) {
				error('Task Note submit', "Variable $var is zero");
			}
			${$var} = $_POST[$var];
		}

		// text inputs
		$input_array = array('notify', 'task_action');
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

		// Mark pct complete if user set action to be completed.
		if ($task_action=="Comp") {
			$percentcomplete = "100";
		}

		$note = mysql_real_escape_string($_POST['note']);
		if (empty($note)) {
			warning('Task Note Submit', 'No Message!  Please go back and try again');
		}

		// Pull Task Information
		$q = db_query('SELECT * FROM tasks WHERE task_id='.$task_id.' AND project_id='.$project_id);
		if (db_numrows($q) < 1) {
			error("Task Note Submit", "Unable to Find Task Record");
		}
		if (!$task_row = db_fetch_array($q, 0)) {
			error("Task Note Submit", "Unable to Find Task");
		}
		$milestone_id = $task_row['parent_task_ID'];
		$task_name = $task_row['task_name'];

		// Pull Milestone Information
		$r = db_query('SELECT * FROM tasks WHERE task_id='.$milestone_id.' AND project_id='.$project_id);
		if (db_numrows($r) < 1) {
			error("Task Note Submit", "Unable to Find Milestone Record");
		}
		if (!$milestone_row = db_fetch_array($r, 0)) {
			error("Task Note Submit", "Unable to Find Milestone");
		}
		$milestone_name = $milestone_row['task_name'];
		$curr_milestone_order_num = $milestone_row['order_num'];

		//public post
		db_begin();
		db_query("INSERT INTO task_notes (project_ID, task_ID, TimeStamp, Note, user_ID, PercentComplete)
					VALUES ('$project_id', '$task_id', now(), '$note', ".$_SESSION["UID"].",'$percentcomplete')");

		//get last insert id
		$note_id = db_lastoid('note_ID');

		db_query("UPDATE tasks set PercentComplete=".$percentcomplete." where task_ID=".$task_id);

		$lead_notified = 0;

		// next action
		if ($percentcomplete==100 or $task_action=="Comp") {
			// if another task exists within this milestone
			$next_task_ID = db_result(db_query('SELECT task_ID FROM tasks WHERE parent_task_ID='.$milestone_id.' AND project_id='.$project_id.' AND order_num = '.($task_row['order_num']+1)), 0, 0);
			if ($next_task_ID > 0) {
				// - auto notify next task user
				$ondeck_email = db_result(db_query('SELECT emp.EMail FROM employees emp, tasks t WHERE t.Assigned_To_ID=emp.employee_ID and t.task_ID='.$next_task_ID),0,0);
				//TODO: change message
				$message = "Batter Up!";
				send_html_email($ondeck_email,"noreply@wfubmc.edu","Project Notification",$message);
				// - change on-deck task on milestone
				db_query("UPDATE tasks SET Curr_Task_ID=".$next_task_ID." WHERE task_id=".$milestone_id." AND Project_id=".$project_id);
			} else {
				// search to see if there is another milestone that needs to be put on deck
				$next_milestone_ID = db_result(db_query('SELECT task_ID FROM tasks WHERE parent_task_ID=0 AND project_id='.$project_id.' AND order_num = '.($curr_milestone_order_num+1)), 0, 0);
				if ($next_milestone_ID > 0) {
					// find first task and notify user
					// if another task exists within this milestone
					$next_task_ID = db_result(db_query('SELECT task_ID FROM tasks WHERE parent_task_ID='.$next_milestone_ID.' AND project_id='.$project_id.' AND order_num = '.($task_row['order_num']+1)), 0, 0);
					if ($next_task_ID > 0) {
						//    - auto notify next task user
						$ondeck_email = db_result(db_query('SELECT emp.EMail FROM employees emp, tasks t WHERE t.Assigned_To_ID=emp.employee_ID and t.task_ID='.$next_task_ID),0,0);
						//TODO: change message
						$message = "Batter Up!";
						send_html_email($ondeck_email,"noreply@wfubmc.edu","Project Notification",$message);
						//    - change on deck user on milestone
						db_query("UPDATE tasks SET Curr_Task_ID=".$next_task_ID." WHERE task_id=".$next_milestone_ID." AND Project_id=".$project_id);
					} else {
						// No task exists for this Milestone
						// TODO: what do we do here when project milestone has no tasks?
					}
				} else {
					// this project is complete - notify project lead
					$lead_notified = 1;
					$lead_email = db_result(db_query('SELECT emp.EMail FROM employees emp, projects proj WHERE proj.Owner_ID=emp.employee_ID and proj.project_ID='.$project_id),0,0);
					$message = "Project Done!";
					//TODO: change message
					send_html_email($lead_email,"noreply@wfubmc.edu","Project Notification",$message);
					//    - change on deck user on milestone
					db_query("UPDATE tasks SET Curr_Task_ID=0 WHERE task_id=".$next_milestone_ID." AND Project_id=".$project_id);
				}
			}
		}


		// notifications
		if ($notify=='Lead' and $lead_notified==0) {
			$lead_email = db_result(db_query('SELECT emp.EMail FROM employees emp, projects proj WHERE proj.Owner_ID=emp.employee_ID and proj.project_ID='.$project_id),0,0);
			$project_name=db_result(db_query('SELECT project_name FROM projects WHERE project_ID='.$project_id), 0, 0);
			$milestone_name=db_result(db_query('SELECT task_name FROM tasks WHERE task_ID='.$milestone_id), 0, 0);
			$message = "";
			$message .= "<h2>A Note had been added to a Project!</h2>\n";
			$message .= "<table>\n";
			$message .= "<tr><td>Project:</td><td>".$project_name."</td></tr>\n";
			$message .= "<tr><td>Milestone:</td><td>".$milestone_name."</td></tr>\n";
			$message .= "<tr><td>Task:</td><td>".$task_name."&nbsp;&nbsp;(".$percentcomplete."% complete)</td></tr>\n";
			$message .= "<tr><td>Note:</td><td>".$_POST['note']."</td></tr>\n";
			$message .= "</table><br /><br />\n";
			$message .= "<a href=\"".BASE_URL."projects.php?project_id=".$project_id."\">Click Here to go directly to the project</a>";
			$message .= "<br /><br /><br />NOTE: This is an automatic notification from WFUBMC Project Management.<br />";
			send_html_email($lead_email,"noreply@wfubmc.edu","Project Notification",$message);
		}


		if ($task_action=="Prev") {
			// TODO: Add Previous move
			// send task back to previous task if exists
			// post no impact entry on prev task stating action
		}

		// update total weight in Milestone
		db_query('call spUpdateMilestoneTotalWeight('.$task_id.')');

		//set time of last messages post to this project
		//db_query('UPDATE projects SET lastmessagepost=now() WHERE id='.$project_id);

		db_commit();

		// Free DB Results
		db_free_result($q);
		db_free_result($r);

		break;

		//		//submit Edit
		//	case 'submit_update':
		//		$input_array = array('project_id', 'task_id', 'percentcomplete', 'note_id');
		//		foreach ($input_array as $var) {
		//			if (!@safe_integer($_POST[$var])) {
		//				error('Task Note submit', "Variable $var is not set");
		//			}
		//			if ($_POST[$var]==0) {
		//				error('Task Note submit', "Variable $var is zero");
		//			}
		//			${$var} = $_POST[$var];
		//		}
		//
		//		$note = mysql_real_escape_string($_POST['note']);
		//
		//		//do data consistency check on parent_id
		//		if (db_result(db_query('SELECT COUNT(*) FROM tasks WHERE task_id='.$task_id.' AND project_id='.$project_id), 0, 0) == 0){
		//			error('Task Note submit', 'Data consistency error - child post has no parent');
		//		}
		//
		//		//public post
		//		db_begin();
		//		db_query('UPDATE task_notes SET Note=\''.$note.'\', PercentComplete='.$percentcomplete.' WHERE note_ID ='.$note_id);
		//
		//		//set time of last messages post to this project
		//		//db_query('UPDATE projects SET lastmessagepost=now() WHERE id='.$project_id);
		//
		//		db_commit();
		//
		//		break;
		//
		//		//owner of the thread can delete, admin can delete
		//	case 'submit_del':
		//		if (!@safe_integer($_GET['message_id'])) {
		//			error('Message submit', 'Message_id not valid');
		//		}
		//		$message_id = $_GET['message_id'];
		//
		//		//check if user is owner of the task or the owner of the post
		//		if ((db_result(db_query('SELECT COUNT(*) FROM messages LEFT JOIN projects ON (messages.project_id=projects.id) WHERE projects.owner='.UID.' AND messages.id='.$message_id), 0, 0) == 1) ||
		//		(db_result(db_query('SELECT COUNT(*) FROM messages WHERE creator='.UID.' AND id='.$message_id), 0, 0 ) == 1)) {
		//
		//			db_begin();
		//			delete_messages($message_id);
		//			db_commit();
		//		} else
		//		error('Forum submit', 'You are not authorised to delete that post.');
		//		break;
}

?>