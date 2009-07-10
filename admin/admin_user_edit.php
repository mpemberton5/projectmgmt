<?php
/* $Id$ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

$content = '';
$javascript = '';

if ($_REQUEST['action'] == "userPopupEdit") {
	if (!@safe_integer($_GET['employee_id'])) {
		error('User edit', 'The employee_id input is not valid');
	}
	$employee_id = $_GET['employee_id'];

	//get employee details - if any
	$q = db_query('SELECT * FROM employees WHERE employee_ID='.$employee_id.' LIMIT 1');

	//check for any posts
	if (db_numrows($q) < 1) {
		error("User Edit", "Unable to Find Message");
	}

	if (!$user_row = db_fetch_array($q, 0)) {
		error("User Edit", "Unable to Find Message");
	}

	$form_submit = "user_submit_update";
	$return_page = "				parent.fb.loadPageOnClose='self';\n";
	$Department_ID = $user_row['Department_ID'];
	$MedCtrLogin = $user_row['MedCtrLogin'];
	$LastName = $user_row['LastName'];
	$FirstName = $user_row['FirstName'];
	$EMail = $user_row['EMail'];
	$JobTitle = $user_row['JobTitle'];
	$Phone = $user_row['Phone'];
	$Notes = $user_row['Notes'];
	$Level_ID = $user_row['Level_ID'];
	$pm_SiteAdmin = $user_row['pm_SiteAdmin'];
	$mgmt = $user_row['mgmt'];
	$active = $user_row['active'];

	db_free_result($q);

} else if ($_REQUEST['action'] == "userPopupAdd") {
	$form_submit = "user_submit_insert";
	$return_page = "				parent.fb.loadPageOnClose='self';\n";
	$employee_id = "";
	$Department_ID = "";
	$MedCtrLogin = "";
	$LastName = "";
	$FirstName = "";
	$EMail = "";
	$JobTitle = "";
	$Phone = "";
	$Notes = "";
	$Level_ID = "";
	$pm_SiteAdmin = "0";
	$mgmt = "0";
	$active = "1";
}

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
//$content .= "alert(parameters);";
$content .= "		$.ajax({\n";
$content .= "			type: \"POST\",\n";
$content .= "			url: \"admin.php\",\n";
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
$content .= "		var answer = confirm(\"Are you sure you want to Discard this Employee?\")\n";
$content .= "		if (answer) {\n";
$content .= "			$.ajax({\n";
$content .= "				type: \"POST\",\n";
$content .= "				url: \"admin.php\",\n";
$content .= "				data: 'action=user_submit_delete&employee_id=".$employee_id."',\n";
$content .= "				dataType: 'text',\n";
$content .= "				error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "					parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "   				},\n";
$content .= "				success: function(data){\n";
$content .= "					parent.fb.loadPageOnClose='admin.php?action=users';\n";
$content .= "					parent.fb.end(true);\n";
$content .= "				}\n";
$content .= "			});\n";
$content .= "		}\n";
$content .= "		return false;\n";
$content .= "	});\n";

$content .= "});\n";
$content .= "</script>\n";

//all okay show task info
$content .= "<div class=\"container\">\n";
$content .= "<form action=\"\" name=\"UpdateForm\" id=\"UpdateForm\" method=\"post\">\n";
$content .= "<input type=\"hidden\" name=\"action\" value=\"".$form_submit."\" />\n";
$content .= "<input type=\"hidden\" name=\"employee_id\" value=\"".$employee_id."\" />\n";


$content .= "<table style=\"width:100%\">\n";

$content .= "<tr>\n";
$content .= "	<td>MedCtr Login:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"MedCtrLogin\" type=\"text\" name=\"MedCtrLogin\" size=\"50\" value=\"".$MedCtrLogin."\" class=\"required\"  minlength=\"2\" />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td>Last Name:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"LastName\" type=\"text\" name=\"LastName\" size=\"50\" value=\"".$LastName."\" class=\"required\"  minlength=\"2\" />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td>First Name:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"FirstName\" type=\"text\" name=\"FirstName\" size=\"50\" value=\"".$FirstName."\" class=\"required\"  minlength=\"2\" />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$q = db_query('SELECT * FROM departments ORDER BY Dept_Name');

//select contact
$content .= "<tr>\n";
$content .= "	<td>Departments:</td>\n";
$content .= "	<td>\n";
$content .= "		<select name=\"Department_ID\">\n";
for ($i=0; $dept_row = @db_fetch_array($q, $i); ++$i) {

	$content .= "			<option value=\"".$dept_row['department_ID']."\"";

	if ($dept_row['department_ID'] == $Department_ID) {
		$content .= " selected=\"selected\"";
	}
	$content .= ">".$dept_row['Dept_Name']."</option>\n";
}
$content .= "		</select>\n";
$content .= "	</td>\n";
$content .= "</tr>\n";
db_free_result($q);

$content .= "<tr>\n";
$content .= "	<td>E-mail:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"EMail\" type=\"text\" name=\"EMail\" size=\"50\" value=\"".$EMail."\" />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td>Job Title:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"JobTitle\" type=\"text\" name=\"JobTitle\" size=\"50\" value=\"".$JobTitle."\" />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td>Phone:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"Phone\" type=\"text\" name=\"Phone\" size=\"50\" value=\"".$Phone."\" />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td>Level ID:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"Level_ID\" type=\"text\" name=\"Level_ID\" size=\"50\" value=\"".$Level_ID."\" />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td>PM Site Admin:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"pm_SiteAdmin\" type=\"checkbox\" name=\"pm_SiteAdmin\" value=\"1\"".(($pm_SiteAdmin=='1') ? ' checked' : '')." />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td>Management:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"mgmt\" type=\"checkbox\" name=\"mgmt\" value=\"1\"".(($mgmt=='1') ? ' checked' : '')." />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td>Active:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"active\" type=\"checkbox\" name=\"active\" value=\"1\"".(($active=='1') ? ' checked' : '')." />\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "<tr>\n";
$content .= "	<td>Notes:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<textarea style=\"width: 100%\" name=\"Notes\" rows=\"4\">".$Notes."</textarea>\n";
$content .= "	</td>\n";
$content .= "</tr>\n";

$content .= "</table>\n";

$content .= "<p />\n";

$content .= "<div align=\"center\">\n";
$content .= "	<input type=\"submit\" name=\"Submit\" class=\"button\" id=\"submit_btn\" value=\"Save\" />\n";
$content .= "	&nbsp;&nbsp;&nbsp;\n";
$content .= "	<input type=\"button\" value=\"Cancel\" onClick=\"parent.fb.end(true); return false;\" />\n";
/*
if ($employee_id>0) {
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
*/
$content .= "</div>";
$content .= "</form>\n";
$content .= "</div>\n";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "	var mytext = document.getElementById('MedCtrLogin');\n";
$content .= "	mytext.focus();\n";
$content .= "</script>\n";

echo $content;

?>