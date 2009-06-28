<?php
/* $Id: projects_edit.php,v 1.16 2009/06/02 21:13:16 markp Exp $ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

$content = '';
$javascript = '';

if ($_REQUEST['action'] == "deptPopupEdit") {
	if (!@safe_integer($_GET['department_id'])) {
		error('Dept edit', 'The department_id input is not valid');
	}
	$department_id = $_GET['department_id'];

	//get employee details - if any
	$q = db_query('SELECT * FROM departments WHERE department_ID='.$department_id.' LIMIT 1');

	//check for any posts
	if (db_numrows($q) < 1) {
		error("Dept Edit", "Unable to Find Message");
	}

	if (!$dept_row = db_fetch_array($q, 0)) {
		error("Dept Edit", "Unable to Find Message");
	}

	$form_submit = "dept_submit_update";
	$return_page = "				parent.fb.loadPageOnClose='self';\n";
	$Dept_Name = $dept_row['Dept_Name'];
	
	db_free_result($q);

} else if ($_REQUEST['action'] == "deptPopupAdd") {
	$form_submit = "dept_submit_insert";
	$return_page = "				parent.fb.loadPageOnClose='self';\n";
	$department_id = "";
	$Dept_Name = "";
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
$content .= "				data: 'action=dept_submit_delete&department_id=".$department_id."',\n";
$content .= "				dataType: 'text',\n";
$content .= "				error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "					parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "   				},\n";
$content .= "				success: function(data){\n";
$content .= "					parent.fb.loadPageOnClose='admin.php?action=depts';\n";
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
$content .= "<input type=\"hidden\" name=\"department_id\" value=\"".$department_id."\" />\n";


$content .= "<table style=\"width:100%\">\n";

$content .= "<tr>\n";
$content .= "	<td>Department Name:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"Dept_Name\" type=\"text\" name=\"Dept_Name\" size=\"50\" value=\"".$Dept_Name."\" class=\"required\"  minlength=\"2\" />\n";
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
$content .= "	var mytext = document.getElementById('Dept_Name');\n";
$content .= "	mytext.focus();\n";
$content .= "</script>\n";

echo $content;

?>