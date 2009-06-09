<?php
/* $Id: task_show.php,v 1.26 2009/06/05 18:16:39 markp Exp $ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

//includes
require_once('path.php');
require_once(BASE.'includes/security.php');

//secure variables
$content = '';

if (!@safe_integer($_GET['project_id'])) {
	error('Task show', 'The project_id input is not valid');
}
$project_id = $_GET['project_id'];

if (isset($_GET['task_id'])) {
	if (!@safe_integer($_GET['task_id'])) {
		error('Task show', 'The task_id input is not valid');
	}
	$task_id = $_GET['task_id'];
} else {
	$task_id = 0;
}

$SQL  = "SELECT p.*, emp.FirstName, emp.LastName FROM projects p, employees emp where emp.employee_id=p.owner_id and p.project_ID=".$project_id;
$q = db_query($SQL);

//check for any posts
if (db_numrows($q) < 1) {
	error("Task Edit", "Unable to Find Task Details");
}

//get the data
if (!($project_row = db_fetch_array($q, 0))) {
	error('Task edit', 'The requested item has either been deleted, or is now invalid.');
}

$project_pct = $project_row['PercentComplete'];
$milestone_pct="0";

$content .= "<script type='text/javascript'>\n";
$content .= "function SelectTask(node){\n";
$content .= "	var task = 'task-' + node;\n";
$content .= "	$(\"#treev\").dynatree(\"getTree\").activateKey(task);\n";
$content .= "}\n";
$content .= "</script>\n";

// show project details
$content .= "<fieldset>\n";
$content .= "	<legend><span class=\"gl\" style=\"width: 260px;\">Project Details</span></legend>\n";
$content .= "	<div>\n";
$content .= "		<div style=\"position: absolute; width=100%\"><div style=\"position: relative; display: inline; float: left; top:-25px; #top:-21; right:-178px;\"><a href=\"javascript:void(0);\" onclick='fb.start({ href:\"projects.php?action=popupEdit&project_id=".$project_id."\", rev:\"width:665 height:515 infoPos:tc disableScroll:true caption:`EDIT Project Details` doAnimations:false\" });'>Edit</a></div></div>\n";
$content .= "		<div style=\"position: absolute; width=100%\"><div style=\"position: relative; display: inline; float: left; top:-25px; #top:-21; right:-222px; #right:-228px;\"><a href=\"javascript:void(0);\" onclick='fb.start({ href:\"files.php?action=popupAdd&project_id=".$project_id."\", rev:\"width:665 height:515 infoPos:tc disableScroll:true caption:`Add Attachment` doAnimations:false\" });'>Attach</a></div></div>\n";
$content .= "		<div>\n";
$content .= "			<table class=\"nt\" style=\"width=99%;\">\n";
$content .= "				<tr class=\"nt\" style=\"align:left;\">\n";
$content .= "					<td class=\"nt\" valign=\"top\"style=\"align: left; width: 1%;\"><b>Name:&nbsp;</b></td>\n";
$content .= "					<td class=\"nt\" style=\"align: left; width: 40%;\"><div style=\"white-space:normal;\">".nl2br(html_links($project_row['Project_Name']))."</div></td>\n";
$content .= "					<td class=\"nt\" valign=\"top\"style=\"align: left; width: 1%;\"><b>Lead:&nbsp;</b></td>\n";
$content .= "					<td class=\"nt\" style=\"align: left; width: 50%;\"><div style=\"white-space:normal;\">".$project_row['FirstName']." ".$project_row['LastName']."</div></td>\n";
$content .= "					<td class=\"nt\" style=\"text-align:right; color:#15A50D; font-size: 20px; font-weight: bold;\" rowspan=\"2\">".$project_pct."%</td>\n";
$content .= "				</tr>\n";
$content .= "				<tr class=\"nt\" style=\"align:left;\">\n";
$content .= "					<td class=\"nt\" valign=\"top\"><b>Description:&nbsp;</b></td>\n";
$content .= "					<td class=\"nt\" style=\"align: left; width: 100%;\" colspan=\"3\"><div style=\"white-space:normal;\">".nl2br(html_links($project_row['Description']))."</div></td>\n";
$content .= "				</tr>\n";
$content .= "			</table>\n";
$content .= "		</div>\n";
$content .= "	</div>\n";
$content .= "</fieldset>\n";

$content .= "<p>\n";

db_free_result($q);

/*********************************/
/*** PROJECT VIEW OF ALL TASKS ***/
/*********************************/
if ($_GET['action']=="showTasks") {
	// show task list that allows reordering

	$content .= "<fieldset>\n";
	$content .= "	<legend><span class=\"gl\">Milestones</span></legend>\n";
	$content .= "	<div>\n";
	$content .= "		<div style=\"position: absolute; width=100%;\"><div style=\"position: relative; display: inline; float: left; top:-25px; #top:-21; left:100px;\"><a href=\"javascript:void(0);\" onclick='fb.start({ href: \"tasks.php?action=organize&project_id=".$project_id."\", rev:\"width:650 height:430 infoPos:tc showClose:false disableScroll:true caption:`ORGANIZE Tasks` doAnimations:false\" });'>Organize</a></div></div>\n";
	$content .= "		<div style=\"position: absolute; width=100%;\"><div style=\"position: relative; display: inline; float: left; top:-25px; #top:-21; left:178px;\"><a href=\"javascript:void(0);\" onclick='fb.start({ href: \"tasks.php?action=popupAdd&project_id=".$project_id."\", rev:\"width:650 height:430 infoPos:tc showClose:false disableScroll:true caption:`NEW Milestone` doAnimations:false\" });'>New</a></div></div>\n";

	// query all tasks/subtasks
	//query to get the children for this project_id
	$SQL  = "SELECT * FROM tasks where project_ID=".$project_id." and parent_task_ID=0 ORDER BY order_num";
	$SQL  = "select t.*, e.FirstName, e.LastName from tasks t, employees e where e.employee_id=t.Assigned_To_ID and project_ID=".$project_id." and parent_task_ID=0 ORDER BY t.order_num";
	$q = db_query($SQL);

	//check for any tasks
	if (db_numrows($q) > 0) {
		//show all tasks
		$content .= "		<div>\n";
		$content .= "			<table style=\"width:99%\">\n";
		for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
			$content .= "				<tr>\n";
			$content .= "					<td><a href=\"javascript:void(0)\" onclick=\"SelectTask(".$row['task_ID'].");return false;\"><b>".$row['task_name']."</b></a></td>\n";
			$content .= "					<td>".$row['FirstName']." ".$row['LastName']."</div></td>\n";
			$content .= "					<td>".$row['PercentComplete']."%</td>\n";
			$content .= "				</tr>\n";
		}
		$content .= "			</table>\n";
		$content .= "		</div>\n";
	}
	$content .= "	</div>\n";
	$content .= "</fieldset>\n";

	db_free_result($q);

	//****************
	// Milestone Level
	//****************
} else if ($_GET['action']=="show") {

	if ($task_id>0) {
		$task_row = db_fetch_array(db_query("select t.*, e.FirstName, e.LastName from tasks t, employees e where e.employee_id=t.Assigned_To_ID and t.task_ID=".$task_id),0);
	}

	$milestone_pct = $task_row['PercentComplete'];

	// show specific task details
	$content .= "<fieldset>\n";
	$content .= "	<legend><span class=\"gl\">Milestone Details</span></legend>\n";
	$content .= "	<div>\n";
	$content .= "		<div style=\"position: absolute; width=100%;\"><div style=\"position:relative; display: inline; float: left; top:-25px; #top:-21; right:-178px;\"><a href=\"javascript:void(0);\" onclick='fb.start({ href: \"tasks.php?action=popupEdit&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:430 infoPos:tc showClose:false disableScroll:true caption:`EDIT Milestone` doAnimations:false\" });'>Edit</a></div></div>\n";
	$content .= "		<div>\n";
	$content .= "			<table class=\"nt\" style=\"width=100%;\">\n";
	$content .= "				<tr class=\"nt\" style=\"align:left;\">\n";
	$content .= "					<td class=\"nt\" valign=\"top\"style=\"align: left; width: 1%;\"><b>Name:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 40%;\"><div style=\"white-space:normal;\">".nl2br(html_links($task_row['task_name']))."</div></td>\n";
	$content .= "					<td class=\"nt\" valign=\"top\"style=\"align: left; width: 1%;\"><b>On Deck:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 50%;\"><div style=\"white-space:normal;\">".$task_row['FirstName']." ".$task_row['LastName']."</div></td>\n";
	$content .= "					<td class=\"nt\" style=\"text-align:right; color:#15A50D; font-size: 20px; font-weight: bold;\" rowspan=\"2\">".$milestone_pct."%</td>\n";
	$content .= "				</tr>\n";
	$content .= "				<tr class=\"nt\" style=\"align:left;\">\n";
	$content .= "					<td class=\"nt\" valign=\"top\"><b>Description:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 100%;\" colspan=\"3\"><div style=\"white-space:normal;\">".nl2br(html_links($task_row['Description']))."</div></td>\n";
	$content .= "				</tr>\n";
	$content .= "			</table>\n";
	$content .= "		</div>\n";
	$content .= "	</div>\n";
	$content .= "</fieldset>\n";

	$content .= "<p>\n";

	// show specific task subtasks
	$content .= "<fieldset>\n";
	$content .= "	<legend><span class=\"gl\">Task List</span></legend>\n";
	$content .= "	<div>\n";
	$content .= "		<div style=\"position: absolute; width=100%\"><div style=\"position:relative; display: inline; float: left; top:-25px; #top:-21; right:-100px;\"><a href=\"javascript:void(0);\" onclick='fb.start({ href: \"tasks.php?action=organize&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:430 infoPos:tc showClose:false disableScroll:true caption:`ORGANIZE Tasks` doAnimations:false\" });'>Organize</a></div></div>\n";
	$content .= "		<div style=\"position: absolute; width=100%\"><div style=\"position:relative; display: inline; float: left; top:-25px; #top:-21; right:-178px;\"><a href=\"javascript:void(0);\" onclick='fb.start({ href: \"tasks.php?action=popupAdd&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:430 infoPos:tc showClose:false disableScroll:true caption:`NEW Task` doAnimations:false\" });'>New</a></div></div>\n";

	// query all tasks/subtasks
	//query to get the children for this project_id
	$SQL  = "select t.*, e.FirstName, e.LastName from tasks t, employees e where e.employee_id=t.Assigned_To_ID and t.parent_task_ID=".$task_id." ORDER BY t.order_num";
	$q = db_query($SQL);

	//check for any tasks
	if (db_numrows($q) > 0) {
		$content .= "		<div>\n";
		$content .= "			<table style=\"width:95%\">\n";
		//show all tasks
		for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
			$content .= "			<tr>\n";
			$content .= "				<td><a href=\"javascript:void(0)\" onclick=\"SelectTask(".$row['task_ID'].");return false;\"><b>".$row['task_name']."</b></a></td>";
			$content .= "				<td>".$row['FirstName']." ".$row['LastName']."</div></td>\n";
			$content .= "				<td>".$row['PercentComplete']."%</td>\n";
			$content .= "			</tr>\n";
		}
		$content .= "			</table>\n";
		$content .= "		</div>\n";
	}
	$content .= "	</div>\n";
	$content .= "</fieldset>\n";

	db_free_result($q);

	//****************
	// Task Level
	//****************
} else if ($_GET['action']=="showSub") {
	$parent_task_id = db_simplequery("tasks","parent_task_ID","task_ID",$task_id);

	if ($parent_task_id>0) {
		//		$task_row = db_fetch_array(db_query("select * from tasks where task_ID=".$parent_task_id),0);
		$task_row = db_fetch_array(db_query("select t.*, e.FirstName, e.LastName from tasks t, employees e where e.employee_id=t.Assigned_To_ID and t.task_ID=".$parent_task_id),0);
	}
	$milestone_pct = $task_row['PercentComplete'];

	// show specific task details
	$content .= "<fieldset>\n";
	$content .= "	<legend><span class=\"gl\">Milestone Details</span></legend>\n";
	$content .= "	<div>\n";
	$content .= "		<div style=\"position: absolute; width=100%\"><div style=\"position:relative; display: inline; float: left; top:-25px; #top:-21; right:-178px;\"><a href=\"javascript:void(0);\" onclick='fb.start({ href: \"tasks.php?action=popupEdit&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:430 infoPos:tc showClose:false disableScroll:true caption:`EDIT Milestone` doAnimations:false\" });'>Edit</a></div></div>\n";
	$content .= "		<div>\n";
	$content .= "			<table class=\"nt\" style=\"width=100%;\">\n";
	$content .= "				<tr class=\"nt\" style=\"align:left;\">\n";
	$content .= "					<td class=\"nt\" valign=\"top\"style=\"align: left; width: 1%;\"><b>Name:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 40%;\"><div style=\"white-space:normal;\">".nl2br(html_links($task_row['task_name']))."</div></td>\n";
	$content .= "					<td class=\"nt\" valign=\"top\"style=\"align: left; width: 1%;\"><b>On Deck:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 50%;\"><div style=\"white-space:normal;\">".$task_row['FirstName']." ".$task_row['LastName']."</div></td>\n";
	$content .= "					<td class=\"nt\" style=\"text-align:right; color:#15A50D; font-size: 20px; font-weight: bold;\" rowspan=\"2\">".$milestone_pct."%</td>\n";
	$content .= "				</tr>\n";
	$content .= "				<tr class=\"nt\" style=\"align:left;\">\n";
	$content .= "					<td class=\"nt\" valign=\"top\"><b>Description:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 100%;\" colspan=\"3\"><div style=\"white-space:normal;\">".nl2br(html_links($task_row['Description']))."</div></td>\n";
	$content .= "				</tr>\n";
	$content .= "			</table>\n";
	$content .= "		</div>\n";
	$content .= "	</div>\n";
	$content .= "</fieldset>\n";

	$content .= "<p>\n";

	if ($task_id>0) {
		$subtask_row = db_fetch_array(db_query("select * from tasks where task_ID=".$task_id),0);
	}
	// show specific sub-task details
	$content .= "<fieldset>\n";
	$content .= "	<legend><span class=\"gl\">Task Details</span></legend>\n";
	$content .= "	<div>\n";
	$content .= "		<div style=\"position: absolute; width=100%\"><div style=\"position:relative; display: inline; float: left; top:-25px; #top:-21; right:-178px;\"><a href=\"javascript:void(0);\" onclick='fb.start({ href: \"tasks.php?action=popupEdit&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:430 infoPos:tc showClose:false disableScroll:true caption:`EDIT Task` doAnimations:false\" });'>Edit</a></div></div>\n";
	$content .= "		<div>\n";
	$content .= "			<table class=\"nt\" style=\"width=100%;\">\n";
	$content .= "				<tr class=\"nt\" style=\"align:left;\">\n";
	$content .= "					<td class=\"nt\" valign=\"top\"><b>Name:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 100%;\"><div style=\"white-space:normal;\">".nl2br(html_links($subtask_row['task_name']))."</div></td>\n";
	$content .= "				</tr>\n";
	$content .= "				<tr class=\"nt\" style=\"align:left;\">\n";
	$content .= "					<td class=\"nt\" valign=\"top\"><b>Description:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 100%;\"><div style=\"white-space:normal;\">".nl2br(html_links($subtask_row['Description']))."</div></td>\n";
	$content .= "				</tr>\n";
	$content .= "			</table>\n";
	$content .= "		</div>\n";
	$content .= "	</div>\n";
	$content .= "</fieldset>\n";

	$content .= "<p>\n";

	// show notes of sub-task
	$content .= "<fieldset>\n";
	$content .= "	<legend><span class=\"gl\">Notes</span></legend>\n";
	$content .= "	<div>\n";
	$content .= "		<div style=\"position: absolute; width=100%\"><div style=\"position:relative; display: inline; float: left; top:-25px; #top:-21; right:-178px;\"><a href=\"javascript:void(0);\" onclick='fb.start({ href: \"task_notes.php?action=popupAdd&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:530 infoPos:tc showClose:false disableScroll:true caption:`NEW Task Note` doAnimations:false\" });'>New</a></div></div>\n";

	//query to get the children for this project_id
	$SQL  = "SELECT * FROM task_notes where project_ID=".$project_id." and task_ID=".$task_id." ORDER BY TimeStamp desc";
	$q = db_query($SQL);

	//check for any tasks
	if (db_numrows($q) > 0) {
		$tmpcontent = "";
		$tmpcontent .= "<script type=\"text/javascript\">\n";
		$tmpcontent .= "$(document).ready(function(){\n";
		//show all notes
		for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
			$tmpcontent .= "	$(\"#note".$i."\").html(\"".mysql_escape_string($row['Note'])."\");\n";
			$content .= "<div><b>Entry Posted:</b> ".$row['TimeStamp']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Percent Complete:</b> ".$row['PercentComplete']."</div><pre id=\"note".$i."\" class=\"generalbox\"></pre>\n";
		}
		$tmpcontent .= "});\n";
		$tmpcontent .= "</script>\n";
		$content = $tmpcontent . $content;
	}
	db_free_result($q);

	$content .= "	</div>\n";
	$content .= "</fieldset>\n";
}
echo $content;

?>