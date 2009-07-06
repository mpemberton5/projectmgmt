<?php
/* $Id$ */

//get includes
require_once('path.php');
require_once(BASE.'path_config.php');
require_once(BASE_CONFIG.'config.php');
require_once(BASE.'includes/common.php');
require_once(BASE.'database/database.php');


// Run Each Time User Accesses page
InitSession();


/**
 * Error Condition
 *
 * @param $error
 * @param $redirect
 * @return nothing
 */
function secure_error($error = 'Login error', $redirect=0) {

	if ($redirect == 1) {
		$redirect_time = 15;
	} else {
		$redirect_time = 0;
	}

	$content = "<div style=\"text-align : center\"><br />$error<br /></div>";
	create_complete_top('Login', 1, '', '', '', '', $redirect_time);
	new_box('Error', $content, 'boxdata', 'singlebox');

	if ($redirect_time != 0) {
		$content = "<div style=\"text-align : center\"><a href=\"".BASE_URL."index.php\">Please click here to return to Login now</a></div>\n";
		new_box(sprintf('You will automatically return to Login after a %d second delay', $redirect_time), $content, 'boxdata', 'singlebox');
	}

	create_bottom();
	die;
}

/**
 * Initialize Session Variables - Authenticate user
 *
 * @return nothing
 */
function InitSession() {
	$SQL = "";
	$original_UID = 0;

	// if this is an existing SESSION
	if (isset($_SESSION['UID'])) {
		// EXISTING LOGIN, refresh credentials
		$SQL = "SELECT * FROM employees WHERE employee_ID=".$_SESSION['UID'];
		$original_UID = $_SESSION['UID'];
	}

	// if this is in an INTRANET
	if (isset($_SERVER["PHP_AUTH_USER"])) {
		// Authenticate the domain user
		$SQL = "SELECT * FROM employees WHERE MedCtrLogin=\"".safe_data(substr($_SERVER["PHP_AUTH_USER"],7,20))."\"";
	}

	// if this is from a LOGIN
	if (isset($_POST['username'])) {
		// Authenticate the login
		$SQL = "SELECT * FROM employees WHERE MedCtrLogin=\"".safe_data($_POST['username'])."\"";
	}

	if ($SQL=="") {
		// GO to Login Screen if you get here
		header('Location: '.BASE_URL.'login.php');
		die();
	}

	// no ip (possible?)
	if (!isset($_SERVER['REMOTE_ADDR'])) {
		header('Location: '.BASE_URL.'login.php');
		//secure_error('Unable to determine ip address');
		die();
	}

	// Query database to get user ID
	if (!$q = @db_query($SQL, 0)) {
		session_destroy();
		header('Location: '.BASE_URL.'login.php');
		//secure_error('Access denied; Unable to Retrieve User Information (1)', 1);
		die();
	}

	// no such user-password combination
	if (@db_numrows($q) < 1) {
		session_destroy();
		header('Location: '.BASE_URL.'login.php');
		//secure_error('Access denied; User Not Setup', 1);
		die();
	}

	// Attempt to get the data
	if (!($row = db_fetch_array($q, 0))) {
		session_destroy();
		header('Location: '.BASE_URL.'login.php');
		//secure_error('Access denied; Unable to Read User Information (2)', 1);
		die();
	}

	// Do not allow if user is inactive
	if ($row['active']<1) {
		session_destroy();
		header('Location: '.BASE_URL.'login.php');
		//secure_error('Access denied; Unable to Read User Information (2)', 1);
		die();
	}

	// SET VARIABLES
	$_SESSION['UID'] = $row['employee_ID'];
	$_SESSION['LEVEL_ID'] = $row['Level_ID'];
	$_SESSION['MGMT'] = $row['mgmt'];
	$_SESSION['UID_NAME'] = $row['FirstName'].' '.$row['LastName'];
	$_SESSION['ADMIN'] = $row['pm_SiteAdmin'];

	// clear results
	db_free_result($q);

	// User already logged in - update last access
	if ($_SESSION['UID'] == $original_UID) {
		db_query('UPDATE logins SET lastaccess=now() WHERE session_key=\''.$_SESSION['SESSION_KEY'].'\' AND user_id='.$_SESSION['UID']);
	} else {
		// create session key
		// use Mersenne Twister algorithm (random number) + user's IP address, then one-way hash to give session key
		$_SESSION['SESSION_KEY'] = md5(mt_rand().$_SERVER['REMOTE_ADDR']);

		// remove the old login information
		@db_query('DELETE FROM logins WHERE user_id="'.$_SESSION["UID"].'"');

		// log the user in
		@db_query('INSERT INTO logins(user_id, session_key, ip, lastaccess) VALUES (\''.$_SESSION["UID"].'\', \''.$_SESSION['SESSION_KEY'].'\', \''.$_SERVER['REMOTE_ADDR'].'\', now())');
	}
}

//header('Location: '.BASE_URL.'index.php');

?>