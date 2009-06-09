<?php
/* $Id: user_mail.php,v 1.1 2009/04/22 00:05:05 markp Exp $ */

//security check
if (!defined('UID')) {
  die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

//set variables
$content = '';

//only for admins
if (!ADMIN) {
  error( 'Not permitted', 'This function is for admins only');
  return;
}

//start form data
$content .=
        "<form method=\"post\" action=\"users.php\">\n".
          "<fieldset><input type=\"hidden\" name=\"x\" value=\"".$x."\" />\n".
          "<input type=\"hidden\" name=\"action\" value=\"submit_email\" /></fieldset>\n".
          "<table class=\"celldata\">\n".
          "<tr><td></td><td>\n".
          "<table class=\"decoration\" cellpadding=\"5px\">\n".
          "<tr><td><input type=\"radio\" value=\"all\" name=\"group\" id=\"all\" checked=\"checked\" /><label for=\"all\">".$lang['all_users']."</label></td>\n".
          "<td><input type=\"radio\" value=\"maillist\" name=\"group\" id=\"maillist\" /><label for=\"maillist\">".$lang['mailing_list']."</label></td>\n".
          "<td><input type=\"radio\" value=\"group\" name=\"group\" id=\"group\" /><label for=\"group\">".$lang['select_usergroup']."</label></td></tr>\n";

//add user-groups
$q = db_query('SELECT name, id FROM usergroups ORDER BY name');
$content .=  "<tr><td></td><td>".$lang['usergroup'].":</td><td><label for=\"group\"><select name=\"usergroup[]\" multiple=\"multiple\" size=\"4\">\n";
for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
  $content .= "<option value=\"".$row['id']."\">".$row['name']."</option>";
}
$content .= "</select></label></td></tr>\n".
            "<tr><td></td><td></td><td><small><i>".$lang['select_instruct']."</i></small></td></tr>\n".
            "</table>\n".
            "</td></tr>\n".
            "<tr><td>".$lang['subject']."</td><td><input type=\"text\" name=\"subject\" size=\"60\" /></td></tr>\n".
            "<tr><td>".$lang['message']."</td><td><textarea name=\"message\" rows=\"10\" cols=\"60\"></textarea></td></tr>\n".
            "<tr><td></td><td>".$lang['message_sent_maillist']."</td></tr>\n".
            "</table>\n".
            "<p><input type=\"submit\" value=\"".$lang['post']."\" /></p>\n".
            "</form>\n";

new_box($lang['admin_email'], $content);
?>
