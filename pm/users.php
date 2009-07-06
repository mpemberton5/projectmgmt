<?php

/* $Id$ */

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/screen.php');
include_once(BASE.'includes/time.php');

//
// action handler
//
if (!isset($_REQUEST['action'])) {
  error('Users action handler', 'No request given');
}

// Users Action
switch($_REQUEST['action']) {

  //show user's personal details
  case 'show':
    create_complete_top('User Information');
/*
    include(BASE.'includes/mainmenu.php');
    include(BASE.'users/user_menubox.php');
    include(BASE.'users/user_existing_menubox.php');
    goto_main();
*/
    include(BASE.'users/user_show.php');
    create_bottom();
    break;

  //who is online ?
  case 'showonline':
    create_complete_top('Users Online');
/*
    include(BASE.'includes/mainmenu.php');
    include(BASE.'users/user_menubox.php');
    include(BASE.'users/user_existing_menubox.php');
    goto_main();
*/
    include(BASE.'users/user_online.php');
    create_bottom();
    break;

  //give the user-manager screen
  case 'manage':
    create_complete_top('Manage Users');
/*
    include(BASE.'includes/mainmenu.php');
    include(BASE.'users/user_menubox.php');
    goto_main();
*/
    include(BASE.'users/user_existing_list.php');
    if (ADMIN) {
      include(BASE.'users/user_deleted_list.php');
      //include(BASE.'users/user_info.php');
    }
    create_bottom();
    break;

  //Add a user
  case 'add':
    create_complete_top('Add Users', 0, 'name');
/*
    include(BASE.'includes/mainmenu.php');
    include(BASE.'users/user_menubox.php');
    goto_main();
*/
    include(BASE.'users/user_add.php');
    create_bottom();
    break;

  //Edit a user
  case 'edit':
    create_complete_top('Edit User');
/*
    include(BASE.'includes/mainmenu.php');
//    include(BASE.'users/user_menubox.php');
    goto_main();
*/
    include(BASE.'users/users_edit.php');
    create_bottom();
    break;

  //admin email
  case 'email':
    create_complete_top('Email:');
/*
    include(BASE.'includes/mainmenu.php');
    goto_main();
*/
    include(BASE.'users/user_mail.php');
    create_bottom();
    break;

  //submit email to submission engine
  case 'submit_email':
    include(BASE.'users/user_mail_send.php');
    break;

  //submit insert/update to submission engine
  case 'submit_insert':
  case 'submit_edit':
  case 'revive':
    include(BASE.'users/user_submit.php');
    break;

  //delete to submission engine
  case 'del':
  case 'permdel':
    include(BASE.'users/user_del.php');
    break;

  //Error case
  default:
    error('Users action handler', 'Invalid request given');
    break;
}

?>
