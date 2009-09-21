<?php
/* $Id: project_templates.php 21 2009-07-10 14:10:27Z mpemberton5@gmail.com $ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/time.php');

if (!@safe_integer($_GET['parent_project_id'])) {
	error('Task show', 'The parent_project_id input is not valid');
}
$parent_project_id = $_GET['parent_project_id'];

if (!@safe_integer($_GET['parent_milestone_id'])) {
	error('Task show', 'The parent_milestone_id input is not valid');
}
$parent_milestone_id = $_GET['parent_milestone_id'];

$content = '';

$content .= "<link type='text/css' rel='stylesheet' href='/public/flexigrid/css/flexigrid/flexigrid.css'>\n";
$content .= "<script type=\"text/javascript\" src=\"/public/flexigrid/flexigrid.pack.js\"></script>\n";

$content .= "<script>\n";
$content .= "	$(document).ready(function() {\n";
$content .= "	$('#scrollTable').flexigrid({
			url: 'projects.php',
			dataType: 'json',
			params: [{name: 'action', value:'list_all'}],
			colModel : [
				{display: 'Project Name', name : 'Project_Name', width : 250, sortable : true, align: 'left'},
				{display: 'Status', name : 'Status', width : 75, sortable : true, align: 'right'},
				{display: 'Dept Name', name : 'Dept', width : 120, sortable : true, align: 'left'},
				{display: 'Lead Contact', name : 'empName', width : 150, sortable : true, align: 'left'}
				],
			sortname: 'Project_Name',
			sortorder: 'asc',
			usepager: true,
			title: 'All Projects',
			useRp: false,
			resizable: false,
			singleSelect: true,
			rp: 10,
			showTableToggleBtn: false,
			width: '650',
			height: 'auto'
});\n";

$content .= "	$(\"#link_btn\").click(function() {\n";
$content .= "		// we want to store the values from the form input box, then send via ajax below\n";
$content .= "		var selID = getSelectedRow();\n";
$content .= "		var parameters = 'action=savePL&project_id=".$parent_project_id."&milestone_id=".$parent_milestone_id."&selected_project_id=' + selID;\n";
$content .= "		$.ajax({\n";
$content .= "			type: \"POST\",\n";
$content .= "			url: \"projects.php\",\n";
$content .= "			data: parameters,\n";
$content .= "			dataType: 'text',\n";
$content .= "			error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "				parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "   		},\n";
$content .= "			success: function(data){\n";
$content .= "				parent.fb.loadPageOnClose='self';\n";
$content .= "				parent.fb.end(true);\n";
$content .= "			}\n";
$content .= "		});\n";
$content .= "		return false;\n";
$content .= "	});\n";

$content .= "});\n";

$content .= "function getSelectedRow() {\n";
$content .= "	var selectedRow =$(\"#scrollTable\").find(\"tr.trSelected\").get();\n";
$content .= "	if (selectedRow.length >0) {\n";
$content .= "		return selectedRow[0].id.substr(3);\n";
$content .= "	}\n";
//$content .= "	var arrReturn = [];\n";
//$content .= "	$('#scrollTable').each(function() {\n";
//$content .= "		var arrRow = [];\n";
//$content .= "		$(this).find('div').each(function() {\n";
//$content .= "			arrRow.push( $(this).html() );\n";
//$content .= "		});\n";
//$content .= "		arrReturn.push(arrRow);\n";
//$content .= "	});\n";
//$content .= " 	return arrReturn;\n";
$content .= "}\n";

$content .= "</script>\n";

$content .= "<div align=\"left\" style=\"height: 400px;\">\n";
$content .= "	<table id=\"scrollTable\" class=\"scrollTable\" style=\"display:none\"></table>\n";
$content .= "</div>\n";
$content .= "<div align=\"center\">\n";
$content .= "	<input type=\"submit\" name=\"Submit\" class=\"button\" id=\"link_btn\" value=\"Link to Selected Project\" />\n";
$content .= "</div>\n";

echo $content;
?>