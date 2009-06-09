<?php
/* $Id: projects_quick_add.php,v 1.3 2009/06/03 20:18:07 markp Exp $ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/time.php');

$content = '';
$javascript = '';


	$form_submit = "submit_quick_insert";
	$project_id = "";
	$project_name = "";
	$owner_id = "";
	$startdate = "";
	$enddate = "";
	$impact = "";
	$CE = "";
	$managed = "";
	$contingency = "";
	$priority = "Normal";
	$status = "Active";
	$description = "";



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
$content .= "		$.ajax({\n";
$content .= "			type: \"POST\",\n";
$content .= "			url: \"projects.php\",\n";
$content .= "			data: parameters,\n";
$content .= "			dataType: 'text',\n";
$content .= "			error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "				parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "    		},\n";
$content .= "			success: function(data){\n";
$content .= "				parent.fb.loadPageOnClose='projects.php?action=show&project_id='+data;\n";
$content .= "				parent.fb.end(true);\n";
$content .= "			}\n";
$content .= "		});\n";
$content .= "	return false;\n";
$content .= "	});\n";
$content .= "	$(\"#startdate\").datepicker({dateFormat: 'mm-dd-yy', showAnim: 'slideDown', showOn: 'button', showButtonPanel: true, buttonImage: '/public/jquery/development-bundle/demos/datepicker/images/calendar.gif', buttonImageOnly: true});\n";
$content .= "	$(\"#enddate\").datepicker({dateFormat: 'mm-dd-yy', showAnim: 'slideDown', showOn: 'button', showButtonPanel: true, buttonImage: '/public/jquery/development-bundle/demos/datepicker/images/calendar.gif', buttonImageOnly: true});\n";
$content .= "});\n";
$content .= "</script>\n";

//all okay show task info
$content .= "<div class=\"container\"";
$content .= "	<form action=\"\" name=\"UpdateForm\" method=\"post\">\n";
$content .= "		<input type=\"hidden\" name=\"action\" value=\"".$form_submit."\" />\n";
$content .= "		<input type=\"hidden\" name=\"project_id\" value=\"".$project_id."\" />\n";
$content .= "		<input type=\"hidden\" name=\"assigned_to\" value=\"".$_SESSION['UID']."\" />\n";
$content .= "		<input type=\"hidden\" name=\"impact\" value=\"Low\" />\n";
$content .= "		<input type=\"hidden\" name=\"CE\" value=\"0\" />\n";
$content .= "		<input type=\"hidden\" name=\"managed\" value=\"0\" />\n";
$content .= "		<input type=\"hidden\" name=\"contingency\" value=\"0\" />\n";
$content .= "		<input type=\"hidden\" name=\"priority\" value=\"Low\" />\n";
$content .= "		<input type=\"hidden\" name=\"status\" value=\"Active\" />\n";


$content .= "		<table style=\"width:100%\">\n";
$content .= "			<tr>\n";
$content .= "				<td>Project Name:</td>\n";
$content .= "				<td style=\"width:100%\"><input id=\"name\" type=\"text\" name=\"name\" size=\"30\" value=\"".$project_name."\" /></td>\n";
$content .= "			</tr>\n";
$content .= "			<tr>\n";
$content .= "				<td style=\"vertical-align: top\">Description:</td><td style=\"width:100%\">\n";
$content .= "					<textarea style=\"width: 100%\" name=\"description\" rows=\"4\">".$description."</textarea>\n";
$content .= "				</td>\n";
$content .= "			</tr>\n";
$content .= "		</table>\n";

$content .= "		<p />\n";
$content .= "		<div align=\"center\">\n";
$content .= "			<input type=\"submit\" name=\"submit\" class=\"button\" id=\"submit_btn\" value=\"Save\" />\n";
$content .= "			&nbsp;&nbsp;&nbsp;\n";
$content .= "			<input type=\"button\" value=\"Cancel\" onClick=\"parent.fb.end(true); return false;\" />\n";
$content .= "		</div>\n";
$content .= "	</form>\n";
$content .= "</div>\n";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "	var mytext = document.getElementById('name');\n";
$content .= "	mytext.focus();\n";
$content .= "</script>\n";

echo $content;

?>