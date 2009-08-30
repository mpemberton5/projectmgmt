<?php
/* $Id$ */

require_once('path.php');
require_once(BASE.'includes/security.php');
require_once(BASE.'includes/screen.php');

create_complete_top('Feedback', 4, 0, 'comments', 1);

$content = "";
$content .= "<script type='text/javascript'>\n";
$content .= "$(function() {\n";
$content .= "	$(\"#submit_btn2\").click(function() {\n";
$content .= "	// we want to store the values from the form input box, then send via ajax below\n";
$content .= "	var parameter1 = $(\"input\").serialize();\n";
$content .= "	var parameter2 = $(\"textarea\").serialize();\n";
$content .= "	var parameter3 = $(\"select\").serialize();\n";
$content .= "	var parameters = parameter1 + '&' + parameter2 + '&' + parameter3;\n";
//$content .= " alert(parameters);\n";
$content .= "		$.ajax({\n";
$content .= "			type: \"POST\",\n";
$content .= "			url: \"feedback_submit.php\",\n";
$content .= "			data: parameters,\n";
$content .= "			error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "				parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "    		},\n";
$content .= "			success: function(data){\n";
//$content .= "				parent.fb.loadPageOnClose='self';\n";
$content .= "				parent.fb.end();\n";
$content .= "			}\n";
$content .= "		});\n";
$content .= "	return false;\n";
$content .= "	});\n";
$content .= "});\n";
$content .= "</script>\n";

$content .= "<H1>Feedback</H1>\n";
$content .= "<form action=\"\" name=\"FeedbackForm\" method=\"post\">\n";
$content .= "<input type=\"hidden\" name=\"currform\" value=\"".$_REQUEST['currform']."\" />\n";

//$content .= "<br />\n";
$content .= "<div align=\"center\">\n";
$content .= "<textarea rows=\"15\" cols=\"65\" name=\"comments\" id=\"comments\"></textarea><br />\n";

$content .= "	<input type=\"submit\" name=\"submit\" class=\"button\" id=\"submit_btn2\" value=\"Submit Feedback\" />\n";
$content .= "</div>\n";
$content .= "</form>\n";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "	var mytext = document.getElementById('comments');\n";
$content .= "	mytext.focus();\n";
$content .= "</script>\n";
echo $content;
create_bottom();

?>