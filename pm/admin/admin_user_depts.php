<?php
/* $Id: admin_user_depts.php,v 1.1 2009/06/08 21:13:03 markp Exp $ */

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

//set variables
$content = '';
$maillist = '';

//start form data
$content .=
          "<form method=\"post\" action=\"admin.php\">\n".
          "<fieldset><input type=\"hidden\" name=\"x\" value=\"".$x."\" />\n".
          "<input type=\"hidden\" name=\"action\" value=\"submit\" /></fieldset>\n".
          "<table class=\"celldata\" >\n";

//get config data
$q = db_query('SELECT * FROM config');
$row = db_fetch_array($q, 0);

if (USE_EMAIL === 'Y') {

  $content .=
            "<tr><td style=\"white-space : nowrap\" colspan=\"2\"><b>Email Header Settings</b><br /><br /></td></tr>\n";

  //email addresses
  $content .=
            "<tr><td><a href=\"help/help_language.php?item=admin&amp;type=admin\" onclick=\"window.open('help/help_language.php?item=admin&amp;type=admin'); return false\" >Admin Email</a>:</td><td><input type=\"text\" name=\"email_admin\" value=\"".$row['email_admin']."\" size=\"30\" /></td></tr>\n".
            "<tr><td><a href=\"help/help_language.php?item=reply&amp;type=admin\" onclick=\"window.open('help/help_language.php?item=reply&amp;type=admin'); return false\">Email 'reply to'</a>:</td><td><input type=\"text\" name=\"reply_to\" value=\"".$row['reply_to']."\" size=\"30\" /></td></tr>\n".
            "<tr><td><a href=\"help/help_language.php?item=from&amp;type=admin\" onclick=\"window.open('help/help_language.php?item=from&amp;type=admin'); return false\">Email 'From'</a>:</td><td><input type=\"text\" name=\"from\" value=\"".$row['email_from']."\" size=\"30\" /></td></tr>\n";

  //get mailing list
  $q = db_query('SELECT DISTINCT email FROM maillist');

  for($i=0 ; $row_mail = @db_fetch_array($q, $i) ; ++$i) {
    $maillist .= $row_mail['email']."\n";
  }

  $content .= "<tr><td><a href=\"help/help_language.php?item=list&amp;type=admin\" onclick=\"window.open('help/help_language.php?item=list&amp;type=admin'); return false\">".$lang['mailing_list']."</a>: </td><td><textarea name=\"email\" rows=\"5\" cols=\"30\">".$maillist."</textarea></td></tr>\n".
               "</table>\n".
               "<table class=\"celldata\" >\n";
}

$content .= "<tr><td colspan=\"2\"><b>".$lang['default_checkbox']."</b><br /><br /></td></tr>\n";

//defaults for task checkboxes
$content .= "<tr><td><label for=\"access\">".$lang['allow_globalaccess']."</label></td><td><input type=\"checkbox\" name=\"access\" id=\"access\" ".$row['globalaccess']." /></td></tr>\n".
            "<tr><td><label for=\"group_edit\">".$lang['allow_group_edit']."</label></td><td><input type=\"checkbox\" name=\"group_edit\" id=\"group_edit\" ".$row['groupaccess']." /></td></tr>\n".
            "<tr><td><label for=\"owner\">".$lang['set_email_owner']."</label></td><td><input type=\"checkbox\" name=\"owner\" id=\"owner\" ".$row['owner']." /></td></tr>\n".
            "<tr><td><label for=\"usergroup\">".$lang['set_email_group']."</label></td><td><input type=\"checkbox\" name=\"usergroup\" id=\"usergroup\" ".$row['usergroup']." /></td></tr>\n";

//set default selection for project listing
switch($row['project_order']) {
  case 'ORDER BY due ASC, name':
    $s1 = ""; $s2 = " selected=\"selected\""; $s3 = "";
    break;

  case 'ORDER BY priority DESC, name':
    $s1 = ""; $s2 = ""; $s3 = " selected=\"selected\"";
    break;

  case 'ORDER BY name':
  default:
    $s1 = " selected=\"selected\""; $s2 = ""; $s3 = "";
    break;
}

//project listing order
$content .= "<tr><td>".$lang['project_listing_order'].":</td><td>\n".
            "<select name=\"project_order\">\n".
            "<option value=\"name\"".$s1.">".$lang['name']."</option>\n".
            "<option value=\"deadline\"".$s2.">".$lang['deadline']."</option>\n".
            "<option value=\"priority\"".$s3.">".$lang['priority']."</option>\n".
            "</select></td></tr>\n";

//set default selection for task listing
switch($row['task_order']) {
  case 'ORDER BY due ASC, name':
    $s1 = ""; $s2 = " selected=\"selected\""; $s3 = "";
    break;

  case 'ORDER BY priority DESC, name':
    $s1 = ""; $s2 = ""; $s3 = " selected=\"selected\"";
    break;

  case 'ORDER BY name':
  default:
    $s1 = " selected=\"selected\""; $s2 = ""; $s3 = "";
    break;
}

//task listing order
$content .= "<tr><td>".$lang['task_listing_order'].":</td><td>\n".
            "<select name=\"task_order\">\n".
            "<option value=\"name\"".$s1.">".$lang['name']."</option>\n".
            "<option value=\"deadline\"".$s2.">".$lang['deadline']."</option>\n".
            "<option value=\"priority\"".$s3.">".$lang['priority']."</option>\n".
            "</select></td></tr>\n".
            "</table>\n";

$content .=
          "<p><input type=\"submit\" value=\"".$lang['update']."\" /></p>\n".
        "</form>\n";

new_box('User Groups', $content);

?>
