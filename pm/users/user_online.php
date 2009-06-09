<?php
/* $Id: */

//security check
if (! defined('UID')) {
  die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

$content = '';

$content .= "<table>\n";
//users online in last hour
$q = db_query('SELECT logins.lastaccess AS last,
            users.id AS id,
            users.fullname AS fullname,
            FROM logins
            LEFT JOIN users ON (users.id=logins.user_id)
            WHERE logins.lastaccess > (now()-INTERVAL '.$delim.'1 HOUR'.$delim.')
            AND users.security_access>\'0\'
            ORDER BY logins.lastaccess DESC');

$content .= "<tr><td style=\"white-space:nowrap\" colspan=\"2\"><b>Currently Online (at least within the past hour)</b></td></tr>\n";
for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {

  //show output
  $content .= "<tr><td><a href=\"users.php?x=".$x."&amp;action=show&amp;user_id=".$row['id']."\">".$row['fullname']."</a></td><td>".nicetime($row['last'], 1)."</td></tr>\n";
}

$content .= "<tr><td style=\"white-space:nowrap\"colspan=\"2\">&nbsp;</td></tr>\n";
//users previously online
$q = db_query('SELECT logins.lastaccess AS last,
            users.id AS id,
            users.fullname AS fullname,
            FROM logins
            LEFT JOIN users ON (users.id=logins.user_id)
            WHERE logins.lastaccess < (now()-INTERVAL '.$delim.'1 HOUR'.$delim.')
            AND users.security_access>\'0\'
            ORDER BY logins.lastaccess DESC');

$content .= "<tr><td colspan=\"2\"><b>Previously Online</b></td></tr>\n";
for ($i=0 ; $row = @db_fetch_array($q, $i) ; ++$i) {

  //show output
  $content .= "<tr><td><a href=\"users.php?x=".$x."&amp;action=show&amp;user_id=".$row['id']."\">".$row['fullname']."</a></td><td>".nicetime($row['last'], 1)."</td></tr>\n";

}
$content .= "</table>\n";

new_box('User Activity', $content);

?>
