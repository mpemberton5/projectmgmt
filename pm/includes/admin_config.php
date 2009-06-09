<?php
/* $Id: admin_config.php,v 1.1 2009/04/22 00:05:07 markp Exp $ */

//security check
//if (!defined('UID')) {
//  die('Direct file access not permitted');
//}

//get config data
$q = db_query('SELECT * FROM config');
$row = @db_fetch_array($q, 0);

//set variables
define('EMAIL_REPLY_TO', $row['reply_to']);
define('EMAIL_FROM',     $row['email_from']);
define('EMAIL_ADMIN',    $row['email_admin']);
define('DEFAULT_OWNER',  $row['owner']);

?>
