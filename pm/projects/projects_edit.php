<?php
/* $Id: projects_edit.php,v 1.16 2009/06/02 21:13:16 markp Exp $ */

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
	$owner_id = $_SESSION['UID'];
	$startdate = "";
	$enddate = "";
	$impact = "";
	$CE = "";
	$managed = "";
	$contingency = "";
	$priority = "Normal";
	$status = "Active";
	$description = "";
}


$content .= "<script type='text/javascript'>\n";
$content .= "$(document).ready(function() {\n";
$content .= "	$(\".button\").click(function() {\n";
$content .= " \n";
$content .= "	// we want to store the values from the form input box, then send via ajax below\n";
$content .= "	var parameter1 = $(\"input\").serialize();\n";
$content .= "	var parameter2 = $(\"textarea\").serialize();\n";
$content .= "	var parameter3 = $(\"checkbox\").serialize();\n";
$content .= "	var parameter4 = $(\"select\").serialize();\n";
$content .= "	var parameters = parameter1 + '&' + parameter2 + '&' + parameter3 + '&' + parameter4;\n";
//$content .= " alert(parameters)\n";
$content .= "		$.ajax({\n";
$content .= "			type: \"POST\",\n";
$content .= "			url: \"projects.php\",\n";
$content .= "			data: parameters,\n";
$content .= "			dataType: 'text',\n";
$content .= "			error: function(xhr, ajaxOptions, thrownError){\n";
//$content .= "		        alert(parameters);\n";
$content .= "				parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "    		},\n";
$content .= "			success: function(data){\n";
$content .= $return_page;
//$content .= "				parent.fb.loadPageOnClose='projects.php?action=show&project_id='+data;\n";
//$content .= "				parent.fb.loadPageOnClose='self';\n";
$content .= "				parent.fb.end(true);\n";
$content .= "			}\n";
$content .= "		});\n";
$content .= "	return false;\n";
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
$content .= "<div class=\"container\"";
$content .= "<form action=\"\" name=\"UpdateForm\" method=\"post\">\n";
$content .= "<input type=\"hidden\" name=\"action\" value=\"".$form_submit."\" />\n ";
$content .= "<input type=\"hidden\" name=\"project_id\" value=\"".$project_id."\" />\n";


$content .= "<table style=\"width:100%\">\n";

$content .= "<tr><td>Project Name:</td><td style=\"width:100%\"><input id=\"name\" type=\"text\" name=\"name\" size=\"30\" value=\"".$project_name."\" /></td></tr>\n";

$q = db_query('SELECT * FROM employees WHERE Department_ID=(select emp.Department_ID from employees emp where emp.employee_ID='.$_SESSION['UID'].') ORDER BY LastName,FirstName');

//select contact
$content .= "<tr><td>Lead Contact:</td><td><select name=\"assigned_to\">\n";
for ($i=0; $user_row = @db_fetch_array($q, $i); ++$i) {

	$content .= "<option value=\"".$user_row['employee_ID']."\"";

	if ($user_row['employee_ID'] == $owner_id) {
		$content .= " selected=\"selected\"";
	}
	$content .= ">".$user_row['LastName'].", ".$user_row['FirstName']."</option>\n";
}
$content .= "</select></td></tr>\n";

$content .= "<tr><td>Start Date:</td><td style=\"width:100%\"><input id='startdate' name='startdate' type='text' size='12' value=\"".$startdate."\"></td></tr>";
$content .= "<tr><td>End Date:</td><td style=\"width:100%\"><input id='enddate' name='enddate' type='text' size='12' value=\"".$enddate."\"></td></tr>";

$content .= "<tr><td>Impact:</td><td style=\"width:100%\">\n";
$content .= "<select name=\"impact\">\n";
$content .= "<option value=\"Low\"".(($impact=='Low') ? ' selected=\'selected\'' : '').">Low</option>\n";
$content .= "<option value=\"Normal\"".(($impact=='Normal') ? ' selected=\'selected\'' : '').">Normal</option>\n";
$content .= "<option value=\"High\"".(($impact=='High') ? ' selected=\'selected\'' : '').">High</option>\n";
$content .= "</select></td></tr>\n";

$content .= "<tr><td>CE:</td><td style=\"width:100%\"><input id=\"CE\" type=\"checkbox\" name=\"CE\" value=\"1\"".(($CE=='1') ? ' checked' : '')."/></td></tr>\n";
$content .= "<tr><td>Managed:</td><td style=\"width:100%\"><input id=\"managed\" type=\"checkbox\" name=\"managed\" value=\"1\"".(($managed=='1') ? ' checked' : '')."/></td></tr>\n";
$content .= "<tr><td>Contingency:</td><td style=\"width:100%\"><input id=\"contingency\" type=\"text\" name=\"contingency\" size=\"5\" value=\"".$contingency."\" /></td></tr>\n";

$content .= "<tr><td>Priority:</td><td style=\"width:100%\">\n";
$content .= "<select name=\"priority\">\n";
$content .= "<option value=\"Low\"".(($priority=='Low') ? ' selected=\'selected\'' : '') .">Low</option>\n";
$content .= "<option value=\"Normal\"".(($priority=='Normal') ? ' selected=\'selected\'' : '') .">Normal</option>\n";
$content .= "<option value=\"High\"".(($priority=='High') ? ' selected=\'selected\'' : '') .">High</option>\n";
$content .= "</select></td></tr>\n";

$content .= "<tr><td>Status:</td><td style=\"width:100%\">\n";
$content .= "<select name=\"status\">\n";
$content .= "<option value=\"Planning\"".(($status=='Planning') ? ' selected=\'selected\'' : '') .">Planning</option>\n";
$content .= "<option value=\"Active\"".(($status=='Active') ? ' selected=\'selected\'' : '') .">Active</option>\n";
$content .= "<option value=\"On Hold\"".(($status=='On Hold') ? ' selected=\'selected\'' : '') .">On Hold</option>\n";
$content .= "<option value=\"Archived\"".(($status=='Archived') ? ' selected=\'selected\'' : '') .">Archived</option>\n";
$content .= "<option value=\"Cancelled\"".(($status=='Cancelled') ? ' selected=\'selected\'' : '') .">Cancelled</option>\n";
$content .= "<option value=\"Complete\"".(($status=='Complete') ? ' selected=\'selected\'' : '') .">Complete</option>\n";
$content .= "</select></td></tr>\n";

$content .= "<tr><td>Description:</td><td style=\"width:100%\"><textarea style=\"width: 100%\" name=\"description\" rows=\"4\">".$description."</textarea></td> </tr>\n";

$content .= "</table>\n";
$content .= "<p><input type=\"submit\" name=\"submit\" class=\"button\" id=\"submit_btn\" value=\"Send\" /></button>&nbsp;&nbsp;&nbsp;<input type=\"button\" value=\"Cancel\" onClick=\"parent.fb.end(true); return false;\" /></p>";
$content .= "</form></div>\n";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "var mytext = document.getElementById('name');\n";
$content .= "mytext.focus();\n";
$content .= "</script>\n";

echo $content;

?>