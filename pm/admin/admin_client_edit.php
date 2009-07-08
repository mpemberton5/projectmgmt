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

if ($_REQUEST['action'] == "clientPopupEdit") {
	if (!@safe_integer($_GET['client_id'])) {
		error('Client edit', 'The client_id input is not valid');
	}
	$client_id = $_GET['client_id'];

	//get employee details - if any
	$q = db_query('SELECT * FROM clients WHERE client_ID='.$client_id.' LIMIT 1');

	//check for any posts
	if (db_numrows($q) < 1) {
		error("Clients Edit", "Unable to Find Message");
	}

	if (!$client_row = db_fetch_array($q, 0)) {
		error("Clients Edit", "Unable to Find Message");
	}

	$form_submit = "client_submit_update";
	$return_page = "				parent.fb.loadPageOnClose='self';\n";
	$client_full_name = $client_row['client_full_name'];

	db_free_result($q);

} else if ($_REQUEST['action'] == "clientPopupAdd") {
	$form_submit = "client_submit_insert";
	$return_page = "				parent.fb.loadPageOnClose='self';\n";
	$client_id = "";
	$client_full_name = "";
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
$content .= "		var answer = confirm(\"Are you sure you want to Discard this Client?\")\n";
$content .= "		if (answer) {\n";
$content .= "			$.ajax({\n";
$content .= "				type: \"POST\",\n";
$content .= "				url: \"admin.php\",\n";
$content .= "				data: 'action=client_submit_delete&client_id=".$client_id."',\n";
$content .= "				dataType: 'text',\n";
$content .= "				error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "					parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "   				},\n";
$content .= "				success: function(data){\n";
$content .= "					parent.fb.loadPageOnClose='admin.php?action=clients';\n";
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
$content .= "<input type=\"hidden\" name=\"client_id\" value=\"".$client_id."\" />\n";


$content .= "<table style=\"width:100%\">\n";

$content .= "<tr>\n";
$content .= "	<td>Client Name:</td>\n";
$content .= "	<td style=\"width:100%\">\n";
$content .= "		<input id=\"client_full_name\" type=\"text\" name=\"client_full_name\" size=\"100\" value=\"".$client_full_name."\" class=\"required\"  minlength=\"2\" />\n";
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
$content .= "	var mytext = document.getElementById('client_full_name');\n";
$content .= "	mytext.focus();\n";
$content .= "</script>\n";

echo $content;

?>