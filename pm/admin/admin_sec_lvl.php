<?php
/* $Id: admin_sec_lvl.php,v 1.1 2009/04/22 00:05:06 markp Exp $ */

//security check
if (!defined('UID')) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

//only for admins
if(!ADMIN) {
	error('Not permitted', 'This function is for admins only');
}

//set variables
$content = '';

//get config data
$q = db_query('SELECT * FROM data_table WHERE type="SECLVL"');


	$content .= "<script src=\"js/sorttable.js\"></script>";
	$content .= "<link rel=\"StyleSheet\" href=\"".BASE_CSS."table_default.css\" type=\"text/css\" />\n";

	//setup main table
	$content .= "<span class=\"textlink\">[<a href=\"admin.php?x=".$x."&amp;action=admin\">Return to Administration</a>]</span>";
	$content .= "<table><tr><td>\n";
	$content .= "<span class=\"textlink\">[<a href=\"admin.php?x=".$x."&amp;action=sec_lvl_add\">Add Security Level</a>]</span>";

if (db_numrows($q) > 0) {

	$content .= "</td></tr><tr><td><br />";
	//setup content table
	$content .= "<div class=\"mydiv\">";
	$content .= "  <table class=\"sortable\" id=\"sort_table\">\n";
	$content .= "    <thead>";
	$content .= "      <tr>";
	$content .= "        <th scope=\"col\">Security Level</th>";
	$content .= "        <th>&nbsp;</th>";
	$content .= "      </tr>";
	$content .= "    </thead>";
	$content .= "    <tbody>";

	//show all rows
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {

		if (($i % 2) == 1) {
			$content .= "      <tr>";
		} else {
			$content .= "      <tr class=\"odd\">";
		}

		//show name and a link
		$content .= "        <th scope=\"row\">";
		$content .= "<a href=\"admin.php?x=".$x."&amp;action=sec_lvl_edit&amp;id=".$row['id']."\"><b>".$row['description']."</b></a>\n";
		$content .= "</th>";
		$content .= "<td><a href=\"admin.php?x=".$x."&amp;action=sec_lvl_submit_delete&amp;id=".$row['id']."\"> <img src=\"images/icon-delete.gif\" alt=\"Remove Security Level\" /></a></td>\n";
		$content .= "      </tr>";
	}

	$content .= "    </tbody>";
	$content .= "  </table>";
	$content .= "</div>";
} //end if rows

	$content .= "</td></tr></table>\n";

	$content .= "</td></tr></table>";

new_box('Security Levels', $content);

?>
