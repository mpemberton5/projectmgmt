<?php
/* $Id$ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

//includes
require_once('path.php');
require_once(BASE.'includes/security.php');

if (!@safe_integer($_REQUEST['project_id'])) {
	error('File upload', 'Not a valid project_id');
}
$project_id = $_REQUEST['project_id'];

if (!@safe_integer($_REQUEST['task_id'])) {
	$task_id = 0;
} else {
	$task_id = $_REQUEST['task_id'];
}

$content = "";
$content .= "<link type='text/css' rel='stylesheet' href='/public/flexigrid/css/flexigrid/flexigrid.css'>\n";

$content .= "<script type=\"text/javascript\" src=\"js/jquery.form.js\"></script>\n";
$content .= "<script type=\"text/javascript\" src=\"/public/flexigrid/flexigrid.js\"></script>\n";

$content .= "<script type=\"text/javascript\"><!--\n";
$content .= "$().ajaxError(function(ev,xhr,o,err) {\n";
$content .= "    alert(err);\n";
$content .= "    if (window.console && window.console.log) console.log(err);\n";
$content .= "});\n";

$content .= "$(document).ready(function() {\n";
$content .= "	$('#scrollTable').flexigrid({
			url: 'files.php',
			dataType: 'json',
			params: [{name: 'project_id', value:'".$project_id."'},{name: 'action', value:'list'}],
			colModel : [
				{display: 'FileName', name : 'filename', width : 250, sortable : true, align: 'left', process: onItemClick},
				{display: 'Size', name : 'size', width : 75, sortable : true, align: 'right', process: onItemClick},
				{display: 'Date Uploaded', name : 'uploaded_date', width : 120, sortable : true, align: 'left', process: onItemClick},
				{display: 'Uploaded By', name : 'uploaded_by', width : 150, sortable : true, align: 'left', process: onItemClick},
				{display: 'id', name : 'file_id', width : 10, sortable : false, hide: true}
				],
			sortname: 'uploaded_date',
			sortorder: 'desc',
			usepager: true,
			title: 'Attachments for Project',
			useRp: false,
			resizable: false,
			singleSelect: true,
			rp: 10,
			showTableToggleBtn: false,
			width: 660,
			height: 291
});\n";

$content .= "	$('#uploadForm').ajaxForm({\n";
$content .= "        beforeSubmit: function(a,f,o) {\n";
$content .= "            o.dataType = $('#uploadResponseType')[0].value;\n";
$content .= "            $('#uploadOutput').html('Submitting...');\n";
$content .= "        },\n";
$content .= "        success: function(data) {\n";
$content .= "            var out = $('#uploadOutput');\n";
$content .= "            if (typeof data == 'object' && data.nodeType)\n";
$content .= "                data = elementToString(data.documentElement, true);\n";
$content .= "            else if (typeof data == 'object')\n";
$content .= "                data = objToString(data);\n";
$content .= "            out.append('<div><pre>'+ data +'</pre></div>');\n";
$content .= "			$(\"#scrollTable\").flexReload();\n";
$content .= "        }\n";
$content .= "    });\n";
$content .= "});\n";

$content .= "function onItemClick(cellDiv,id) {\n";
$content .= "	$(cellDiv).click(\n";
$content .= "		function(){\n";
$content .= "			window.open(\"files.php?action=download&file_id=\"+id,\"_self\");\n";
$content .= "		})\n";
$content .= "}\n";

$content .= "// helper\n";
$content .= "function objToString(o) {\n";
$content .= "    var s = '{';\n";
$content .= "    for (var p in o)\n";
$content .= "        s += '    ' + p + ': ' + o[p];\n";
$content .= "    return s + '}';\n";
$content .= "}\n";

$content .= "// helper\n";
$content .= "function elementToString(n, useRefs) {\n";
$content .= "    var attr = '', nest = '', a = n.attributes;\n";
$content .= "    for (var i=0; a && i < a.length; i++)\n";
$content .= "        attr += ' ' + a[i].nodeName + '=\"' + a[i].nodeValue + '\"';\n";

$content .= "    if (n.hasChildNodes == false)\n";
$content .= "        return '<' + n.nodeName + '\/>';\n";

$content .= "    for (var i=0; i < n.childNodes.length; i++) {\n";
$content .= "        var c = n.childNodes.item(i);\n";
$content .= "        if (c.nodeType == 1)       nest += elementToString(c);\n";
$content .= "        else if (c.nodeType == 2)  attr += ' ' + c.nodeName + '=\"' + c.nodeValue + '\" ';\n";
$content .= "        else if (c.nodeType == 3)  nest += c.nodeValue;\n";
$content .= "    }\n";
$content .= "    var s = '<' + n.nodeName + attr + '>' + nest + '<\/' + n.nodeName + '>';\n";
$content .= "    return useRefs ? s.replace(/</g,'&lt;').replace(/>/g,'&gt;') : s;\n";
$content .= "};\n";

$content .= "// -->\n";
$content .= "</script>\n";

$content .= "<form id=\"uploadForm\" method=\"POST\" enctype=\"multipart/form-data\" action=\"files.php\">\n";
$content .= "	<input type=\"hidden\" name=\"action\" value=\"submit_upload\" />\n";
$content .= "	<input type=\"hidden\" name=\"project_id\" value=\"".$project_id."\" />\n";
$content .= "	<input type=\"hidden\" name=\"task_id\" value=\"".$task_id."\" />\n";
$content .= "	<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".FILE_MAXSIZE."\" />\n";
$content .= "	<input type=\"hidden\" id=\"uploadResponseType\"  name=\"mimetype\" value=\"html\" />\n";
$content .= "	Document to Upload:&nbsp;<input id=\"userfile\" type=\"file\" name=\"userfile\" size=\"50\" />\n";
$content .= "	<input type=\"submit\" style=\"height:20px;\" value=\"Upload\" /></p>\n";
$content .= "</form>\n";

$content .= "<div>\n";
$content .= "	<p />\n";
$content .= "	<label>Output:</label>\n";
$content .= "	<div id=\"uploadOutput\"></div>\n";
$content .= "</div>\n";

$content .= "<p /><br />\n";


$content .= "<table id=\"scrollTable\" class=\"scrollTable\" style=\"display:none\"></table>\n";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "	var mytext = document.getElementById('userfile');\n";
$content .= "	mytext.focus();\n";
$content .= "</script>\n";

echo $content;

?>