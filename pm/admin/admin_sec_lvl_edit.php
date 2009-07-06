<?php
/* $Id$ */

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

//secure vars
$content = '';

$content .= "<span class=\"textlink\">[<a href=\"admin.php?x=".$x."&amp;action=sec_lvl\">Return to Security Levels</a>]</span>\n";

$content .= "<form method=\"post\" action=\"admin.php\">\n";
//set some hidden values
$content .=  "<fieldset><input type=\"hidden\" name=\"x\" value=\"".$x."\" />".
             "<input type=\"hidden\" name=\"id\" value=\"\" />".
             "<input type=\"hidden\" name=\"action\" value=\"sec_lvl_submit_add\" /></fieldset>\n";

$content .= "<br /><table>\n";
//build up the text-entry part
$content .= "<tr><td>New Security Level Description:</td><td><input id=\"description\" name=\"description\" size=\"50\" /></td></tr>\n";
$content .= "</table><p>\n";

/**************************************************************8
$content .= "<table>\n";
$content .= "    <thead>";
$content .= "      <tr>";
$content .= "        <th scope=\"col\">Module</th>";
$content .= "        <th width=\"10%\" scope=\"col\" align=\"center\">Add</th>";
$content .= "        <th width=\"10%\" scope=\"col\" align=\"center\">Edit</th>";
$content .= "        <th width=\"10%\" scope=\"col\" align=\"center\">Delete</th>";
$content .= "        <th width=\"10%\" scope=\"col\" align=\"center\">View</th>";
$content .= "        <th>&nbsp;</th>";
$content .= "      </tr>";
$content .= "    </thead>";
$content .= "    <tbody>";


//get config data
$q = db_query('SELECT * FROM modules');

  //show all rows
  for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
    if (($i % 2) == 1) {
      $content .= "      <tr>";
    } else {
      $content .= "      <tr class=\"odd\">";
    }
    $content .= "<td>".$row['description']."</td>\n";
    $content .= "<td align=\"center\"><input type=\"checkbox\" name=\"".$row['id']."[]\" value=\"A\"></td>\n";
    $content .= "<td align=\"center\"><input type=\"checkbox\" name=\"".$row['id']."[]\" value=\"E\"></td>\n";
    $content .= "<td align=\"center\"><input type=\"checkbox\" name=\"".$row['id']."[]\" value=\"D\"></td>\n";
    $content .= "<td align=\"center\"><input type=\"checkbox\" name=\"".$row['id']."[]\" value=\"V\"></td>\n";
    $content .= "</tr>\n";
  }

$content .= "</tbody>\n";
$content .= "</table>\n";

***********************************************/

$content .= "<p><input type=\"submit\" value=\"Add\" onclick=\"return fieldCheck()\" /></p>";
$content .= "</form>\n";

new_box('Add Security Level', $content);
?>
