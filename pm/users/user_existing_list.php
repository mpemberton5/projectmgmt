<?php
/* $Id$ */

//security check
if (!defined('UID')) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/time.php');
require_once(BASE.'includes/security.php');

//only for admins
if(!ADMIN) {
	error('Not permitted', 'This function is for admins only');
}

$content = '';

//get config data
//$q = db_query('SELECT * FROM users WHERE security_level>\'0\' ORDER by lastname,firstname');
$q = db_query('SELECT * FROM users ORDER by lastname,firstname');

if (db_numrows($q) > 0) {

	$content .= "<script src=\"js/sorttable.js\"></script>";
	$content .= "<link rel=\"StyleSheet\" href=\"".BASE_CSS."table_default.css\" type=\"text/css\" />\n";

	//setup main table
	$content .= "<span class=\"textlink\">[<a href=\"admin.php?x=".$x."&amp;action=admin\">Return to Administration</a>]</span>";
	$content .= "<table><tr><td>\n";
	$content .= "<span class=\"textlink\">[<a href=\"users.php?x=".$x."&amp;action=add\">Add New User</a>]</span>\n";
	$content .= "</td></tr><tr><td>&nbsp;";

	//setup content table
	$content .= "<div class=\"mydiv\">";
	$content .= "  <table class=\"sortable\" id=\"sort_table\">\n";
	$content .= "    <thead>";
	$content .= "      <tr>";
	$content .= "        <th scope=\"col\">Users</th>";
	$content .= "        <th scope=\"col\">Login</th>";
	$content .= "        <th scope=\"col\">Join Date</th>";
	$content .= "        <th scope=\"col\">Security Level</th>";
	$content .= "        <th scope=\"col\">Billing Status</th>";
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

		$sec_lvl = db_result(db_query('SELECT description FROM data_table WHERE type="SECLVL" AND id="'.$row['security_level'].'"'), 0, 0);
		$bill_stat = db_result(db_query('SELECT description FROM data_table WHERE type="BILLSTAT" AND id="'.$row['billing_status'].'"'), 0, 0);

		//show name and a link
		$content .= "        <th scope=\"row\">";
		$content .= "<a href=\"users.php?x=".$x."&amp;user_id=".$row['id']."&amp;action=edit\"><b>".$row['firstname']." ".$row['lastname']."</b></a>\n";
		$content .= "</th>";
		$content .= "<td>".$row['login_name']."</td>";
		$content .= "<td>".nicedate($row['join_date'])."</td>";
		$content .= "<td>".$sec_lvl."</td>";
		$content .= "<td>".$bill_stat."</td>";
		$content .= "<td><a href=\"users.php?x=".$x."&amp;user_id=".$row['id']."&amp;action=del\"><img src=\"images/icon-delete.gif\" alt=\"Remove User\" /></a></td>\n";
		$content .= "</tr>\n";
	}

	$content .= "    </tbody>";
	$content .= "  </table>";
	$content .= "</div>";

	$content .= "</table>\n";

	$content .= "</td></tr></table>";
} //end if rows

//show it
new_box('Existing Users', $content, 'boxdata');
?>
