<?php
/* $Id: file_upload.php,v 1.3 2009/06/03 04:19:51 markp Exp $ */

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

$content .= "<script type=\"text/javascript\" src=\"js/jquery.form.js\"></script>\n";

$content .= "<script type=\"text/javascript\"><!--\n";
$content .= "$().ajaxError(function(ev,xhr,o,err) {\n";
$content .= "    alert(err);\n";
$content .= "    if (window.console && window.console.log) console.log(err);\n";
$content .= "});\n";
$content .= "$(function() {\n";
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

$content .= "			$(\"#fileListTable\").load(\"files.php?action=list&project_id=".$project_id."&task_id=".$task_id."\");\n";

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
$content .= "<input type=\"hidden\" name=\"action\" value=\"submit_upload\" />\n";
$content .= "<input type=\"hidden\" name=\"project_id\" value=\"".$project_id."\" />\n";
$content .= "<input type=\"hidden\" name=\"task_id\" value=\"".$task_id."\" />\n";
$content .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".FILE_MAXSIZE."\" />\n";
$content .= "<input type=\"hidden\" id=\"uploadResponseType\"  name=\"mimetype\" value=\"html\" />\n";
$content .= "Document to Upload:<input id=\"userfile\" type=\"file\" name=\"userfile\" size=\"50\" />\n";
$content .= "<input type=\"submit\" value=\"Upload\" /></p>\n";
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

$content .= "<div id=\"fileListTable\"></div>\n";

$content .= "<p />\n";
$content .= "<div align=\"center\">\n";
$content .= "	<input type=\"button\" value=\"Close\" onClick=\"parent.fb.end(false); return false;\" />\n";
$content .= "</div>\n";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "	$(\"#fileListTable\").load(\"files.php?action=list&project_id=".$project_id."&task_id=".$task_id."\");\n";
$content .= "</script>\n";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "	var mytext = document.getElementById('userfile');\n";
$content .= "	mytext.focus();\n";
$content .= "</script>\n";


echo $content;

?>