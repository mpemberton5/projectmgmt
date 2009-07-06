<?php
/* $Id$ */

require_once('path.php');
require_once(BASE.'path_config.php');
require_once(BASE_CONFIG.'config.php');
include_once(BASE.'includes/common.php');
include_once(BASE.'includes/screen.php');

//secure variables
$content = '';

/******************************************************************************/
/*** LOGIN SCREEN ***/
/******************************************************************************/

$content = "<div style=\"text-align:center\">";

// Add Site Image if exists
if (SITE_IMG != '') {
	$content .=  "	<img src=\"images/".SITE_IMG."\" alt=\"WFUBMC PM logo\" /><br />";
} else {
	$content .=  "	<img src=\"images/wfubmc_logo.gif\" alt=\"WFUBMC PM logo\" /><br />";
}

$content .= "	<form method=\"post\" action=\"index.php\">\n";
$content .= "		<br />\n";
$content .= "		<table class=\"nt\" style=\"margin-left:auto; margin-right:auto;\">\n";
$content .= "			<tr class=\"nt\" align=\"left\">\n";
$content .= "				<td class=\"nt\">Login: </td>\n";
$content .= "				<td class=\"nt\"><input id=\"username\" type=\"text\" name=\"username\" value=\"\" size=\"30\" /></td>\n";
$content .= "				<td class=\"nt\" align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"login\" value=\"Login\" /></td>\n";
$content .= "			</tr>";
$content .= "		</table>\n";
$content .= "	</form>";
$content .= "</div>";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "	var mytext = document.getElementById('username');\n";
$content .= "	mytext.focus();\n";
$content .= "</script>\n";

create_complete_top('Login', 4, 0, 'login', 1);
echo $content;
create_bottom();
?>