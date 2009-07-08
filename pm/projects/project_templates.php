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

$content .= "<script>\n";
$content .= "$(document).ready(function() {\n";
$content .= "	$(\"#Templates\").change(onSelectChange);\n";
$content .= "});\n";
$content .= "function onSelectChange() {\n";
$content .= "	var selected = $(\"#Templates option:selected\");\n";
$content .= "	var bodyContent = '';\n";
$content .= "	if (selected.val() != 0) {\n";
$content .= "		bodyContent = $.ajax({\n";
$content .= "			url: \"projects.php\",\n";
$content .= "			async: false,\n";
$content .= "			type: \"POST\",\n";
$content .= "			data: 'action=template_details&pt_id='+selected.val(),\n";
$content .= "			dataType: \"html\",\n";
$content .= "			success: function(msg){\n";
$content .= "				$(\"#templateDetails\").html(msg);\n";
//$content .= "				alert(msg);\n";
$content .= "			}\n";
$content .= "		});\n";
//$content .= "		output = \"You Selected \" + selected.val();\n";
$content .= "	} else {\n";
$content .= "		$(\"#templateDetails\").html('');";
$content .= "	}\n";
//$content .= "	$(\"#templateDetails\").html(bodyContent.responseText);\n";
$content .= "}\n";
$content .= "</script>\n";


// query to get the projects
$SQL  = "SELECT * from proj_templates order by pt_name";
$q = db_query($SQL);

if (db_numrows($q)>0) {
	//select control
	$content .= "<br />\n";
	$content .= "<div style=\"text-align: center\">\n";
	$content .= "<table class=\"nt\">\n";
	$content .= "<tr class=\"nt\">\n";
	$content .= "	<td class=\"nt\">Template:</td>\n";
	$content .= "	<td class=\"nt\">\n";
	$content .= "		<select style=\"width:200px;\" id=\"Templates\">\n";
	$content .= "			<option style=\"font-style:italic;\" value=\"0\">Select Template</option>\n";
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
		$content .= "			<option value=\"".$row['pt_id']."\"";
		$content .= ">".$row['pt_name']."</option>\n";
	}
	$content .= "		</select>\n";
	$content .= "	</td>\n";
	$content .= "</tr>\n";
	$content .= "</table>\n";
	$content .= "</div>\n";
}
db_free_result($q);


$content .= "<br /><br />\n";
//$content .= "<table><tr><td>\n";
$content .= "<div id=\"templateDetails\"></div>\n";
//$content .= "</td></tr></table>\n";
echo $content;

?>