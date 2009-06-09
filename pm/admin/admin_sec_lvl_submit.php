<?php
/* $Id: admin_sec_lvl_submit.php,v 1.1 2009/04/22 00:05:06 markp Exp $ */

//security check
if (!defined('UID')) {
  die('Direct file access not permitted');
}

//includes
require_once('path.php');
require_once(BASE.'includes/security.php');

if (!isset($_REQUEST['action'])) {
  error('Message submit', 'No request given');
}

//if user aborts, let the script carry onto the end
ignore_user_abort(TRUE);

switch($_REQUEST['action']) {

  case 'sec_lvl_submit_add':
  //************************

    if (empty($_POST['description'])) {
      warning('Data Structures Submit', 'No Structure Description!  Please go back and try again');
    }
    $description = safe_data_long($_POST['description']);

    //start transaction
    db_begin();

    //store the new security level
    db_query ('INSERT INTO data_table(type,description) VALUES (\'SECLVL\',\''.$description.'\')');

    $lvl_id = db_lastoid('id');

    for ($i = 1; $i < 10; $i++) {
      $a = "";
      if (isset($_POST[$i])) {
        foreach ($_POST[$i] as $key => $value) {
          $a .= $value;
        }
      }
/*      if ($a <> "") db_query('INSERT INTO sec_lvl_priv(sec_lvl_id,module,perms) VALUES (\''.$lvl_id.'\',\''.$i.'\',\''.$a.'\')'); */
    }

    //transaction complete
    db_commit();

    break;

  //submit Edit
  case 'sec_lvl_submit_edit':
  //************************
    if (empty($_POST['description'])) {
      warning('Data Structures Submit', 'No Structure Description!  Please go back and try again');
    }
    $description = safe_data_long($_POST['description']);

    if (!@safe_integer($_REQUEST['id'])) {
      error('Data Structures Edit', 'Not a valid Structure identifier');
    }
    $id = $_REQUEST['id'];

    //start transaction
    db_begin();

    //public post
    db_query('UPDATE data_table SET
                description=\''.$description.'\'
                WHERE type=\'SECLVL\' AND id ='.$id);

		/***********************************************
    for ($i = 1; $i < 10; $i++) {
      $a = "";
      if (isset($_POST[$i])) {
        foreach ($_POST[$i] as $key => $value) {
          $a .= $value;
        }
      }
      if ($a <> "") {
        if (db_result(db_query('SELECT COUNT(*) FROM sec_lvl_priv WHERE sec_lvl_id='.$id.' AND module='.$i), 0, 0) == 1) {
          db_query('UPDATE sec_lvl_priv SET perms=\''.$a.'\' WHERE sec_lvl_id='.$id.' and module='.$i);
        } else {
          db_query('INSERT INTO sec_lvl_priv(sec_lvl_id,module,perms) VALUES (\''.$id.'\',\''.$i.'\',\''.$a.'\')');
        }
      } else {
        if (db_result(db_query('SELECT COUNT(*) FROM sec_lvl_priv WHERE sec_lvl_id='.$id.' AND module='.$i), 0, 0) == 1) {
          db_query('DELETE FROM sec_lvl_priv WHERE sec_lvl_id='.$id.' AND module='.$i);
        }
      }
    }
		*************************************/

    //transaction complete
    db_commit();

    break;

  case 'sec_lvl_submit_delete':
  //************************
    if (!@safe_integer($_REQUEST['id'])) {
      error('Security Level Edit', 'Not a valid Identifier');
    }
    $id = $_REQUEST['id'];

    //check if user is owner of the task or the owner of the post
    if (db_result(db_query('SELECT COUNT(*) FROM data_table WHERE id='.$id), 0, 0) == 1) {
      db_begin();
      db_query('DELETE FROM data_table WHERE id='.$id);
/***      db_query('DELETE FROM sec_lvl_priv WHERE sec_lvl_id='.$id); ***/
      db_commit();
    } else {
      error('Security Level Submit', 'Failure to find record to delete.');
    }

    break;
}

//go back to where this request came from
header('Location: '.BASE_URL.'admin.php?x='.$x.'&action=sec_lvl');

?>
