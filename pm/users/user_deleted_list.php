<?php
/* $Id$ */

//security check
if (!defined('UID')) {
  die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

//first check if we are admin
if (!ADMIN) {
  return;
}

//check for inactive users
if (!db_result(db_query('SELECT COUNT(*) FROM users WHERE security_level=\'0\''), 0, 0)) {
  $content = "<small>No Inactive Users</small>";
  new_box('Inactive Users', $content, "boxdata");
  return;
}

//query
$q = db_query('SELECT id, fullname FROM users WHERE security_level=\'0\' ORDER BY fullname');

$content = "<table class=\"celldata\">\n";

//show them
for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
  $content .= "<tr><td class=\"grouplist\"><a href=\"users.php?x=".$x."&amp;action=show&amp;user_id=".$row['id']."\">".$row['fullname']."</a></td>\n".
              "<td><span class=\"textlink\">";

  //if this user has NO tasks owned then we can inactivate them
  if (!db_result(db_query('SELECT COUNT(*) FROM tasks WHERE owner='.$row['id']), 0, 0)) {
    $content .= "[<a href=\"users.php?x=".$x."&amp;action=permdel&amp;user_id=".$row['id']."\" onclick=\"return confirm('".sprintf('This will INACTIVATE all user records and associated tasks for %s. Do you really want to do this?', javascript_escape($row['fullname']))."')\">Permdel</a>]&nbsp;";
  }

  $content .= "[<a href=\"users.php?x=".$x."&amp;action=revive&amp;user_id=".$row['id']."\">Restore</a>]".
              "</span></td></tr>\n";
}

$content .= "</table>";

//show it
new_box('Inactive Users', $content, 'boxdata');

?>
