<?php
/* $Id$ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

//includes
require_once('path.php');
require_once(BASE.'includes/security.php');

//secure variables
$content = '';
$print = '';
$title = '';

//
// Recursive function for listing all posts of a task
//
function list_records($pt_id) {

	global $post_array, $parent_array, $post_count;

	$post_array   = array();
	$parent_array = array();
	$post_count   = 0;
	$content = "";

	//query to get the children for this project_id
	$SQL = "SELECT * FROM proj_template_details where pt_id=".$pt_id." ORDER BY ptd_parent_id, order_num";
	
	$q = db_query($SQL);

	//check for any posts
	if (db_numrows($q) < 1) {
		return "<p>";
	}

//	$content = "	<ul>\n";
	// First build an array of records
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {

		$task_id = $row['ptd_id'];
		$task_name = $row['ptd_name'];
		$parent_task_id = $row['ptd_parent_id'];

			//put values into array
			$post_array[$i]['id'] = $task_id;
			$post_array[$i]['parent_id'] = $parent_task_id;

			//if this is a subpost, store the parent id
			if ($parent_task_id != 0) {
				$parent_array[$parent_task_id] = $parent_task_id;
//				$this_post = "				<li id=\"task-".$task_id."\" data=\"addClass: 'txtmaxsize', url: 'tasks.php?action=showTaskLevel&project_id=".$pt_id."&task_id=".$task_id."'\">".$task_name."</li>\n";
				$this_post = "-- ".$task_name."<br />\n";
			} else {
//				$this_post = "		<li id=\"task-".$task_id."\" data=\"addClass: 'txtmaxsize', url: 'tasks.php?action=showMilestoneLevel&project_id=".$pt_id."&task_id=".$task_id."'\">".$task_name."\n";
				$this_post = "<b>".$task_name."</b><br />\n";
			}

			$post_array[$i]['post'] = $this_post;
			++$post_count;
	}

	//iteration for first level posts
	for ($i=0; $i < $post_count; ++$i) {

		//ignore subtasks in this iteration
		if ($post_array[$i]['parent_id'] != 0) {
			continue;
		}
		$content .= $post_array[$i]['post'];

		//if this post has children (subposts), iterate recursively to find them
		if (isset($parent_array[($post_array[$i]['id'])])) {
			$content .= find_children($post_array[$i]['id']);
		}
//		$content .= "		</li>\n";
	}
//	$content .= "	</ul>\n";

	db_free_result($q);

	return $content;
}

//
// List subposts (recursive function)
//
function find_children($parent_id) {

	global $post_array, $parent_array, $post_count;
	$content = "";
//	$content = "			<ul>\n";

	for($i=0; $i < $post_count ; ++$i) {

		if ($post_array[$i]['parent_id'] != $parent_id) {
			continue;
		}
		$content .= $post_array[$i]['post'];

		//if this post has children (subposts), iterate recursively to find them
		if (isset($parent_array[($post_array[$i]['id'])])) {
			$content .= find_children($post_array[$i]['id']);
		}
	}
//	$content .= "			</ul>\n";

	return $content;
}

//$content .= list_records( $_REQUEST['pt_id']);



function getProjectData($p_id) {
	$SQL  = "SELECT p.* FROM projects p where p.project_ID=".$p_id;
	$q = db_query($SQL);
	$tmpdata = '';

	//get the data
	if (!($project_row = db_fetch_array($q, 0))) {
		error('Project Print', 'The requested item has either been deleted, or is now invalid.');
	}

	$tmpdata .= "<H1>".$project_row['Project_Name']."</H1><BR />\n";
	$tmpdata .= "<div>test1</div>\n";
	$tmpdata .= "<div style=\"display: none;\">test2</div>\n";
	return $tmpdata;
}
function getTaskData($t_id) {
	
}
function getMilestoneData($m_id) {
	
}
// Project Action
switch($_REQUEST['action']) {
	case 'popupProjectPrint':
		$title .= "Print Project";
		$project_id = $_REQUEST['project_id'];
		$print .= getProjectData($project_id);
		break;

	case 'popupMilestonesPrint':
		$title .= "Print All Milestones";
		$project_id = $_REQUEST['project_id'];
		break;

	case 'popupMilestonePrint':
	case 'popupTasksPrint':
		$title .= "Print Specific Milestone";
		$project_id = $_REQUEST['project_id'];
		$m_id = $_REQUEST['task_id'];
		break;

	case 'popupTaskPrint':
		$title .= "Print Specific Task";
		$project_id = $_REQUEST['project_id'];
		$t_id = $_REQUEST['task_id'];
		break;

		//Error case
	default:
		error('Action handler', 'Invalid request1');
		break;
}

$content .= "<html>\n";
$content .= "<head>\n";
$content .= "<title>".$title."</title>\n";
$content .= "</head>\n";
$content .= "<body>\n";
$content .= $print;
//$content .= "<a href=\"javascript:window.print()\">Print this page</a>\n";
$content .= "<input type=\"button\" value=\"Print\" onClick=\"window.print(); parent.fb.end(true); return false;\" />\n";

$content .= "</body>\n";
$content .= "</html>\n";

echo $content;
?>