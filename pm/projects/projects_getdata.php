<?php
/* $Id$ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/time.php');

//
//START OF MAIN PROGRAM
//
$content = "";

//
// Recursive function for listing all posts of a task
//
function list_records($pt_id,$parent_project_id,$parent_milestone_id) {

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

	$content = "	<ul>\n";
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
				$this_post = "		<li >&#187; ".$task_name."</li>\n";
			} else {
//				$this_post = "		<li id=\"task-".$task_id."\" data=\"addClass: 'txtmaxsize', url: 'tasks.php?action=showMilestoneLevel&project_id=".$pt_id."&task_id=".$task_id."'\">".$task_name."\n";
				$this_post = "		<li style=\"font-weight: bold;\">".$task_name."</li>\n";
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
			$content .= "		<ul>\n";
			$content .= find_children($post_array[$i]['id']);
			$content .= "		</ul>\n";
		}
	}
	$content .= "</ul>\n";

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



// Project Action
switch($_REQUEST['action']) {

	//Main Project List
	case 'template_details':
		$content .= "<div class=\"generalbox\">\n";
		$content .= list_records( $_REQUEST['pt_id'],$_REQUEST['parent_project_id'],$_REQUEST['parent_milestone_id']);
		$content .= "</div>\n";
		break;

	//Main Project List
	case 'list_all':
		if (!isset($_POST['page'])) {
			$page = 2;
		} else {
			$page = $_POST['page'];
			if ($page < 0) {
				$page = 1;
			}
		}
		if (!isset($_POST['rp'])) {
			$rp = 10;
		} else {
			$rp = $_POST['rp'];
		}
		if (!isset($_POST['sortname'])) {
			$sortname = 'Project_Name';
		} else {
			$sortname = $_POST['sortname'];
		}
		if (!isset($_POST['sortorder'])) {
			$sortorder = 'desc';
		} else {
			$sortorder = $_POST['sortorder'];
		}

		$sort = "ORDER BY $sortname $sortorder";

		$start = (($page-1) * $rp);

		$limit = "LIMIT $start, $rp";

		$SQL  = "SELECT *, concat(emp.FirstName,' ',emp.LastName) as empName, ";
		$SQL .= "(select Dept_Name from departments where departments.department_id=emp.department_id) as Dept ";
		$SQL .= "FROM projects proj, employees emp ";
		$SQL .= "WHERE emp.employee_id=proj.owner_id $sort $limit";
		$result = db_query($SQL);

		$total = db_result(db_query('SELECT count(*) FROM projects'),0,0);

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
		header("Cache-Control: no-cache, must-revalidate" );
		header("Pragma: no-cache" );
//		header("Content-type: text/x-json");
		$json = "";
		$json .= "{\n";
		$json .= "page: $page,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		while ($row = mysql_fetch_array($result)) {
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:\"".$row['project_ID']."\",";
			$json .= "cell:[\"".mysql_escape_string($row['Project_Name'])."\"";
			$json .= ",\"".$row['Status']."\"";
			$json .= ",\"".$row['Dept']."\"";
			$json .= ",\"".$row['empName']."\"";
			$json .= "]}";
			$rc = true;
		}
		$json .= "]\n";
		$json .= "}";
		$content = $json;
		break;

	//Error case
	default:
		error('Project action handler', 'Invalid request');
		break;
}

echo $content;
?>