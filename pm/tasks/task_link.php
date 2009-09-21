<?php
/* $Id: task_edit.php 28 2009-07-30 18:07:10Z mpemberton5@gmail.com $ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');
require_once(BASE.'includes/screen.php');
include_once(BASE.'includes/time.php');

//secure vars
$content = "";
$project_id = 0;
$task_id = 0;

if (!@safe_integer($_REQUEST['project_id'])) {
	error('Task Add/Edit', 'Not a valid value for project_id');
}
$project_id = $_REQUEST['project_id'];

if (!@safe_integer($_REQUEST['task_id'])) {
	error('Task Add/Edit', 'Not a valid value for task_id');
}
$task_id = $_REQUEST['task_id'];

if (isset($_REQUEST['milestone_id'])) {
	if (!@safe_integer($_REQUEST['milestone_id'])) {
		error('Task Add/Edit', 'Not a valid value for milestone_id');
	}
	$milestone_id = $_REQUEST['milestone_id'];
} else {
	$milestone_id = "0";
}

if ($_REQUEST['action'] == "popupRemovePL") {
	$content .= "<script>\n";
	$content .= "	$(document).ready(function() {\n";
	$content .= "		$(\"#del_btn\").click(function() {\n";
	$content .= "			// we want to store the values from the form input box, then send via ajax below\n";
	$content .= "			var parameters = 'action=deletePL&task_id=".$task_id."'\n";
	$content .= "			$.ajax({\n";
	$content .= "				type: \"POST\",\n";
	$content .= "				url: \"projects.php\",\n";
	$content .= "				data: parameters,\n";
	$content .= "				dataType: 'text',\n";
	$content .= "				error: function(xhr, ajaxOptions, thrownError){\n";
	$content .= "					parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
	$content .= "   			},\n";
	$content .= "				success: function(data){\n";
	// TODO: need to load milestone level - http://938-2shxrk1/pm/projects.php?action=show&project_id=34#tasks.php?action=showMilestoneLevel&project_id=34&task_id=180
	//$content .= "					parent.fb.loadPageOnClose='self';\n";
	$content .= "					parent.fb.loadPageOnClose='projects.php?action=show&project_id=".$project_id."&reload=1#tasks.php?action=showMilestoneLevel&project_id=".$project_id."&task_id=".$milestone_id."';\n";
//	$content .= "					parent.fb.loadPageOnClose='projects.php?action=show&project_id=1';\n";
	$content .= "					parent.fb.end(true);\n";
	$content .= "				}\n";
	$content .= "			});\n";
	$content .= "			return false;\n";
	$content .= "		});\n";
	$content .= "	});\n";
	$content .= "</script>\n";
	
	// Are you sure you want to remove Project Link?
	// NOTE: This will not remove the Child Project.
	$content .= "<br />\n";
	$content .= "<br />\n";
	$content .= "<div align=\"center\">\n";
	$content .= "	Are you SURE you want to remove the link to this Child Project?\n";
	//$content .= "	<button type=\"button\" href=\"javascript:void(0);\">Delete Link</button>\n";
	$content .= "	<input type=\"submit\" name=\"Submit\" class=\"button\" id=\"del_btn\" value=\"Delete Link\" />\n";
	$content .= "</div>\n";
	$content .= "<br />\n";
	$content .= "<div align=\"center\">\n";
	$content .= "	<span style=\"color:red;\">NOTE: This does NOT delete the project.  It simply removes the link to the project.</span>\n";
	$content .= "</div>\n";
	$content .= "<br />\n";
	
} else if ($_REQUEST['action'] == "popupAddPL") {
	
	$content .= "<br />\n";
	$content .= "<br />\n";
	$content .= "	<table style=\"width: 96%; margin-right:auto; margin-left:auto; \">\n";
	$content .= "		<tr style=\"align: center;\">\n";
	$content .= "			<td style=\"text-align: center\">\n";
	$content .= "				<button type=\"button\" style=\"width:140px;\" onclick='fb.start({ href: \"projects.php?action=popupAdd&parent_project_id=".$project_id."&parent_milestone_id=".$task_id."\", rev:\"sameBox:true width:665 height:515 infoPos:tc disableScroll:true caption:`NEW Project` doAnimations:false\" });'>Create Blank Project</button>\n";
	$content .= "			</td>";
	$content .= "			<td style=\"text-align: center\">\n";
	$content .= "				<button type=\"button\" style=\"width:200px;\" onclick='fb.start({ href: \"projects.php?action=popupLinkNew&parent_project_id=".$project_id."&parent_milestone_id=".$task_id."\", rev:\"sameBox:true width:665 height:515 infoPos:tc disableScroll:true caption:`Link NEW Project with Template` doAnimations:false\" });'>Create Project with Template</button>\n";
	$content .= "			</td>";
	$content .= "			<td style=\"text-align: center\">\n";
	$content .= "				<button type=\"button\" style=\"width:170px;\" onclick='fb.start({ href: \"projects.php?action=popupLinkExisting&parent_project_id=".$project_id."&parent_milestone_id=".$task_id."\", rev:\"sameBox:true width:665 height:515 infoPos:tc disableScroll:true caption:`Link EXISTING Project` doAnimations:false\" });'>Select Existing Project</button>\n";
	$content .= "			</td>";
	$content .= "		</tr>\n";
	$content .= "	</table>\n";
	$content .= "	<br />\n";
	
}

echo $content;

?>