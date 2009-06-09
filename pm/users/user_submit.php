<?php
/* $Id: */

//security check
if (!defined('UID')) {
  die('Direct file access not permitted');
}

//includes
require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/email.php');
include_once(BASE.'includes/time.php');

$admin_state = '';

//update or insert ?
if (empty($_REQUEST['action'])) {
  error('User submit', 'No request given');
}

//if user aborts, let the script carry onto the end
ignore_user_abort(TRUE);

switch($_REQUEST['action']) {

  //revive a user
  //************************************************************************
  case 'revive':

    //only for the admins
    if (!ADMIN) {
      error('Authorization failed', 'You have to be admin to do this');
    }
    if (!@safe_integer($_GET['user_id'])) {
      error('User submit', 'No user_id was specified.');
    }

    $user_id = $_GET['user_id'];

    if (db_result(db_query('SELECT COUNT(*) FROM users WHERE security_level=\'0\' AND id='.$user_id), 0, 0)) {
      //undelete
      db_query('UPDATE users SET security_level=\'1\' WHERE id='.$user_id);

      //get the users' info
      $q = db_query('SELECT login_name, CONCAT(firstname," ",lastname) as fullname, email FROM users where id='.$user_id);
      $row = db_fetch_array($q, 0);

      //mail the user the happy news :)
      $message = sprintf($email_revive, $row['login_name'], $row['fullname']);
      email($row['email'], $title_revive, $message);
    }
    break;


  //add a user
  //************************************************************************
  case 'submit_insert':

    //only for the admins
    if (!ADMIN) {
      error('Authorization failed', 'You have to be admin to do this');
    }

    //GET REQUIRED FIELDS
    $input_array = array('login_name', 'firstname', 'lastname');
    foreach ($input_array as $var) {
      if (empty($_POST[$var])) {
        warning('Value Missing', sprintf('The field for %s is missing. Please go back and fill it in.', $var));
      }
      ${$var} = safe_data($_POST[$var]);
    }

		//GET PASSWORD
    if (empty($_POST['password'])) {
      warning('Value Missing', sprintf('The field for %s is missing. Please go back and fill it in.', 'password'));
    }
    $password_unclean = trim($_POST['password']);

		//GET EMAIL
    $email_raw = validate($_POST['email']);
    if ((!preg_match('/\b[a-z0-9\.\_\-]+@[a-z0-9][a-z0-9\.\-]+\.[a-z\.]+\b/i', $email_raw, $match)) || (strlen(trim($email_raw)) > 200)) {
      warning('Invalid Email Address', sprintf('The email address %s is invalid.  Please go back and try again.', $_POST['email']));
    }
    $email_unclean = $match[0];

		//GET SECURITY LEVEL
    $security_level = (isset($_POST['security_level']) && ($_POST['security_level'] < "100")) ? $_POST['security_level'] : 1;

    //GET ALL OTHER FIELDS
    $input_array = array('prefix','billing_status','secret_question_id','secret_question_answer','business_category',
													'bus_name','bus_addr1','bus_addr2','bus_city','bus_state','bus_zip','bus_url','address_default',
													'title','notes','mls_location','mls_id','address1','address2','city','state','zip','bus_phone',
													'cell_phone','home_phone','pager','fax','usergroup_id');
    foreach ($input_array as $var) {
			if (isset($_POST[$var])) {
				${$var} = safe_data($_POST[$var]);
			} else {
				${$var} = "";
      }
    }

    //prohibit 2 people from choosing the same username - this is a double safeguard
    if (db_result(db_query('SELECT COUNT(*) FROM users WHERE login_name=\''.$login_name.'\'', 0), 0, 0) > 0) {
      warning('Duplicate User', sprintf('The user %s already exists.  Please go back change one name.', $login_name));
    }

    //begin transaction
    db_begin();
    //insert into the users table
    $q = db_query("INSERT INTO users(login_name, firstname, lastname, password, email, security_level, mls_location, mls_id, address1, address2,
									city, state, zip, bus_phone, cell_phone, home_phone, pager, fax, group_id, prefix, billing_status, secret_question_id,
									secret_question_answer, business_category, bus_name, bus_addr1, bus_addr2, bus_city, bus_state, bus_zip, bus_url, address_default,
									title, notes)
                  VALUES('$login_name', '$firstname', '$lastname', '".md5($password_unclean)."', '".db_escape_string($email_unclean)."',
									'$security_level','$mls_location', '$mls_id', '$address1', '$address2', '$city', '$state', '$zip', '$bus_phone', '$cell_phone',
									'$home_phone', '$pager', '$fax', '$usergroup_id','$prefix', '$billing_status', '$secret_question_id', '$secret_question_answer',
									'$business_category', '$bus_name', '$bus_addr1', '$bus_addr2', '$bus_city', '$bus_state', '$bus_zip', '$bus_url', '$address_default',
									'$title', '$notes')");

    //transaction complete
    db_commit();

//    $admin_state = ($admin_user == 't') ? "NOTE: You have been granted administrator privileges.\n" : '';

    $name_unclean     = validate($_POST['login_name']);
    $fullname_unclean = validate($_POST['firstname']) . " " . validate($_POST['lastname']);
    $password_unclean = validate($_POST['password']);

    $message = sprintf($email_welcome, $name_unclean, $password_unclean, '',
                $fullname_unclean, $admin_state);
    email($email_unclean, $title_welcome, $message);

    break;


  //edit a user
  //************************************************************************
  case 'submit_edit':

    //GET REQUIRED FIELDS
    $input_array = array('firstname', 'lastname');
    foreach($input_array as $var) {
      if (empty($_POST[$var])) {
        warning('Value Mising', sprintf('The field for %s is missing. Please go back and fill it in.', $var));
      }
      ${$var} = safe_data($_POST[$var]);
    }

    //get new password, if any
    $password_unclean = (empty($_POST['password1'])) ? '' : trim($_POST['password1']);
    //magic quotes is not required
    $email_raw = validate($_POST['email']);

    if ((!preg_match('/\b[a-z0-9\.\_\-]+@[a-z0-9][a-z0-9\.\-]+\.[a-z\.]+\b/i', $email_raw, $match)) || (strlen(trim($email_raw)) > 200)) {
      warning('Invalid Email Address', sprintf('The email address %s is invalid.  Please go back and try again.', $_POST['email']));
    }
    $email_unclean = $match[0];

		// ONLY ADMINS CAN CHANGE OTHER USER INFO
    if (ADMIN) {
      if (!@safe_integer($_POST['user_id'])) {
        error('User submit', 'No user_id specified');
      }
      $user_id = safe_data($_POST['user_id']);
		} else {
      $user_id = UID;
		}

    $security_level = (isset($_POST['security_level']) && ($_POST['security_level'] < "100")) ? $_POST['security_level'] : 1;

    //GET ALL OTHER FIELDS
    $input_array = array('prefix','billing_status','secret_question_id','secret_question_answer','business_category',
													'bus_name','bus_addr1','bus_addr2','bus_city','bus_state','bus_zip','bus_url','address_default',
													'title','notes','mls_location','mls_id','address1','address2','city','state','zip','bus_phone',
													'cell_phone','home_phone','pager','fax','usergroup_id');
    foreach ($input_array as $var) {
			if (isset($_POST[$var])) {
				${$var} = safe_data($_POST[$var]);
			} else {
				${$var} = "";
      }
    }

    $sql = "UPDATE users SET";
		$sql .= " firstname      = '$firstname'";
		$sql .= ",lastname       = '$lastname'";
		$sql .= ",email          = '".db_escape_string($email_unclean)."'";
		$sql .= ",mls_location   = '$mls_location'";
		$sql .= ",mls_id         = '$mls_id'";
		$sql .= ",address1       = '$address1'";
		$sql .= ",address2       = '$address2'";
		$sql .= ",city           = '$city'";
		$sql .= ",state          = '$state'";
		$sql .= ",zip            = '$zip'";
		$sql .= ",bus_phone      = '$bus_phone'";
		$sql .= ",cell_phone     = '$cell_phone'";
		$sql .= ",home_phone     = '$home_phone'";
		$sql .= ",pager          = '$pager'";
		$sql .= ",fax            = '$fax'";
		$sql .= ",prefix         = '$prefix'";
		$sql .= ",secret_question_id = '$secret_question_id'";
		$sql .= ",secret_question_answer = '$secret_question_answer'";
		$sql .= ",business_category = '$business_category'";
		$sql .= ",bus_name       = '$bus_name'";
		$sql .= ",bus_addr1      = '$bus_addr1'";
		$sql .= ",bus_addr2      = '$bus_addr2'";
		$sql .= ",bus_city       = '$bus_city'";
		$sql .= ",bus_state      = '$bus_state'";
		$sql .= ",bus_zip        = '$bus_zip'";
		$sql .= ",bus_url        = '$bus_url'";
		$sql .= ",address_default = '$address_default'";
		$sql .= ",title          = '$title'";
		$sql .= ",notes          = '$notes'";

    //was a password provided or not ?
    if ($password_unclean != '') {
			$sql .= ",password     = '".md5($password_unclean)."'";
		}

		//FIELDS THAT ONLY ADMINS CAN UPDATE
    if (ADMIN) {
			$sql .= ",group_id       = '$usergroup_id'";
			$sql .= ",security_level = '$security_level'";
			$sql .= ",billing_status = '$billing_status'";
		}

		// ADD THE WHERE CLAUSE
		$sql .= " WHERE id = $user_id";

    //begin transaction
    db_begin();

    $q = db_query($sql);

    //transaction complete
    db_commit();

    if ($password_unclean == '') {
      $password_unclean = '(Your existing password has not changed)';
    } else {
      $password_unclean = validate($_POST['password1']);
    }

//    $admin_state = ($admin_user == 't') ? "NOTE: You have been granted administrator privileges.\n" : '';

    $name_unclean     = validate($_POST['login_name']);
    $fullname_unclean = validate($_POST['firstname']) . " " . validate($_POST['lastname']);

    //email the changes to the user
    $message = sprintf($email_user_change1, UID_NAME, UID_EMAIL, $name_unclean,
            $password_unclean, '', $fullname_unclean, $admin_state);
    email($email_unclean, $title_user_change1, $message);

    break;


  //default error
  //************************************************************************
  default:
    error('User submit', 'Invalid request given');
    break;
}

header("Location: ".BASE_URL."admin.php?x=".$x."&action=users");
die;

?>
