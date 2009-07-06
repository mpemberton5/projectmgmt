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
//$content .= "<script type=\"text/javascript\" src=\"/public/flexigrid/lib/jquery/jquery.js\"></script>\n";

$content .= "<script type=\"text/javascript\" src=\"js/jquery.form.js\"></script>\n";
$content .= "<script type=\"text/javascript\" src=\"/public/flexigrid/flexigrid.pack.js\"></script>\n";

$content .= "<script type=\"text/javascript\"><!--\n";
$content .= "$().ajaxError(function(ev,xhr,o,err) {\n";
$content .= "    alert(err);\n";
$content .= "    if (window.console && window.console.log) console.log(err);\n";
$content .= "});\n";

$content .= "$(document).ready(function() {\n";
$content .= "	$('.scrollTable').flexigrid();\n";
$content .= "	$('#uploadForm').ajaxForm({\n";
$content .= "        beforeSubmit: function(a,f,o) {\n";
$content .= "            o.dataType = $('#uploadResponseType')[0].value;\n";
$content .= "            $('#uploadOutput').html('Submitting...');\n";
$content .= "        },\n";
$content .= "        success: function(data) {\n";
$content .= "            var out = $('#uploadOutput');\n";
//$content .= "            out.html('Form success handler received: <strong>' + typeof data + '</strong>');\n";
$content .= "            if (typeof data == 'object' && data.nodeType)\n";
$content .= "                data = elementToString(data.documentElement, true);\n";
$content .= "            else if (typeof data == 'object')\n";
$content .= "                data = objToString(data);\n";
$content .= "            out.append('<div><pre>'+ data +'</pre></div>');\n";
//$content .= "			$(\"#tableContainer\").load(\"files.php?action=list&project_id=".$project_id."&task_id=".$task_id."\");\n";
$content .= "        }\n";
$content .= "    });\n";
$content .= "});\n";

$content .= "// helper\n";
$content .= "function objToString(o) {\n";
$content .= "    var s = \"{\";\n";
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


//find out the project name
//$project_name = db_result(db_query('SELECT project_name FROM projects WHERE id='.$project_id), 0, 0);

$content .= "<form id=\"uploadForm\" method=\"POST\" enctype=\"multipart/form-data\" action=\"files.php\">\n";
$content .= "	<input type=\"hidden\" name=\"action\" value=\"submit_upload\" />\n";
$content .= "	<input type=\"hidden\" name=\"project_id\" value=\"".$project_id."\" />\n";
$content .= "	<input type=\"hidden\" name=\"task_id\" value=\"".$task_id."\" />\n";
$content .= "	<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".FILE_MAXSIZE."\" />\n";
$content .= "	<input type=\"hidden\" id=\"uploadResponseType\"  name=\"mimetype\" value=\"html\" />\n";
$content .= "	Document to Upload:&nbsp;<input id=\"userfile\" type=\"file\" name=\"userfile\" size=\"50\" />\n";
$content .= "	<input type=\"submit\" value=\"Upload\" /></p>\n";
$content .= "</form>\n";

$content .= "<div>\n";
$content .= "	<p />\n";
$content .= "	<label>Output:</label>\n";
$content .= "	<div id=\"uploadOutput\"></div>\n";
$content .= "</div>\n";

$content .= "<p /><br />\n";
$content .= "<div>\n";
$content .= "List of Files for Project\n";
$content .= "</div>\n";


$SQL = "SELECT f.*, emp.FirstName, emp.LastName from files f, employees emp where f.project_id=".$project_id." and emp.employee_ID=f.uploaded_by";
$q = db_query($SQL);

//check if there are project
if (db_numrows($q) > 0) {
	$content .= "<table id=\"scrollTable\" class=\"scrollTable\">\n";
	$content .= "	<thead>\n";
	$content .= "		<tr>\n";
	$content .= "			<th width='150px'>FileName</th>\n";
	$content .= "			<th width='50px'>Size</th>\n";
	$content .= "			<th width='100px'>Date Uploaded</th>\n";
	$content .= "			<th width='100px'>Uploaded By</th>\n";
	$content .= "		</tr>\n";
	$content .= "	</thead>\n";
	$content .= "	<tbody>\n";
	
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
		$content .= "		<tr>\n";
		$content .= "			<td><a href=\"files.php?action=download&file_id=".$row['file_id']."\">".$row['filename']."</a></td>\n";
		$content .= "			<td>".$row['size']."</td>\n";
		$content .= "			<td>".$row['uploaded_date']."</td>\n";
		$content .= "			<td>".$row['FirstName']." ".$row['LastName']."</td>\n";
		$content .= "		</tr>\n";
	}
	$content .= "	</tbody>\n";
	$content .= "</table>\n";
}
db_free_result($q);

$content .= "<p />\n";
$content .= "<div align=\"center\">\n";
$content .= "	<input type=\"button\" value=\"Close\" onClick=\"parent.fb.end(false); return false;\" />\n";
$content .= "</div>\n";

//$content .= "<script language='javascript' type='text/javascript'>\n";
//$content .= "	$(\"#tableContainer\").load(\"files.php?action=list&project_id=".$project_id."&task_id=".$task_id."\");\n";
//$content .= "</script>\n";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "	var mytext = document.getElementById('userfile');\n";
$content .= "	mytext.focus();\n";
$content .= "</script>\n";


echo $content;

?>