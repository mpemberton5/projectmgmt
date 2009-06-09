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
		$input_array = array('notify');
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

		$note = mysql_real_escape_string($_POST['note']);
		if (empty($note)) {
			warning('Task Note Submit', 'No Message!  Please go back and try again');
		}

		//do data consistency check on parent_id
		if (db_result(db_query('SELECT COUNT(*) FROM tasks WHERE task_id='.$task_id.' AND project_id='.$project_id), 0, 0) == 0){
			error('Task Note submit', 'Data consistency error - child post has no parent');
		}

		//public post
		db_begin();
		db_query ("INSERT INTO task_notes (project_ID, task_ID, TimeStamp, Note, user_ID, PercentComplete)
					VALUES ('$project_id', '$task_id', now(), '$note', ".$_SESSION["UID"].",'$percentcomplete')");

		//get last insert id
		$note_id = db_lastoid('note_ID');

		db_query("UPDATE tasks set PercentComplete=".$percentcomplete." where task_ID=".$task_id);

		// notifications
		if ($notify=='Lead') {
			$lead_email = db_result(db_query('SELECT emp.EMail FROM employees emp, projects proj WHERE proj.Owner_ID=emp.employee_ID and proj.project_ID='.$project_id),0,0);
			$project_name=db_result(db_query('SELECT project_name FROM projects WHERE project_ID='.$project_id), 0, 0);
			$parent_task_id=db_result(db_query('SELECT parent_task_ID FROM tasks WHERE task_ID='.$task_id), 0, 0);
			$milestone_name=db_result(db_query('SELECT task_name FROM tasks WHERE task_ID='.$parent_task_id), 0, 0);
			$task_name=db_result(db_query('SELECT task_name FROM tasks WHERE task_ID='.$task_id), 0, 0);
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

		if ($percentcomplete==100) {
			// update parent task
			//db_query("");

			// Be sure to change Curr_Task_ID = to task_id
			//db_query("UPDATE tasks SET Curr_Task_ID=".$task_id." WHERE task_id=".$milestone_id." AND Project_id=".$project_id);

		}

		// update total weight in Milestone
		db_query('call spUpdateMilestoneTotalWeight('.$task_id.')');

		//set time of last messages post to this project
		//db_query('UPDATE projects SET lastmessagepost=now() WHERE id='.$project_id);

		db_commit();

		break;

		//submit Edit
	case 'submit_update':
		$input_array = array('project_id', 'task_id', 'percentcomplete', 'note_id');
		foreach ($input_array as $var) {
			if (!@safe_integer($_POST[$var])) {
				error('Task Note submit', "Variable $var is not set");
			}
			if ($_POST[$var]==0) {
				error('Task Note submit', "Variable $var is zero");
			}
			${$var} = $_POST[$var];
		}

		$note = mysql_real_escape_string($_POST['note']);

		//do data consistency check on parent_id
		if (db_result(db_query('SELECT COUNT(*) FROM tasks WHERE task_id='.$task_id.' AND project_id='.$project_id), 0, 0) == 0){
			error('Task Note submit', 'Data consistency error - child post has no parent');
		}

		//public post
		db_begin();
		db_query('UPDATE task_notes SET Note=\''.$note.'\', PercentComplete='.$percentcomplete.' WHERE note_ID ='.$note_id);

		//set time of last messages post to this project
		//db_query('UPDATE projects SET lastmessagepost=now() WHERE id='.$project_id);

		db_commit();

		break;

		//owner of the thread can delete, admin can delete
	case 'submit_del':
		if (!@safe_integer($_GET['message_id'])) {
			error('Message submit', 'Message_id not valid');
		}
		$message_id = $_GET['message_id'];

		//check if user is owner of the task or the owner of the post
		if ((db_result(db_query('SELECT COUNT(*) FROM messages LEFT JOIN projects ON (messages.project_id=projects.id) WHERE projects.owner='.UID.' AND messages.id='.$message_id), 0, 0) == 1) ||
		(db_result(db_query('SELECT COUNT(*) FROM messages WHERE creator='.UID.' AND id='.$message_id), 0, 0 ) == 1)) {

			db_begin();
			delete_messages($message_id);
			db_commit();
		} else
		error('Forum submit', 'You are not authorised to delete that post.');
		break;
}

?>