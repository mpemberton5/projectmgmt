<?php
/* $Id$ */

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
$parent_task_id = 0;

if (!@safe_integer($_REQUEST['project_id'])) {
	error('Task Add/Edit', 'Not a valid value for project_id');
}
$project_id = $_REQUEST['project_id'];

if ($_REQUEST['action'] == "popupEdit") {

	$task_id = 0;
	if (@safe_integer($_REQUEST['task_id'])) {
		$task_id = $_REQUEST['task_id'];
	}

	if ($task_id == 0) {
		error('Task edit', 'The requested item has either been deleted, or is now invalid.');
	}

	//query to get the children for this project_id
	$q = db_query('SELECT * FROM tasks WHERE project_id='.$project_id.' AND task_id='.$task_id.' LIMIT 1');

	//check for any posts
	if (db_numrows($q) < 1) {
		error("Task Edit", "Unable to Find Task Details");
	}

	//get the data
	if (!($row = db_fetch_array($q, 0))) {
		error('Task edit', 'The requested item has either been deleted, or is now invalid.');
	}

	$form_submit = "submit_update";
	if (empty($row['Start_Date']) or $row['Start_Date']==="0000-00-00") {
		$start_date = "";
	} else {
		$start_date = date('m-d-Y',strtotime($row['Start_Date']));
	}
	if (empty($row['End_Date']) or $row['End_Date']==="0000-00-00") {
		$end_date = "";
	} else {
		$end_date = date('m-d-Y',strtotime($row['End_Date']));
	}
	$parent_task_id = $row['parent_task_ID'];
	$task_name = html_escape($row['task_name']);
	$task_weight = $row['weight'];
	$priority = $row['Priority'];
	$status = $row['Status'];
	$pct_comp = $row['PercentComplete'];
	//	$c_type = "";
	$assigned_to = $row['Assigned_To_ID'];
	//  $assigned_to_desc = db_result(db_query('SELECT CONCAT(firstname," ",lastname) as name FROM contacts WHERE id='.$row['assigned_to']),0,0);
	//	$c_name = $assigned_to_desc;
	//	if ($assigned_to_desc == "") $assigned_to_desc = "None";
	$description = $row['Description'];

	db_free_result($q);

} else if ($_REQUEST['action'] == "popupAdd") {

	$task_id = 0;
	if (@safe_integer($_REQUEST['task_id'])) {
		$parent_task_id = $_REQUEST['task_id'];
	}

	$form_submit = "submit_insert";
	$start_date = date('m-d-Y');
	$end_date = "";
	$task_name = "";
	$task_weight="1";
	$priority = "Normal";
	$status = "Active";
	$pct_comp = "0";
	$assigned_to = $_SESSION['UID'];
	$description = "";
}

// START CONTEXT

$content .= "<script type='text/javascript'>\n";
$content .= "$(function() {\n";

$content .= "	$(\"#submit_btn\").click(function() {\n";
$content .= "	// we want to store the values from the form input box, then send via ajax below\n";
$content .= "	var parameter1 = $(\"input\").serialize();\n";
$content .= "	var parameter2 = $(\"textarea\").serialize();\n";
$content .= "	var parameter3 = $(\"select\").serialize();\n";
$content .= "	var parameters = parameter1 + '&' + parameter2 + '&' + parameter3;\n";
//$content .= " alert(parameters);\n";
$content .= "		$.ajax({\n";
$content .= "			type: \"POST\",\n";
$content .= "			url: \"tasks.php\",\n";
$content .= "			data: parameters,\n";
$content .= "			error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "				parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "    		},\n";
$content .= "			success: function(data){\n";
$content .= "				parent.fb.loadPageOnClose='self';\n";
$content .= "				parent.fb.end(true);\n";
$content .= "			}\n";
$content .= "		});\n";
$content .= "	return false;\n";
$content .= "	});\n";

$content .= "	$(\"#discard_btn\").click(function() {\n";
if ($parent_task_id>0) {
	$content .= "		var answer = confirm(\"Are you sure you want to Discard this Task?\")\n";
} else {
	$content .= "		var answer = confirm(\"Are you sure you want to Discard this Milestone?\")\n";
}
$content .= "		if (answer) {\n";
$content .= "			$.ajax({\n";
$content .= "				type: \"POST\",\n";
$content .= "				url: \"tasks.php\",\n";
$content .= "				data: 'action=submit_delete&task_id=".$task_id."&parent_task_id=".$parent_task_id."',\n";
$content .= "				dataType: 'text',\n";
$content .= "				error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "					parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "   				},\n";
$content .= "				success: function(data){\n";
$content .= "					parent.fb.loadPageOnClose='projects.php?action=show&project_id=".$project_id."';\n";
$content .= "					parent.fb.end(true);\n";
$content .= "				}\n";
$content .= "			});\n";
$content .= "		}\n";
$content .= "		return false;\n";
$content .= "	});\n";

// SLIDER JQUERY CODE
$content .= "	$.extend($.ui.slider.defaults, {\n";
$content .= "		range: \"min\",\n";
$content .= "		animate: true,\n";
$content .= "		orientation: \"vertical\"\n";
$content .= "	});\n";

$content .= "	$(\".slide\").each(function() {\n";
$content .= "		// read initial values from markup and remove that\n";
$content .= "		var value = parseInt($(this).text());\n";
$content .= "		$(this).empty();\n";
$content .= "		$(this).slider({\n";
$content .= "			slide: function(event, ui) { $('#taskw-'+$(this).attr('id').substr(7)).val(ui.value); },\n";
$content .= "			value: value, step:1, min:1, max:10,\n";
$content .= "			orientation: \"horizontal\"\n";
$content .= "		})\n";
$content .= "	});\n";
/*
$content .= "	$(\"#startdate\").datepicker({\n";
$content .= "		dateFormat: 'mm-dd-yy',\n";
$content .= "		showOn: 'button',\n";
$content .= "		showButtonPanel: true,\n";
$content .= "		buttonImage: '/public/jquery/development-bundle/demos/datepicker/images/calendar.gif',\n";
$content .= "		buttonImageOnly: true,\n";
$content .= "		beforeShow: function (i, e) {\n";
$content .= "			e.dpDiv.css('z-index', '10000');\n";
$content .= "		}\n";
$content .= "	});\n";

$content .= "	$(\"#enddate\").datepicker({\n";
$content .= "		dateFormat: 'mm-dd-yy',\n";
$content .= "		showOn: 'button',\n";
$content .= "		showButtonPanel: true,\n";
$content .= "		buttonImage: '/public/jquery/development-bundle/demos/datepicker/images/calendar.gif',\n";
$content .= "		buttonImageOnly: true,\n";
$content .= "		beforeShow: function (i, e) {\n";
$content .= "			e.dpDiv.css('z-index', '10000');\n";
$content .= "		}\n";
$content .= "	});\n";
*/
$content .= "});\n";

$content .= "</script>\n";

$content .= "<link type='text/css' rel='stylesheet' href='/public/slider/css/redmond/jquery-ui-1.7.1.custom.css'>\n";
$content .= "<link type='text/css' rel='stylesheet' href='/public/slider/css/ui.slider.extras.css'>\n";

$content .= '<!-- calendar stylesheet -->';
$content .= '<link rel="stylesheet" type="text/css" media="all" href="/public/jscalendar-1.0/calendar-win2k-cold-1.css" title="win2k-cold-1" />';

$content .= '<!-- main calendar program -->';
$content .= '<script type="text/javascript" src="/public/jscalendar-1.0/calendar.js"></script>';

$content .= '<!-- language for the calendar -->';
$content .= '<script type="text/javascript" src="/public/jscalendar-1.0/lang/calendar-en.js"></script>';

$content .= '<!-- the following script defines the Calendar.setup helper function, which makes';
$content .= '     adding a calendar a matter of 1 or 2 lines of code. -->';
$content .= '<script type="text/javascript" src="/public/jscalendar-1.0/calendar-setup.js"></script>';

//all okay show task info
//$content .= "<div class=\"container\">\n";
$content .= "<form action=\"\" name=\"UpdateForm\" method=\"post\">\n";
$content .= "<input type=\"hidden\" name=\"action\" value=\"".$form_submit."\" />\n";
$content .= "<input type=\"hidden\" name=\"project_id\" value=\"".$project_id."\" />\n";
$content .= "<input type=\"hidden\" name=\"task_id\" value=\"".$task_id."\" />\n";
$content .= "<input type=\"hidden\" name=\"parent_task_id\" value=\"".$parent_task_id."\" />\n";

$content .= "<table style=\"width:100%\">\n";
$content .= "<tr>\n";
if ($parent_task_id>0) {
	$content .= "	<td>Task Name:</td>\n";
} else {
	$content .= "	<td>Milestone Name:</td>\n";
}
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"name\" type=\"text\" name=\"name\" size=\"30\" value=\"".$task_name."\" />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";
$content .= "<tr>\n";
$content .= "	<td>Start Date:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id='startdate' name='startdate' type='text' size='12' value=\"".$start_date."\" />\n";
$content .= "		<img src='/public/jquery/development-bundle/demos/datepicker/images/calendar.gif' id='startdate_img' style='cursor: pointer;' title='Date selector' />\n";
$content .= "	</td>\n";
$content .= "</tr>";
$content .= "<tr>\n";
$content .= "	<td>End Date:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id='enddate' name='enddate' type='text' size='12' value=\"".$end_date."\" />\n";
$content .= "		<img src='/public/jquery/development-bundle/demos/datepicker/images/calendar.gif' id='enddate_img' style='cursor: pointer;' title='Date selector' />\n";
$content .= "	</td>\n";
$content .= "</tr>";

if ($parent_task_id>0) {
	$content .= "<tr>\n";
	$content .= "	<td>Priority:</td>\n";
	$content .= "	<td style=\"width:100%\">\n";
	$content .= "		<select name=\"priority\">\n";
	$content .= "			<option value=\"Low\"".(($priority=='Low') ? ' selected=\'selected\'' : '') .">Low</option>\n";
	$content .= "			<option value=\"Normal\"".(($priority=='Normal') ? ' selected=\'selected\'' : '') .">Normal</option>\n";
	$content .= "			<option value=\"High\"".(($priority=='High') ? ' selected=\'selected\'' : '') .">High</option>\n";
	$content .= "		</select>\n";
	$content .= "	</td>\n";
	$content .= "</tr>\n";

	$content .= "<tr>\n";
	$content .= "	<td>Status:</td>\n";
	$content .= "	<td style=\"width:100%\">\n";
	$content .= "			<select name=\"status\">\n";
	$content .= "			<option value=\"Planning\"".(($status=='Planning') ? ' selected=\'selected\'' : '') .">Planning</option>\n";
	$content .= "			<option value=\"Active\"".(($status=='Active') ? ' selected=\'selected\'' : '') .">Active</option>\n";
	$content .= "			<option value=\"On Hold\"".(($status=='On Hold') ? ' selected=\'selected\'' : '') .">On Hold</option>\n";
	$content .= "			<option value=\"Archived\"".(($status=='Archived') ? ' selected=\'selected\'' : '') .">Archived</option>\n";
	$content .= "			<option value=\"Cancelled\"".(($status=='Cancelled') ? ' selected=\'selected\'' : '') .">Cancelled</option>\n";
	$content .= "			<option value=\"Complete\"".(($status=='Complete') ? ' selected=\'selected\'' : '') .">Complete</option>\n";
	$content .= "		</select>\n";
	$content .= "	</td>\n";
	$content .= "</tr>\n";

	$content .= "	<tr>\n";
	$content .= "		<td><div class=\"txtmaxsize\" style=\"width:100%\">Weight of Task:</div></td>\n";
	$content .= "		<td>\n";
	$content .= "			<div style=\"position: relative;\">\n";
	$content .= "				<div style=\"float: right;\"><input id=\"taskw-".$task_id."\" type=\"text\" readonly size=\"1\" name=\"weight\" value=\"".$task_weight."\" /></div>\n";
	$content .= "				<div class=\"slide\" id=\"slider-".$task_id."\" style=\"float:left; width:260px; margin:5px;\">".$task_weight."</div>\n";
	$content .= "			</div>\n";
	$content .= "		</td>\n";
	$content .= "	</tr>\n";

	//task assigned_to
	$q = db_query('SELECT * FROM employees WHERE Department_ID=(select emp.Department_ID from employees emp where emp.employee_ID='.$_SESSION['UID'].') ORDER BY LastName,FirstName');
	
	//select contact
	$content .= "<tr>\n";
	$content .= "	<td>Assigned To:</td>\n";
	$content .= "	<td style=\"width:100%\">\n";
	$content .= "		<select name=\"assigned_to\">\n";
	for ($i=0; $user_row = @db_fetch_array($q, $i); ++$i) {
	
		$content .= "			<option value=\"".$user_row['employee_ID']."\"";
	
		if ($user_row['employee_ID'] == $assigned_to) {
			$content .= " selected=\"selected\"";
		}
		$content .= ">".$user_row['LastName'].", ".$user_row['FirstName']."</option>\n";
	}
	$content .= "		</select>\n";
	$content .= "	</td>\n";
	$content .= "</tr>\n";
}

$content .=  "<tr>\n";
$content .= "	<td style=\"vertical-align: top\">Description</td><td style=\"width:100%\">\n";
$content .= "		<textarea style=\"width: 100%\" name=\"text\" rows=\"5\">".$description."</textarea>\n";
$content .= "	</td>\n";
$content .= "</tr>\n";
$content .= "</table>\n";

$content .= "<p />\n";

$content .= "<div align=\"center\">\n";
$content .= "	<input type=\"submit\" name=\"submit\" class=\"button\" id=\"submit_btn\" value=\"Save\" />\n";
$content .= "	&nbsp;&nbsp;&nbsp;\n";
$content .= "	<input type=\"button\" value=\"Cancel\" onClick=\"parent.fb.end(true); return false;\" />\n";
if ($task_id>0) {
	if ($parent_task_id>0) {
		$SQL = 'SELECT COUNT(*) FROM task_notes WHERE task_ID='.$task_id.' LIMIT 1';
	} else {
		$SQL = 'SELECT COUNT(*) FROM task_notes WHERE task_ID in (select task_ID from tasks where parent_task_ID='.$task_id.') LIMIT 1';
	}
	if (db_result(db_query($SQL), 0, 0) > 0) {
		$content .= "	&nbsp;&nbsp;&nbsp;\n";
		$content .= "	<input type=\"submit\" name=\"Discard\" title=\"help\" disabled=\"disabled\" class=\"button\" id=\"discard_btn\" value=\"Delete\" /> Unable to delete with task notes.\n";
	} else {
		$content .= "	&nbsp;&nbsp;&nbsp;\n";
		$content .= "	<input type=\"submit\" name=\"Discard\" class=\"button\" id=\"discard_btn\" value=\"Delete\" />\n";
	}
}
$content .= "</div>\n";

$content .= "</form>\n";
//$content .= "</div>\n";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "	var mytext = document.getElementById('name');\n";
$content .= "	mytext.focus();\n";

$content .= "Calendar.setup({\n";
$content .= "	inputField	: 'startdate',\n";     // id of the input field
$content .= "	ifFormat	: '%m-%d-%Y',\n";    // format of the input field
$content .= "	button		: 'startdate_img',\n";  // trigger for the calendar (button ID)
$content .= "	weekNumbers	: false,\n";
$content .= "	singleClick	: true\n";
$content .= "});\n";
$content .= "Calendar.setup({\n";
$content .= "	inputField	: 'enddate',\n";     // id of the input field
$content .= "	ifFormat	: '%m-%d-%Y',\n";    // format of the input field
$content .= "	button		: 'enddate_img',\n";  // trigger for the calendar (button ID)
$content .= "	weekNumbers	: false,\n";
$content .= "	singleClick	: true\n";
$content .= "});\n";

$content .= "</script>\n";

echo $content;

?>