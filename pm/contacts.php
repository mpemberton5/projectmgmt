<?php
/* $Id: contacts.php,v 1.1 2009/04/22 00:05:06 markp Exp $ */

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/screen.php');

//
// The action handler
//
if (!isset($_REQUEST['action'])) {
  error('Contacts action handler', 'No request given');
}

// set this because the javascript is set globally to persist the tabs
setcookie('contacttab','');

// Contact Action
switch($_REQUEST['action']) {

  //Select Contact from Business List
  case 'sel_contact_popup':
    create_complete_top('Select Contact',1);
    include(BASE.'contacts/contacts_select_popup.php');
    create_bottom();
    break;

  //Select Contact from Business List
  case 'sel_contact_popup1':
    create_complete_top('Select Contact',1);
    include(BASE.'contacts/contacts_select_popup1.php');
    create_bottom();
    break;

  //gives a window and some options to do to the poor 'old contact manager
  case 'add_popup':
    create_complete_top('Add Contact', 1, 'prefix');
    include(BASE.'contacts/contact_add.php');
    create_bottom();
    break;

  //gives a window and some options to do to the poor 'old contact manager
  case 'invite':
    create_complete_top('Invite Contact');
    include(BASE.'includes/mainmenu.php');
    goto_main();
    include(BASE.'contacts/contact_invite.php');
    create_bottom();
    break;

  //gives a window and some options to do to the poor 'old contact manager
  case 'manage':
    create_complete_top('Show Contacts');
    include(BASE.'includes/mainmenu.php');
    goto_main();
    include(BASE.'contacts/contacts_manage.php');
    create_bottom();
    break;

  //gives a window and some options to do to the poor 'old contact manager
  case 'show':
    create_complete_top('Show Contacts');
    include(BASE.'includes/mainmenu.php');
    goto_main();
    include(BASE.'contacts/contact_show.php');
    create_bottom();
    break;

  //gives a window and some options to do to the poor 'old contact manager
  case 'add':
    create_complete_top('Add Contact', 0, 'prefix');
    include(BASE.'includes/mainmenu.php');
    goto_main();
    include(BASE.'contacts/contact_add.php');
    create_bottom();
    break;

  case 'edit':
    create_complete_top('Edit Contact');
    include(BASE.'includes/mainmenu.php');
    goto_main();
    include(BASE.'contacts/contact_edit.php');
    create_bottom();
    break;

  case 'submit_invite':
  case 'submit_add':
  case 'submit_edit':
  case 'submit_delete':
  case 'remove_from_task':
	case 'dissociate':
    include(BASE.'contacts/contact_submit.php');
    break;

  //Error case
  default:
    error('Contacts action handler', 'Invalid request given');
    break;
}

?>
