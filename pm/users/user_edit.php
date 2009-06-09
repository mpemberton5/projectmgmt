<?php
/* $Id: user_edit.php,v 1.1 2009/04/22 00:05:05 markp Exp $ */

//security check
if (!defined('UID')) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

//secure vars
$user_id = '';
$content = '';

//is an admin everything can be queried otherwise only yourself can be queried
if (ADMIN) {
	//is there a uid ?
	if (!safe_integer($_REQUEST['user_id'])) {
		error('User edit', 'No user_id was specified');
	}
	$user_id = $_REQUEST['user_id'];

	//query for user
	$q = db_query('SELECT * FROM users WHERE id='.$user_id);

} else {
	//user
	$q = db_query('SELECT * FROM users WHERE id='.UID);
	$user_id = UID;
}

//fetch data
if (!($row = db_fetch_array($q , 0))) {
	error('Database result', 'Error in retrieving user-data from database');
}

//show data
$content .= "<form method=\"post\" action=\"users.php\">\n";
$content .= "<fieldset><input type=\"hidden\" name=\"action\" value=\"submit_edit\" />\n";
$content .= "<input type=\"hidden\" name=\"x\" value=\"".$x."\" />\n";
$content .= "<input type=\"hidden\" name=\"user_id\" value=\"$user_id\" /></fieldset>\n";
$content .= "<table class=\"celldata\">";
$content .= "<tr><td>Login Name:</td><td><input type=\"text\" name=\"login_name\" size=\"30\" value=\"".html_escape($row['login_name'])."\" /></td></tr>\n";
$content .= "<tr><td>Full Name:</td><td><input type=\"text\" name=\"fullname\" size=\"30\" value=\"".html_escape($row['fullname'])."\" /></td></tr>\n";
$content .= "<tr><td>Password:</td><td><input type=\"text\" name=\"password\" size=\"30\" value=\"\" /></td><td><small><i>(Leave blank for current password)</i></small></td></tr>\n";
$content .= "<tr><td>Email Address:</td><td><input type=\"text\" name=\"email\" size=\"30\" value=\"".$row['email']."\" /></td></tr>\n";

//Security Level
if (ADMIN) {
	$content .= "<tr><td><label for=\"security_level\">Security Level:</label></td> <td><select name=\"security_level\">\n";
	$content .= "<option value=\"0\">Disabled</option>\n";

	$r = db_query('SELECT id, description FROM data_table WHERE type="SECLVL" ORDER BY id');

	for ($i=0; $level_row = @db_fetch_array($r, $i); ++$i) {

		$content .= "<option value=\"".$level_row['id']."\"";

		if ($row['security_level'] == $level_row['id']) {
			$content .= " selected=\"selected\" >";
		} else {
			$content .= ">";
		}
		$content .= $level_row['description']."</option>\n";
	}
	$content .= "</select></td></tr>\n";
} else {
	$sec_lvl = db_result(db_query('SELECT description FROM data_table WHERE type="SECLVL" AND id='.$row['security_level']), 0, 0);
	$content .= "<tr><td>Security Level: </td> <td>".$sec_lvl."</td></tr>\n";
}


$content .= "<tr><td><label for=\"mls_location\">MLS Location:</label></td><td><input type=\"text\" name=\"mls_location\" size=\"30\" value=\"".$row['mls_location']."\" /></td></tr>\n";
$content .= "<tr><td><label for=\"mls_id\">MLS ID:</label></td><td><input type=\"text\" name=\"mls_id\" size=\"30\" value=\"".$row['mls_id']."\" /></td></tr>\n";
$content .= "<tr><td><label for=\"address1\">Address 1:</label></td><td><input type=\"text\" name=\"address1\" size=\"30\" value=\"".$row['address1']."\" /></td></tr>\n";
$content .= "<tr><td><label for=\"address2\">Address 2:</label></td><td><input type=\"text\" name=\"address2\" size=\"30\" value=\"".$row['address2']."\" /></td></tr>\n";
$content .= "<tr><td><label for=\"city\">City:</label></td><td><input type=\"text\" name=\"city\" size=\"30\" value=\"".$row['city']."\" /></td></tr>\n";
$content .= "<tr><td><label for=\"state\">State:</label></td><td><input type=\"text\" name=\"state\" size=\"30\" value=\"".$row['state']."\" /></td></tr>\n";
$content .= "<tr><td><label for=\"zip\">Zip:</label></td><td><input type=\"text\" name=\"zip\" size=\"30\" value=\"".$row['zip']."\" /></td></tr>\n";
$content .= "<tr><td><label for=\"bus_phone\">Office Phone:</label></td><td><input type=\"text\" name=\"bus_phone\" size=\"30\" value=\"".$row['bus_phone']."\" /></td></tr>\n";
$content .= "<tr><td><label for=\"cell_phone\">Cell Phone:</label></td><td><input type=\"text\" name=\"cell_phone\" size=\"30\" value=\"".$row['cell_phone']."\" /></td></tr>\n";
$content .= "<tr><td><label for=\"home_phone\">Home Phone:</label></td><td><input type=\"text\" name=\"home_phone\" size=\"30\" value=\"".$row['home_phone']."\" /></td></tr>\n";
$content .= "<tr><td><label for=\"pager\">Pager:</label></td><td><input type=\"text\" name=\"pager\" size=\"30\" value=\"".$row['pager']."\" /></td></tr>\n";
$content .= "<tr><td><label for=\"fax\">Fax:</label></td><td><input type=\"text\" name=\"fax\" size=\"30\" value=\"".$row['fax']."\" /></td></tr>\n";

//Usergroups
if (ADMIN) {
	$content .= "<tr><td><label for=\"usergroup_id\">UserGroup:</label></td> <td><select name=\"usergroup_id\">\n";
	$content .= "<option value=\"0\">No Group</option>\n";

	$r = db_query('SELECT group_id, name FROM usergroups ORDER BY name');

	for ($i=0; $usergroup_row = @db_fetch_array($r, $i); ++$i) {

		$content .= "<option value=\"".$usergroup_row['group_id']."\"";

		if ($row['group_id'] == $usergroup_row['group_id']) {
			$content .= " selected=\"selected\" >";
		} else {
			$content .= ">";
		}
		$content .= $usergroup_row['name']."</option>\n";
	}
	$content .= "</select></td></tr>\n";
} else {
	if ($row['group_id'] > 0) {
		$groupname = db_result(db_query('SELECT name FROM usergroups WHERE group_id='.$row['group_id']), 0, 0);
		$content .= "<tr><td>UserGroup: </td> <td>".$groupname;
	} else {
		$content .= "<tr><td>UserGroup: </td> <td>NONE";
	}
	$content .= "</td></tr>\n";
}

$content .= "</table>\n";
$content .= "<p><input type=\"submit\" value=\"Submit Changes\" /></p>\n";
$content .= "</form>\n";

new_box('Edit User', $content);

?>
