<?php
/* $Id: user_del.php,v 1.1 2009/04/22 00:05:05 markp Exp $ */

//security check
if (!defined('UID')) {
  die('Direct file access not permitted');
}

//includes
require_once('path.php');
require_once(BASE.'includes/security.php');

//admins only
if (!ADMIN) {
  error('Unauthorized access', 'This function is for admins only.');
}

//get some stupid errors
if (!@safe_integer($_GET['user_id'])) {
  error('User inactivate', 'No user_id specified');
}

$user_id = $_GET['user_id'];

if (empty($_GET['action'])) {
  error('User inactivate', 'No action specified');
}

//if user aborts, let the script carry onto the end
ignore_user_abort(TRUE);

switch($_GET['action']) {

  case 'del':

     //if user exists we can delete them
     if (db_result(db_query('SELECT COUNT(*) FROM users WHERE id='.$user_id), 0, 0)) {
       //mark user as deleted
       db_query('UPDATE users SET security_level=\'0\' WHERE id='.$user_id);

       //get the users' info
       $q = db_query('SELECT email FROM users WHERE id='.$user_id);
       $email = db_result($q, 0, 0);

       //mail the user that he/she had been deleted
       include_once(BASE.'lang/lang_email.php');
       email($email, $title_delete_user, $email_delete_user);
     }
    break;

  default:
    error('User inactivate action handler', 'Invalid request given');
    break;

}

header('Location: '.BASE_URL.'users.php?x='.$x.'&action=manage');

?>
