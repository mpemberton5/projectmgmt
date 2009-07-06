<?php
/* $Id$ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/time.php');
require_once(BASE.'includes/security.php');

//only for admins
if(!$_SESSION['ADMIN']) {
	error('Not permitted', 'This function is for admins only');
}


if (!@safe_integer($_REQUEST['id'])) {
  error('Module Edit', 'Not a valid Module identifier');
}
$level_id = $_REQUEST['id'];

//secure vars
$content = '';

$content .= "<form method=\"post\" action=\"admin.php\">\n";
//set some hidden values
$content .=  "<fieldset><input type=\"hidden\" name=\"x\" value=\"".$x."\" />".
             "<input type=\"hidden\" name=\"id\" value=\"".$id."\" />".
             "<input type=\"hidden\" name=\"action\" value=\"sec_lvl_submit_edit\" /></fieldset>\n";

//get the text from the parent and the username of the person that posted that text
//$q = db_query('SELECT * FROM security_levels WHERE level_id='.$level_id);
$q = db_query('SELECT * FROM data_table WHERE id='.$level_id);

if (!$row = db_fetch_array($q, 0)) {
  error("Security Level Edit", "Unable to Find ID");
}

$content .= "<br /><table>\n";
//build up the text-entry part
$content .= "<tr><td>Security Level Description:</td><td><input id=\"description\" name=\"description\" size=\"50\" value=\"".$row['description']."\" /></td></tr>\n";
$content .= "</table><p>\n";


$content .= "<p><input type=\"submit\" value=\"Save\" onclick=\"return fieldCheck()\" /></p>";
$content .= "</form>\n";

new_box('Edit Security Level', $content);
?>
