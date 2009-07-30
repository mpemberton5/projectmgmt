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
$watched_project = db_result(db_query("select count(*) from user_prefs where user_ID=".$_SESSION['UID']." and pref_type='watchedProject' and value1=".$project_id." LIMIT 1"), 0, 0);

$content .= "<script type='text/javascript'>\n";
$content .= "function SelectTask(node){\n";
$content .= "	var task = 'task-' + node;\n";
$content .= "	$(\"#treev\").dynatree(\"getTree\").activateKey(task);\n";
$content .= "}\n";

$content .= "var timeout = 200;\n";
$content .= "var closetimer = 0;\n";
$content .= "var ddmenuitem = 0;\n";
$content .= "function jsddm_open() {\n";
$content .= "	jsddm_canceltimer();\n";
$content .= "	jsddm_close();\n";
$content .= "	ddmenuitem = $(this).find('ul').css('visibility', 'visible');\n";
$content .= "}\n";
$content .= "function jsddm_close() {\n";
$content .= "	if(ddmenuitem) ddmenuitem.css('visibility', 'hidden');\n";
$content .= "}\n";
$content .= "function jsddm_timer() {\n";
$content .= "	closetimer = window.setTimeout(jsddm_close, timeout);\n";
$content .= "}\n";
$content .= "function jsddm_canceltimer() {\n";
$content .= "	if(closetimer) {\n";
$content .= "		window.clearTimeout(closetimer);\n";
$content .= "		closetimer = null;\n";
$content .= "	}\n";
$content .= "}\n";
$content .= "$(document).ready(function() {\n";
$content .= "	$('.jsddm > li').bind('mouseover', jsddm_open);\n";
$content .= "	$('.jsddm > li').bind('mouseout', jsddm_timer);\n";
$content .= "});\n";
$content .= "document.onclick = jsddm_close;\n";

$content .= "function SaveWatch() {\n";
$content .= "	var parameters = 'action=submit_watch&user_id=".$_SESSION['UID']."&project_id=".$project_id."&watch_flag=' + document.getElementById('hiddenWatchedFlag').value;\n";
$content .= "	$.ajax({\n";
$content .= "		type: \"POST\",\n";
$content .= "		url: \"projects.php\",\n";
$content .= "		data: parameters,\n";
$content .= "		error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "			parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "   		},\n";
$content .= "		success: function(data){\n";
$content .= "			parent.fb.loadPageOnClose='self';\n";
$content .= "			parent.fb.end(true);\n";
$content .= "			if (document.getElementById('hiddenWatchedFlag').value==1) {\n";
$content .= "				document.getElementById('hiddenWatchedFlag').value=0;\n";
$content .= "				document.getElementById('watch_img').src='images/checkbox-small-unchecked.gif';\n";
$content .= "				$.jGrowl(\"Project removed from Watched List\");\n";
$content .= "			} else {\n";
$content .= "				document.getElementById('hiddenWatchedFlag').value=1;\n";
$content .= "				document.getElementById('watch_img').src='images/checkbox-small-checked.gif';\n";
$content .= "				$.jGrowl(\"Project added to Watched List\");\n";
$content .= "			}\n";
$content .= "			jsddm_close;\n";
$content .= "		}\n";
$content .= "	});\n";
$content .= "}\n";
$content .= "</script>\n";


/***********************************************/
// show project details
/***********************************************/
$content .= "<fieldset>\n";
$content .= "	<legend>\n";
$content .= "		<span class=\"gl\" style=\"width: 500px;\">Project Details</span>\n";
$content .= "	</legend>\n";
//$content .= "	<div>\n";
$content .= "		<div style=\"position: absolute; width: 1px;\">\n";
$content .= "			<div style=\"position: relative; float: left; top: -21px; right: -460px;\">\n";
$content .= "				<ul class=\"jsddm\">\n";
$content .= "					<li><a href=\"javascript:void(0)\">Action</a>\n";
$content .= "						<ul>\n";
$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href:\"projects.php?action=popupEdit&project_id=".$project_id."\", rev:\"width:665 height:515 infoPos:tc info:`feedback.php?currform=task_show.php-Edit Project Details` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`EDIT Project Details` doAnimations:false\" }); return false;'><img src='images/blank.gif' width='10px' height='10px'>&nbsp;&nbsp;Edit</a></li>\n";
$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href:\"files.php?action=popupAdd&project_id=".$project_id."\", rev:\"width:665 height:515 infoPos:tc info:`feedback.php?currform=task_show.php-Add Attachment` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`Add Attachment` doAnimations:false\" }); return false;'><img src='images/blank.gif' width='10px' height='10px'>&nbsp;&nbsp;Attachments</a></li>\n";
if ($watched_project) {
	$content .= "							<li id='watch'><a href=\"javascript:void(0)\" onClick=\"SaveWatch();return false;\"><img id='watch_img' src='images/checkbox-small-checked.gif'>&nbsp;&nbsp;Watch</a></li>\n";
} else {
	$content .= "							<li id='watch'><a href=\"javascript:void(0)\" onClick=\"SaveWatch();return false;\"><img id='watch_img' src='images/checkbox-small-unchecked.gif'>&nbsp;&nbsp;Watch</a></li>\n";
}
//$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href:\"projects.php?action=popupProjectPrint&project_id=".$project_id."\", rev:\"width:665 height:515 infoPos:tc info:`feedback.php?currform=task_show.php-Print` infoText:Feedback infoOptions:`width:555 height:350` showClose:false disableScroll:true caption:`Print Project` doAnimations:false\", title:\"TEST\" }); return false;'><img src='images/blank.gif' width='10px' height='10px'>&nbsp;&nbsp;Print</a></li>\n";
$content .= "						</ul>\n";
$content .= "					</li>\n";
$content .= "				</ul>\n";
$content .= "			</div>\n";
$content .= "		</div>\n";
//$content .= "		<div>\n";
$content .= "			<table class=\"nt\" style=\"width: 99%;\">\n";
$content .= "				<tr class=\"nt\" style=\"align:left;\">\n";
$content .= "					<td class=\"nt\" valign=\"top\" style=\"align: left; width: 1%;\"><b>Name:&nbsp;</b></td>\n";
$content .= "					<td class=\"nt\" style=\"align: left; width: 40%;\">\n";
$content .= "						<div style=\"white-space: normal;\">".nl2br(html_links($project_row['Project_Name']))."</div>\n";
$content .= "					</td>\n";
$content .= "					<td class=\"nt\" valign=\"top\" style=\"align: left; width: 1%;\"><b>Lead:&nbsp;</b></td>\n";
$content .= "					<td class=\"nt\" style=\"align: left; width: 50%;\">\n";
$content .= "						<div style=\"white-space: normal;\">".$project_row['FirstName']." ".$project_row['LastName']."</div>\n";
$content .= "					</td>\n";
$content .= "					<td class=\"nt\" style=\"text-align: right; color: #15A50D; font-size: 20px; font-weight: bold;\" rowspan=\"2\">".$project_pct."%</td>\n";
$content .= "				</tr>\n";
$content .= "				<tr class=\"nt\" style=\"align: left;\">\n";
$content .= "					<td class=\"nt\" valign=\"top\"><b>Description:&nbsp;</b></td>\n";
$content .= "					<td class=\"nt\" style=\"align: left; width: 100%;\" colspan=\"3\">\n";
$content .= "						<div style=\"white-space: normal;\">".nl2br(html_links($project_row['Description']))."</div>\n";
$content .= "					</td>\n";
$content .= "				</tr>\n";
$content .= "			</table>\n";
//$content .= "		</div>\n";
//$content .= "	</div>\n";
$content .= "</fieldset>\n";

$content .= "<p />\n";

db_free_result($q);

/*********************************/
/*** PROJECT VIEW OF ALL TASKS ***/
/*********************************/
if ($_GET['action']=="showTopLevel") {
	// show task list that allows reordering

	$content .= "<fieldset>\n";
	$content .= "	<legend><span class=\"gl\" style=\"width: 400px;\">Milestones</span></legend>\n";
//	$content .= "	<div>\n";
//	$content .= "		<div style=\"position: absolute; width: 1px;\">\n";
	$content .= "			<div style=\"position: relative; float: left; top: -21px; right: -360px;\">\n";
	$content .= "				<ul class=\"jsddm\">\n";
	$content .= "					<li><a href=\"javascript:void(0)\">Action</a>\n";
	$content .= "						<ul>\n";
	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=organize&project_id=".$project_id."\", rev:\"width:650 height:430 infoPos:tc info:`feedback.php?currform=task_show.php-Organize Milestones` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`ORGANIZE Milestones` doAnimations:false\" }); return false;'>Organize</a></li>\n";
	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupAdd&project_id=".$project_id."\", rev:\"width:650 height:430 infoPos:tc info:`feedback.php?currform=task_show.php-New Milestone` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`NEW Milestone` doAnimations:false\" }); return false;'>New</a></li>\n";
//	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupMilestonesPrint&project_id=".$project_id."\", rev:\"width:650 height:430 infoPos:tc disableScroll:true caption:`PRINT All Milestones` doAnimations:false\" }); return false;'>Print</a></li>\n";
	$content .= "						</ul>\n";
	$content .= "					</li>\n";
	$content .= "				</ul>\n";
	$content .= "			</div>\n";
//	$content .= "		</div>\n";

	// query all tasks/subtasks
	//query to get the children for this project_id
	$SQL  = "SELECT * FROM tasks where project_ID=".$project_id." and parent_task_ID=0 ORDER BY order_num";
	$SQL  = "select t.*, e.FirstName, e.LastName from tasks t, employees e where e.employee_id=t.Assigned_To_ID and project_ID=".$project_id." and parent_task_ID=0 ORDER BY t.order_num";
	$q = db_query($SQL);

	//check for any tasks
	if (db_numrows($q) > 0) {
		//show all tasks
//		$content .= "		<div>\n";
		$content .= "			<table style=\"width: 99%\">\n";
		for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
			$content .= "				<tr>\n";
			$content .= "					<td><a href=\"javascript:void(0)\" onclick=\"SelectTask(".$row['task_ID'].");return false;\"><b>".$row['task_name']."</b></a></td>\n";
			$content .= "					<td>".$row['FirstName']." ".$row['LastName']."</div></td>\n";
			$content .= "					<td>".$row['PercentComplete']."%</td>\n";
			$content .= "				</tr>\n";
		}
		$content .= "			</table>\n";
//		$content .= "		</div>\n";
	}
//	$content .= "	</div>\n";
	$content .= "</fieldset>\n";

	db_free_result($q);

	//****************
	// Milestone Level
	//****************
} else if ($_GET['action']=="showMilestoneLevel") {

	if ($task_id>0) {
		$milestone_row = db_fetch_array(db_query("select t.*, e.FirstName, e.LastName from tasks t, employees e where e.employee_id=t.Assigned_To_ID and t.task_ID=".$task_id),0);
	}
	// now get the current task details
	if ($milestone_row['Curr_Task_ID']>0) {
		$currTaskName = db_simplequery("tasks","task_name","task_ID",$milestone_row['Curr_Task_ID']);
	} else {
		$currTaskName = "";
	}

	// show specific task details
	$content .= "<fieldset>\n";
	$content .= "	<legend><span class=\"gl\" style=\"width: 400px;\">Milestone Details</span></legend>\n";
//	$content .= "	<div>\n";
	$content .= "		<div style=\"position: absolute; width: 1px;\">\n";
	$content .= "			<div style=\"position: relative; float: left; top: -21px; right: -360px;\">\n";
	$content .= "				<ul class=\"jsddm\">\n";
	$content .= "					<li><a href=\"javascript:void(0)\">Action</a>\n";
	$content .= "						<ul>\n";
	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupEdit&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:430 infoPos:tc info:`feedback.php?currform=task_show.php-Edit Milestone` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`EDIT Milestone` doAnimations:false\" }); return false;'>Edit</a></li>\n";
	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupAdd&project_id=".$project_id."\", rev:\"width:650 height:430 infoPos:tc info:`feedback.php?currform=task_show.php-New Milestone` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`NEW Milestone` doAnimations:false\" }); return false;'>New</a></li>\n";
//	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupMilestonePrint&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:430 infoPos:tc disableScroll:true caption:`Print Milestone` doAnimations:false\" }); return false;'>Print</a></li>\n";
	$content .= "						</ul>\n";
	$content .= "					</li>\n";
	$content .= "				</ul>\n";
	$content .= "			</div>\n";
	$content .= "		</div>\n";
//	$content .= "		<div>\n";
	$content .= "			<table class=\"nt\" style=\"width: 99%;\">\n";
	$content .= "				<tr class=\"nt\" style=\"align: left;\">\n";
	$content .= "					<td class=\"nt\" valign=\"top\" style=\"align: left; width: 1%;\"><b>Name:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 40%;\"><div style=\"white-space:normal;\">".nl2br(html_links($milestone_row['task_name']))."</div></td>\n";
	$content .= "					<td class=\"nt\" valign=\"top\" style=\"align: left; width: 1%;\"><b>Current Task:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 50%;\"><div style=\"white-space:normal;\">".$currTaskName."</div></td>\n";
	$content .= "					<td class=\"nt\" style=\"text-align: right; color: #15A50D; font-size: 20px; font-weight: bold;\" rowspan=\"2\">".$milestone_row['PercentComplete']."%</td>\n";
	$content .= "				</tr>\n";
	$content .= "				<tr class=\"nt\" style=\"align: left;\">\n";
	$content .= "					<td class=\"nt\" valign=\"top\"><b>Description:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 100%;\" colspan=\"3\"><div style=\"white-space:normal;\">".nl2br(html_links($milestone_row['Description']))."</div></td>\n";
	$content .= "				</tr>\n";
	$content .= "			</table>\n";
//	$content .= "		</div>\n";
//	$content .= "	</div>\n";
	$content .= "</fieldset>\n";

	$content .= "<p />\n";

	// show specific task subtasks
	$content .= "<fieldset>\n";
	$content .= "	<legend><span class=\"gl\" style=\"width: 300px;\">Task List</span></legend>\n";
//	$content .= "	<div>\n";
//	$content .= "		<div style=\"position: absolute; width: 1px;\">\n";
	$content .= "			<div style=\"position: relative; float: left; top: -21px; right: -260px;\">\n";
	$content .= "				<ul class=\"jsddm\">\n";
	$content .= "					<li><a href=\"javascript:void(0)\">Action</a>\n";
	$content .= "						<ul>\n";
	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=organize&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:430 infoPos:tc info:`feedback.php?currform=task_show.php-Organize Tasks` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`ORGANIZE Tasks` doAnimations:false\" }); return false;'>Organize</a></li>\n";
	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupAdd&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:430 infoPos:tc info:`feedback.php?currform=task_show.php-Edit Tasks` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`NEW Task` doAnimations:false\" }); return false;'>New</a></li>\n";
//	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupTasksPrint&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:430 infoPos:tc disableScroll:true caption:`PRINT All Tasks` doAnimations:false\" }); return false;'>Print</a></li>\n";
	$content .= "						</ul>\n";
	$content .= "					</li>\n";
	$content .= "				</ul>\n";
	$content .= "			</div>\n";
//	$content .= "		</div>\n";

	// query all tasks/subtasks
	//query to get the children for this project_id
	$SQL  = "select t.*, e.FirstName, e.LastName from tasks t, employees e where e.employee_id=t.Assigned_To_ID and t.parent_task_ID=".$task_id." ORDER BY t.order_num";
	$q = db_query($SQL);

	//check for any tasks
	if (db_numrows($q) > 0) {
//		$content .= "		<div>\n";
		$content .= "			<table style=\"width: 99%\">\n";
		//show all tasks
		for ($i=0; $task_row = @db_fetch_array($q, $i); ++$i) {
			$content .= "			<tr>\n";
			$content .= "				<td><a href=\"javascript:void(0)\" onclick=\"SelectTask(".$task_row['task_ID'].");return false;\"><b>".$task_row['task_name']."</b></a></td>";
			$content .= "				<td>".$task_row['FirstName']." ".$task_row['LastName']."</div></td>\n";
			$content .= "				<td>".$task_row['PercentComplete']."%</td>\n";
			$content .= "			</tr>\n";
		}
		$content .= "			</table>\n";
//		$content .= "		</div>\n";
	}
//	$content .= "	</div>\n";
	$content .= "</fieldset>\n";

	db_free_result($q);

	//****************
	// Task Level
	//****************
} else if ($_GET['action']=="showTaskLevel") {
	$milestone_ID = db_simplequery("tasks","parent_task_ID","task_ID",$task_id);

	if ($milestone_ID>0) {
		$milestone_row = db_fetch_array(db_query("select t.*, e.FirstName, e.LastName from tasks t, employees e where e.employee_id=t.Assigned_To_ID and t.task_ID=".$milestone_ID),0);
	}

	// now get the current task details
	$currTaskName = db_simplequery("tasks","task_name","task_ID",$milestone_row['Curr_Task_ID']);

	// show specific task details
	$content .= "<fieldset>\n";
	$content .= "	<legend><span class=\"gl\" style=\"width: 400px;\">Milestone Details</span></legend>\n";
//	$content .= "	<div>\n";
	$content .= "		<div style=\"position: absolute; width: 1px;\">\n";
	$content .= "			<div style=\"position: relative; float: left; top: -21px; right: -360px;\">\n";
	$content .= "				<ul class=\"jsddm\">\n";
	$content .= "					<li><a href=\"javascript:void(0)\">Action</a>\n";
	$content .= "						<ul>\n";
	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupEdit&project_id=".$project_id."&task_id=".$milestone_ID."\", rev:\"width:650 height:430 infoPos:tc info:`feedback.php?currform=task_show.php=Edit Milestone` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`EDIT Milestone` doAnimations:false\" }); return false;'>Edit</a></li>\n";
	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupAdd&project_id=".$project_id."\", rev:\"width:650 height:430 infoPos:tc info:`feedback.php?currform=task_show.php-New Milestone` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`NEW Milestone` doAnimations:false\" }); return false;'>New</a></li>\n";
//	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupMilestonePrint&project_id=".$project_id."&task_id=".$milestone_ID."\", rev:\"width:650 height:430 infoPos:tc disableScroll:true caption:`PRINT Milestone` doAnimations:false\" }); return false;'>Print</a></li>\n";
	$content .= "						</ul>\n";
	$content .= "					</li>\n";
	$content .= "				</ul>\n";
	$content .= "			</div>\n";
	$content .= "		</div>\n";
//	$content .= "		<div>\n";
	$content .= "			<table class=\"nt\" style=\"width: 99%;\">\n";
	$content .= "				<tr class=\"nt\" style=\"align: left;\">\n";
	$content .= "					<td class=\"nt\" valign=\"top\" style=\"align: left; width: 1%;\"><b>Name:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 40%;\"><div style=\"white-space:normal;\">".nl2br(html_links($milestone_row['task_name']))."</div></td>\n";
	$content .= "					<td class=\"nt\" valign=\"top\" style=\"align: left; width: 1%;\"><b>Current Task:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 50%;\"><div style=\"white-space:normal;\">".$currTaskName."</div></td>\n";
	$content .= "					<td class=\"nt\" style=\"text-align: right; color: #15A50D; font-size: 20px; font-weight: bold;\" rowspan=\"2\">".$milestone_row['PercentComplete']."%</td>\n";
	$content .= "				</tr>\n";
	$content .= "				<tr class=\"nt\" style=\"align: left;\">\n";
	$content .= "					<td class=\"nt\" valign=\"top\"><b>Description:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 100%;\" colspan=\"3\"><div style=\"white-space:normal;\">".nl2br(html_links($milestone_row['Description']))."</div></td>\n";
	$content .= "				</tr>\n";
	$content .= "			</table>\n";
//	$content .= "		</div>\n";
//	$content .= "	</div>\n";
	$content .= "</fieldset>\n";

	$content .= "<p />\n";

	if ($task_id>0) {
		$task_row = db_fetch_array(db_query("select * from tasks where task_ID=".$task_id),0);
	}
	// show specific sub-task details
	$content .= "<fieldset>\n";
	$content .= "	<legend><span class=\"gl\" style=\"width: 300px;\">Task Details</span></legend>\n";
//	$content .= "	<div>\n";
	$content .= "		<div style=\"position: absolute; width: 1px;\">\n";
	$content .= "			<div style=\"position: relative; float: left; top: -21px; right: -260px;\">\n";
	$content .= "				<ul class=\"jsddm\">\n";
	$content .= "					<li><a href=\"javascript:void(0)\">Action</a>\n";
	$content .= "						<ul>\n";
	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupEdit&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:430 infoPos:tc info:`feedback.php?currform=task_show.php-Edit Task` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`EDIT Task` doAnimations:false\" }); return false;'>Edit</a></li>\n";
	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupAdd&project_id=".$project_id."&task_id=".$milestone_ID."\", rev:\"width:650 height:430 infoPos:tc info:`feedback.php?currform=task_show.php-New Task` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`NEW Task` doAnimations:false\" }); return false;'>New</a></li>\n";
//	$content .= "							<li><a href=\"javascript:void(0)\" onclick='fb.start({ href: \"tasks.php?action=popupTaskPrint&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:650 height:430 infoPos:tc disableScroll:true caption:`PRINT Task` doAnimations:false\" }); return false;'>Print</a></li>\n";
	$content .= "						</ul>\n";
	$content .= "					</li>\n";
	$content .= "				</ul>\n";
	$content .= "			</div>\n";
	$content .= "		</div>\n";
//	$content .= "		<div>\n";
	$content .= "			<table class=\"nt\" style=\"width: 99%;\">\n";
	$content .= "				<tr class=\"nt\" style=\"align: left;\">\n";
	$content .= "					<td class=\"nt\" valign=\"top\"><b>Name:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 100%;\"><div style=\"white-space:normal;\">".nl2br(html_links($task_row['task_name']))."</div></td>\n";
	$content .= "				</tr>\n";
	$content .= "				<tr class=\"nt\" style=\"align: left;\">\n";
	$content .= "					<td class=\"nt\" valign=\"top\"><b>Description:&nbsp;</b></td>\n";
	$content .= "					<td class=\"nt\" style=\"align: left; width: 100%;\"><div style=\"white-space:normal;\">".nl2br(html_links($task_row['Description']))."</div></td>\n";
	$content .= "				</tr>\n";
	$content .= "			</table>\n";
//	$content .= "		</div>\n";
//	$content .= "	</div>\n";
	$content .= "</fieldset>\n";

	$content .= "<p />\n";

	// show notes of sub-task
	$content .= "<fieldset>\n";
	$content .= "	<legend><span class=\"gl\" style=\"width: 200px;\">Notes</span></legend>\n";
//	$content .= "	<div>\n";
	$content .= "		<div style=\"position: absolute; width: 1px;\">\n";
	$content .= "			<div style=\"position:relative; float: left; top: -21px; right: -160px;\">\n";
	$content .= "				<a href=\"javascript:void(0);\" onclick='fb.start({ href: \"task_notes.php?action=popupAdd&project_id=".$project_id."&task_id=".$task_id."\", rev:\"width:670 height:470 infoPos:tc info:`feedback.php?currform=task_show.php-New Task Note` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`NEW Task Note` doAnimations:false\" }); return false;'>New</a>\n";
	$content .= "			</div>\n";
	$content .= "		</div>\n";

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
			if ($row['user_ID']>0) {
				$posted_by = db_simplequery("employees","CONCAT(LastName,', ',FirstName)","employee_ID",$row['user_ID']);
			} else {
				$posted_by = "SYSTEM";
			}

			$tmpcontent .= "	$(\"#note".$i."\").html(\"".mysql_escape_string($row['Note'])."\");\n";
			$content .= "<div><b>Entry Posted:</b> ".$row['TimeStamp']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Percent Complete:</b> ".$row['PercentComplete']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Posted By:</b> ".$posted_by."</div>\n";
			$content .= "<pre style=\"white-space:normal;\" id=\"note".$i."\" class=\"generalbox\"></pre>\n";
		}
		$tmpcontent .= "});\n";
		$tmpcontent .= "</script>\n";
		$content = $tmpcontent . $content;
	}
	db_free_result($q);

//	$content .= "	</div>\n";
	$content .= "</fieldset>\n";
}
$content .= "<input type=\"hidden\" id=\"hiddenWatchedFlag\" value=\"".$watched_project."\" />\n";
echo $content;

?>