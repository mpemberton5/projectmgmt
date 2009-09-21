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

if ($_REQUEST['action'] == "popupLinkNew") {
	$parent_project_id = $_REQUEST['parent_project_id'];
	$parent_milestone_id = $_REQUEST['parent_milestone_id'];
} else {
	$parent_project_id = "0";
	$parent_milestone_id = "0";
}

$content .= "<style type='text/css'>\n";
$content .= "ul.template-column{\n";
$content .= "	width: 100%;\n";
$content .= "	padding: 0;\n";
$content .= "	list-style: none;\n";
$content .= "}\n";
$content .= "ul.template-column li {\n";
$content .= "	float: left;\n";
$content .= "	width: 50%;\n";
$content .= "	padding: 0;\n";
$content .= "	margin: 5px 0;\n";
$content .= "	display: inline;\n";
$content .= "}\n";
$content .= "
#templateDetailsContainer li {
	margin-left: 40px;
}
#templateDetailsContainer ul {
	margin-left: 20px;
}
\n";
$content .= "</style>\n";

$content .= "<script>\n";
$content .= "$(document).ready(function() {\n";
$content .= "	$(\"#Templates\").change(onSelectChange);\n";

//$content .= "	$(\"#template_btn\").click(function() {\n";
//$content .= "		// we want to store the values from the form input box, then send via ajax below\n";
//$content .= "		var parameters = 'action=submit_insert&parent_project_id=".$parent_project_id."&parent_milestone_id=".$parent_milestone_id."&selected_template_id='+document.getElementById(\"Templates\").value+''\n";
//$content .= "		$.ajax({\n";
//$content .= "			type: \"POST\",\n";
//$content .= "			url: \"projects.php\",\n";
//$content .= "			data: parameters,\n";
//$content .= "			dataType: 'text',\n";
//$content .= "			error: function(xhr, ajaxOptions, thrownError){\n";
//$content .= "				parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
//$content .= "   		},\n";
//$content .= "			success: function(data){\n";
//$content .= "				parent.fb.loadPageOnClose='self';\n";
//$content .= "				parent.fb.end(true);\n";
//$content .= "			}\n";
//$content .= "		});\n";
//$content .= "		return false;\n";
//$content .= "	});\n";
$content .= "});\n";

$content .= "function fnCreateTemplateProject(pp,pm) {
	var fbhref = 'projects.php?action=popupAdd&parent_project_id='+pp+'&parent_milestone_id='+pm+'&selected_template_id='+document.getElementById(\"Templates\").value;
	fb.start({ href: fbhref, rev:\"sameBox:true width:665 height:515 infoPos:tc disableScroll:true caption:`NEW Project` doAnimations:false\" });
}\n";

$content .= "function onSelectChange() {\n";
$content .= "	var selected = $(\"#Templates option:selected\");\n";
$content .= "	var parameters = 'action=template_details&pt_id='+selected.val()+'&parent_project_id=".$parent_project_id."&parent_milestone_id=".$parent_milestone_id."'\n";
$content .= "	var bodyContent = '';\n";
$content .= "	if (selected.val() != 0) {\n";
$content .= "		bodyContent = $.ajax({\n";
$content .= "			url: \"projects.php\",\n";
$content .= "			async: false,\n";
$content .= "			type: \"POST\",\n";
$content .= "			data: parameters,\n";
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

$content .= "<br />\n";
$content .= "<div style=\"text-align: center;\" class=\"generalbox\">\n";
$content .= "	<br />\n";
$content .= "	<button type=\"button\" onclick='fb.start({ href: \"projects.php?action=popupAdd&parent_project_id=".$parent_project_id."&parent_milestone_id=".$parent_milestone_id."\", rev:\"sameBox:true width:665 height:515 infoPos:tc disableScroll:true caption:`NEW Project` doAnimations:false\" });'>Create Blank Project</button>\n";
$content .= "	<br />\n";
$content .= "	<br />\n";
$content .= "</div>\n";
$content .= "<br />\n";

// query to get the projects
$SQL  = "SELECT * from proj_templates order by pt_name";
$q = db_query($SQL);

if (db_numrows($q)>0) {
	//select template
	$content .= "<div style=\"text-align: center;\" class=\"generalbox\">\n";
	$content .= "	<br />\n";
	$content .= "	<label for=\"Templates\">Select Template:</label>\n";
	$content .= "	<select style=\"width:200px;\" id=\"Templates\">\n";
	$content .= "		<option style=\"font-style:italic;\" value=\"0\">Select Template</option>\n";
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
		$content .= "		<option value=\"".$row['pt_id']."\"";
		$content .= ">".$row['pt_name']."</option>\n";
	}
	$content .= "	</select>\n";
	$content .= "	<br />\n";
	$content .= "</div>\n";
}
db_free_result($q);


$content .= "<br />\n";




$content .= "<div id=\"templateDetailsContainer\">\n";
$content .= "<div id=\"templateDetails\"></div>\n";
	//when button is hit, use parent_project_id and parent_milestone_id
	$content .= "<div align=\"center\">\n";
	//$content .= "<div align=\"center\"><input type=\"submit\" name=\"Submit\" class=\"button\" id=\"template_btn\" value=\"Create Project using this Template\" /></div>\n";
	$content .= "	<button type=\"button\" onclick='fnCreateTemplateProject(".$parent_project_id.",".$parent_milestone_id.");return false;'>Create Project using this Template</button>\n";
	$content .= "</div>\n";
	
$content .= "</div>\n";
//$content .= "</td></tr></table>\n";
echo $content;
?>