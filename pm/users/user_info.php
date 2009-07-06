<?php
/* $Id$ */

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
