<?php
/* $Id$ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/time.php');

$content = '';
$javascript = '';

if ($_REQUEST['action'] == "popupEdit") {
	if (!@safe_integer($_GET['project_id'])) {
		error('Project edit', 'The project_id input is not valid');
	}
	$project_id = $_GET['project_id'];

	//get project details - if any
	$q = db_query('SELECT * FROM projects WHERE project_id='.$project_id.' LIMIT 1');

	//check for any posts
	if (db_numrows($q) < 1) {
		error("Project Edit", "Unable to Find Message");
	}

	if (!$project_row = db_fetch_array($q, 0)) {
		error("Project Edit", "Unable to Find Message");
	}

	$form_submit = "submit_update";
	$return_page = "				parent.fb.loadPageOnClose='self';\n";
	$project_name = $project_row['Project_Name'];
	$client_id = $project_row['Client_ID'];
	$owner_id = $project_row['Owner_ID'];
	if (empty($project_row['StartDate']) or $project_row['StartDate']==="0000-00-00") {
		$startdate = "";
	} else {
		$startdate = date('m-d-Y',strtotime($project_row['StartDate']));
	}
	if (empty($project_row['EndDate']) or $project_row['EndDate']==="0000-00-00") {
		$enddate = "";
	} else {
		$enddate = date('m-d-Y',strtotime($project_row['EndDate']));
	}
	$impact = $project_row['Impact'];
	$CE = $project_row['CE'];
	$managed = $project_row['Managed'];
	$contingency = $project_row['Contingency'];
	$priority = $project_row['Priority'];
	$status = $project_row['Status'];
	$description = $project_row['Description'];

	db_free_result($q);

} else if ($_REQUEST['action'] == "popupAdd") {
	$form_submit = "submit_insert";
	$return_page = "				parent.fb.loadPageOnClose='projects.php?action=show&project_id='+data;\n";
	$project_id = "";
	$project_name = "";
	$client_id = "";
	$owner_id = $_SESSION['UID'];
	$startdate = "";
	$enddate = "";
	$impact = "";
	$CE = "";
	$managed = "";
	$contingency = "";
	$priority = "Normal";
	$status = "Planning";
	$description = "";
}

$content .= "<link type='text/css' rel='stylesheet' href='/public/slider/css/redmond/jquery-ui-1.7.1.custom.css'>\n";

$content .= "<script type='text/javascript' src='/public/jquery-validate/jquery.validate.min.js'></script>\n";
$content .= "<script type='text/javascript' src='js/jquery.metadata.min.js'></script>\n";

$content .= "<script type='text/javascript'>\n";
$content .= "$(document).ready(function() {\n";

$content .= "	$(\"#submit_btn\").click(function() {\n";
$content .= "		// we want to store the values from the form input box, then send via ajax below\n";
$content .= "		var parameter1 = $(\"input\").serialize();\n";
$content .= "		var parameter2 = $(\"textarea\").serialize();\n";
$content .= "		var parameter3 = $(\"checkbox\").serialize();\n";
$content .= "		var parameter4 = $(\"select\").serialize();\n";
$content .= "		var parameters = parameter1 + '&' + parameter2 + '&' + parameter3 + '&' + parameter4;\n";
$content .= "		$.ajax({\n";
$content .= "			type: \"POST\",\n";
$content .= "			url: \"projects.php\",\n";
$content .= "			data: parameters,\n";
$content .= "			dataType: 'text',\n";
$content .= "			beforeSend: function(){\n";
$content .= "				if (!$('#UpdateForm').valid()) return false;\n";
$content .= "			},\n";
$content .= "			error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "				parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "   			},\n";
$content .= "			success: function(data){\n";
$content .= $return_page;
$content .= "				parent.fb.end(true);\n";
$content .= "			}\n";
$content .= "		});\n";
$content .= "		return false;\n";
$content .= "	});\n";

$content .= "	$(\"#discard_btn\").click(function() {\n";
$content .= "		var answer = confirm(\"Are you sure you want to Discard this Project?\")\n";
$content .= "		if (answer) {\n";
$content .= "			$.ajax({\n";
$content .= "				type: \"POST\",\n";
$content .= "				url: \"projects.php\",\n";
$content .= "				data: 'action=submit_delete&project_id=".$project_id."',\n";
$content .= "				dataType: 'text',\n";
$content .= "				error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "					parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "   				},\n";
$content .= "				success: function(data){\n";
$content .= "					parent.fb.loadPageOnClose='projects.php?action=list';\n";
$content .= "					parent.fb.end(true);\n";
$content .= "				}\n";
$content .= "			});\n";
$content .= "		}\n";
$content .= "		return false;\n";
$content .= "	});\n";

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
$content .= "});\n";
$content .= "</script>\n";

//all okay show task info
$content .= "<div class=\"container\">\n";
$content .= "<form action=\"\" name=\"UpdateForm\" id=\"UpdateForm\" method=\"post\">\n";
$content .= "<input type=\"hidden\" name=\"action\" value=\"".$form_submit."\" />\n";
$content .= "<input type=\"hidden\" name=\"project_id\" value=\"".$project_id."\" />\n";

$content .= "<table style=\"width:100%\">\n";

$content .= "<tr>\n";
$content .= "	<td>Project Name:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"name\" type=\"text\" name=\"name\" size=\"30\" value=\"".$project_name."\" class=\"required\"  minlength=\"2\" />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$q = db_query('SELECT * FROM employees WHERE Department_ID=(select emp.Department_ID from employees emp where emp.employee_ID='.$_SESSION['UID'].') ORDER BY LastName,FirstName');

//select contact
$content .= "<tr>\n";
$content .= "	<td>Lead Contact:</td>\n";
$content .= "	<td>\n";
$content .= "		<select name=\"assigned_to\">\n";
for ($i=0; $user_row = @db_fetch_array($q, $i); ++$i) {

	$content .= "			<option value=\"".$user_row['employee_ID']."\"";

	if ($user_row['employee_ID'] == $owner_id) {
		$content .= " selected=\"selected\"";
	}
	$content .= ">".$user_row['LastName'].", ".$user_row['FirstName']."</option>\n";
}
db_free_result($q);

$content .= "		</select>\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$q = db_query('SELECT * FROM clients ORDER BY client_full_name');

//select contact
$content .= "<tr>\n";
$content .= "	<td>Client:</td>\n";
$content .= "	<td>\n";
$content .= "		<select name=\"client_id\">\n";
$content .= "			<option value=\"0\">None</option>\n";
for ($i=0; $client_row = @db_fetch_array($q, $i); ++$i) {

	$content .= "			<option value=\"".$client_row['client_ID']."\"";

	if ($client_row['client_ID'] == $client_id) {
		$content .= " selected=\"selected\"";
	}
	$content .= ">".$client_row['client_full_name']."</option>\n";
}
db_free_result($q);

$content .= "		</select>\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td>Start Date:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id='startdate' name='startdate' type='text' size='12' value=\"".$startdate."\" />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";
$content .= "<tr>\n";
$content .= "	<td>End Date:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id='enddate' name='enddate' type='text' size='12' value=\"".$enddate."\" />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td>Impact:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<select name=\"impact\">\n";
$content .= "			<option value=\"Low\"".(($impact=='Low') ? ' selected=\'selected\'' : '').">Low</option>\n";
$content .= "			<option value=\"Normal\"".(($impact=='Normal') ? ' selected=\'selected\'' : '').">Normal</option>\n";
$content .= "			<option value=\"High\"".(($impact=='High') ? ' selected=\'selected\'' : '').">High</option>\n";
$content .= "		</select>\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td>CE:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"CE\" type=\"checkbox\" name=\"CE\" value=\"1\"".(($CE=='1') ? ' checked' : '')." />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";
$content .= "<tr>\n";
$content .= "	<td>Managed:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"managed\" type=\"checkbox\" name=\"managed\" value=\"1\"".(($managed=='1') ? ' checked' : '')." />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";
$content .= "<tr>\n";
$content .= "	<td>Contingency:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"contingency\" type=\"text\" name=\"contingency\" size=\"5\" value=\"".$contingency."\" />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

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
$content .= "		<select name=\"status\">\n";
$content .= "			<option value=\"Planning\"".(($status=='Planning') ? ' selected=\'selected\'' : '') .">Planning</option>\n";
$content .= "			<option value=\"Active\"".(($status=='Active') ? ' selected=\'selected\'' : '') .">Active</option>\n";
$content .= "			<option value=\"On Hold\"".(($status=='On Hold') ? ' selected=\'selected\'' : '') .">On Hold</option>\n";
$content .= "			<option value=\"Archived\"".(($status=='Archived') ? ' selected=\'selected\'' : '') .">Archived</option>\n";
$content .= "			<option value=\"Cancelled\"".(($status=='Cancelled') ? ' selected=\'selected\'' : '') .">Cancelled</option>\n";
$content .= "			<option value=\"Complete\"".(($status=='Complete') ? ' selected=\'selected\'' : '') .">Complete</option>\n";
$content .= "		</select>\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td style=\"vertical-align: top\">Description:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<textarea style=\"width: 100%\" name=\"description\" rows=\"4\">".$description."</textarea>\n";
$content .= "	</td>\n";
$content .= "</tr>\n";
$content .= "</table>\n";

$content .= "<p />\n";

$content .= "<div align=\"center\">\n";
$content .= "	<input type=\"submit\" name=\"Submit\" class=\"button\" id=\"submit_btn\" value=\"Save\" />\n";
$content .= "	&nbsp;&nbsp;&nbsp;\n";
$content .= "	<input type=\"button\" value=\"Cancel\" onClick=\"parent.fb.end(true); return false;\" />\n";
if ($project_id>0) {
	if (db_result(db_query('SELECT COUNT(*) FROM task_notes WHERE project_ID='.$project_id.' LIMIT 1'), 0, 0) > 0) {
		$content .= "	&nbsp;&nbsp;&nbsp;\n";
		$content .= "	<input type=\"submit\" name=\"Discard\" title=\"help\" disabled=\"disabled\" class=\"button\" id=\"discard_btn\" value=\"Discard\" /> Unable to delete active project.\n";
	} else {
		if (db_result(db_query('SELECT COUNT(*) FROM projects WHERE Owner_ID='.$_SESSION['UID'].' LIMIT 1'), 0, 0) > 0) {
			$content .= "	&nbsp;&nbsp;&nbsp;\n";
			$content .= "	<input type=\"submit\" name=\"Discard\" title=\"help\" disabled=\"disabled\" class=\"button\" id=\"discard_btn\" value=\"Discard\" /> Unable to delete active project.\n";
		} else {
			$content .= "	&nbsp;&nbsp;&nbsp;\n";
			$content .= "	<input type=\"submit\" name=\"Discard\" class=\"button\" id=\"discard_btn\" value=\"Discard\" />\n";
		}
	}
}
$content .= "</div>";
$content .= "</form>\n";
$content .= "</div>\n";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "	var mytext = document.getElementById('name');\n";
$content .= "	mytext.focus();\n";
$content .= "</script>\n";

echo $content;

?>