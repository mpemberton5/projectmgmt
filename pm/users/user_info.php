<?php
/* $Id: user_info.php,v 1.1 2009/04/22 00:05:05 markp Exp $ */

//security check
if (!defined('UID')) {
  die('Direct file access not permitted');
}

//admins only
if (!ADMIN) {
  error('Unauthorized access', 'This function is for Administrators only.');
}

$content = $user_info;

new_box('Manage Users', $content, 'boxdata2');

?>
