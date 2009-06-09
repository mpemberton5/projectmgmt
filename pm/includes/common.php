<?php
/* $Id: common.php,v 1.6 2009/05/29 16:49:51 markp Exp $ */

/**
 * @param $recipient
 * @param $from
 * @param $subject
 * @param $message
 * @return TRUE if successful, FALSE if unsuccessful
 */
function send_html_email($recipient, $from, $subject, $message) {
	$body = "<html>\n";
	$body .= "<body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:12px; color:#666666;\">\n";
	$body .= $message;
	$body .= "</body>\n";
	$body .= "</html>\n";

	$headers = "";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "From: ".$from."\r\n";
	$headers .= "X-Mailer: PHP\r\n";

	// In case any of our lines are larger than 70 characters, we should use wordwrap()
	$body = wordwrap($body, 70);

	return mail($recipient, $subject, $body, $headers);
}

/******************************************************************************/
/* Format integer to price */
/******************************************************************************/
/**
 * @param $amount
 * @return unknown_type
 */
function price($amount) {
	return ($amount) ? '$'.number_format($amount, 2, '.', ',') : '-';
}

/******************************************************************************/
// Input validation (single line input)
/******************************************************************************/
/**
 * @param $body
 * @return unknown_type
 */
function safe_data($body) {

	//remove excess whitespace
	$body = trim($body);

	//return null for nothing input
	if (strlen($body) == 0) {
		return '';
	}

	//validate characters
	$body = validate($body);

	//limit line length for single line entries
	if (strlen($body) > 100) {
		$body = substr($body, 0, 100);
	}

	//remove line breaks (not allowed in single lines!)
	$body = strtr($body, array("\r"=>' ', "\n"=>' '));

	$body = clean_up($body);

	return $body;
}

/******************************************************************************/
// Input validation (multiple line input)
/******************************************************************************/
/**
 * @param $body
 * @return unknown_type
 */
function safe_data_long($body) {

	//remove excess whitespace
	$body = trim($body);

	//return null for nothing input
	if (strlen($body) == 0) {
		return '';
	}

	//validate characters
	$body = validate($body);

	//normalise line breaks from Windows & Mac to UNIX style '\n'
	//$body = str_replace("\r\n", "\n", $body);
	$body = str_replace("\r", "\n", $body);
	//break up long non-wrap words
	$body = preg_replace("/[^\s\n\t]{100}/", "$0\n", $body);

	$body = clean_up($body);

	return $body;
}

/******************************************************************************/
// validate text input
/******************************************************************************/
/**
 * @param $body
 * @return unknown_type
 */
function validate($body) {

	global $validation_regex;

	//we don't use magic_quotes
	if (get_magic_quotes_gpc()) {
		$body = stripslashes($body);
	}

	//allow only normal printing characters valid for the character set in use
	if (isset($validation_regex)) {
		//character set regex in language file
		$body = preg_replace($validation_regex, '?', $body);
	} else {
		//no character set defined --> ASCII only
		$body = preg_replace('/[^\x09\x0a\x0d\x20-\x7e]/', '?', $body);
	}

	return $body;
}

/******************************************************************************/
//clean up body text for security reasons
/******************************************************************************/
/**
 * @param $body
 * @return unknown_type
 */
function clean_up($body) {

	//prevent SQL injection
	$body = db_escape_string($body);

	//change '&' to '&amp;' except when part of an entity, or already changed
	$body = preg_replace('/&(?!(#[\d]{2,5}|amp);)/', '&amp;', $body);
	//use HTML encoding, or add escapes '\' for characters that could be used for xss <script> or SQL injection attacks
	$trans = array(';'=>'\;', '<'=>'&lt;', '>'=>'&gt;', '+'=>'\+', '-'=>'\-', '='=>'\=', '%'=>'&#037;');
	$body  = strtr($body, $trans);

	return $body;
}

/******************************************************************************/
//check for true positive integer values to max size limits of PHP
//NOTE: does not return value of integer, simply returns bool of test
/******************************************************************************/
/**
 * @param $integer
 * @return unknown_type
 */
function safe_integer($integer) {

	if (is_numeric($integer) && ((string)$integer === (string)intval(abs($integer)))) {
		return true;
	}
	return false;
}

/******************************************************************************/
// single and double quotes in HTML edit fields are changed to HTML encoding
// (addslashes doesn't work for HTML)
/******************************************************************************/
/**
 * @param $body
 * @return unknown_type
 */
function html_escape($body) {

	$trans = array('"'=>'&quot;', "'"=>'&apos;');

	return strtr($body, $trans);
}

/******************************************************************************/
// single quotes in javascript fields are escaped
// double quotes are changed to HTML (escaping won't work)
/******************************************************************************/
/**
 * @param $body
 * @return unknown_type
 */
function javascript_escape($body) {

	$trans = array('"'=>'&quot;', "'"=>"\\'");

	return strtr($body, $trans);
}

/******************************************************************************/
// make web addresses and email addresses clickable
/******************************************************************************/
/**
 * @param $body
 * @param $database_escape
 * @return unknown_type
 */
function html_links($body, $database_escape=0) {

	if (strlen($body) == 0) {
		return '';
	}
	$body = preg_replace('/\b[a-z0-9\.\_\-]+@[a-z0-9][a-z0-9\.\-]+\.[a-z\.]+\b/i', "<a href=\"mailto:$0\">$0</a>", $body);

	//data being submitted to a database needs ('$0') part escaped
	$escape = ($database_escape) ? '\\' : '';

	$body = preg_replace('/((http|ftp)+(s)?:\/\/[^\s]+)/i', "<a href=\"$0\" onclick=\"window.open(".$escape."'$0".$escape."'); return false\">$0</a>", $body);
	return $body;
}

/******************************************************************************/
// Builds up an error screen
/******************************************************************************/
/**
 * @param $box_title
 * @param $error
 * @return unknown_type
 */
function error($box_title, $error) {

	try {
		throw new Exception ($error);
	}
	catch (Exception $e) {
		header('HTTP/1.0 500 Internal Server Error', true, '500');
		echo $e->getMessage();
		die();
	}

	global $db_error_message;

	include_once(BASE.'includes/screen.php');

	create_complete_top('ERROR', 1);

	if (NO_ERROR !== 'Y') {
		$content = "<div style=\"text-align : center\">".$error."</div>";
		new_box($box_title, $content, 'boxdata', 'singlebox');
	} else {
		new_box('Report', '<H1>Sorry!</H1><P>We are unable to process your request right now.  Please try again later.', 'boxdata2', 'singlebox');
	}

	if ((EMAIL_ERROR != NULL) || (DEBUG === 'Y')) {

		$uid_name = defined('UID_NAME') ? UID_NAME : '';
		$uid_email = defined('UID_EMAIL') ? UID_EMAIL : '';

		//get the post vars
		ob_start();
		print_r($_REQUEST);
		$post = ob_get_contents();
		ob_end_clean();

		//email to the error address
		$message = "Hello,\n This is the ".MANAGER_NAME." site and I have an error :/  \n".
            "\n\n".
            "Error message: ".$error."\n".
            "Database message: ".$db_error_message."\n".
            "User: ".$uid_name." (".$uid_email.")\n".
            "Component: ".$box_title."\n".
            "Page that was called: ".$_SERVER['SCRIPT_NAME']."\n".
            "Requested URL: ".$_SERVER['REQUEST_URI']."\n".
            "URL string: ".$_SERVER['QUERY_STRING']."\n".
            "Browser: ".$_SERVER['HTTP_USER_AGENT']."\n".
            "Time: ".date("F j, Y, H:i")."\n".
            "IP: ".$_SERVER['REMOTE_ADDR']."\n".
            "CAPP version:".CAPP_VERSION."\n".
            "POST variables: $post\n\n";

		if (EMAIL_ERROR != NULL) {
			include_once(BASE.'includes/email.php');
			email(EMAIL_ERROR, "ERROR on ".MANAGER_NAME, $message);
		}

		if (DEBUG === 'Y') {
			$content = nl2br($message);
			new_box("Error Debug", $content);
		}
	}

	create_bottom();

	//do not return
	die;
}

/******************************************************************************/
// Builds up a warning screen
/******************************************************************************/
/**
 * @param $box_title
 * @param $message
 * @return unknown_type
 */
function warning($box_title, $message) {

	include_once(BASE.'includes/screen.php');

	create_complete_top('Error', 1);

	$content = "<div style=\"text-align : center\">".$message."</div>\n";
	new_box($box_title, $content, 'boxdata', 'singlebox');

	create_bottom();

	//do not return
	die;
}

/******************************************************************************/
// Build State Dropdown
/******************************************************************************/
/**
 * @param $var_name
 * @param $curr_val
 * @param $state_default
 * @return unknown_type
 */
function select_state($var_name,$curr_val="",$state_default="NC") {
	$tmp_content = "";

	$q = db_query('SELECT * FROM states ORDER BY abbr');

	//select status
	$tmp_content .= "<select name=\"".$var_name."\" id=\"".$var_name."\">\n";
	for ($i=0; $table_row = @db_fetch_array($q, $i); ++$i) {

		$tmp_content .= "	<option value=\"".$table_row['abbr']."\"";

		if ($curr_val <> "") {
			// set selected for current value
			if ($table_row['abbr'] == $curr_val) {
				$tmp_content .= " selected=\"selected\"";
			}
		} else {
			// set selected for default
			if ($table_row['abbr'] == $state_default) {
				$tmp_content .= " selected=\"selected\"";
			}
		}
		$tmp_content .= ">".$table_row['abbr']."</option>\n";
	}
	$tmp_content .= "</select>";

	return $tmp_content;
}

/* SPECIALIZED FUNCTION THAT CONVERTS HARDCODED ROLE TO RECORD NUMBER IN DATA_TABLE */
/**
 * @param $role
 * @return unknown_type
 */
function convert_to_lvl_id($role) {
	if (strlen($role) == 2) {
		$role = substr($role,1,1);
	}
	switch($role) {
		case "S":
			return 63;
			break;
		case "B":
			return 63;
			break;
		case "A":
			return 60;
			break;
		case "L":
			return 61;
			break;
		case "M":
			return 62;
			break;
		case "V":
			return 59;
			break;
	}
}

/******************************************************/
/**
 * @param $m_id
 * @param $t_id
 * @param $sidefn
 * @param $s_lvl_id
 * @param $perm
 * @return unknown_type
 */
function Check_Perms($m_id,$t_id,$sidefn,$s_lvl_id,$perm) {

	$perms = db_result(db_query('SELECT permissions FROM perms WHERE module_id='.$m_id.' AND rec_id='.$t_id.' AND side="'.$sidefn.'" AND sec_lvl_id='.$s_lvl_id),0,0);

	$view_rights = 0;
	if (stripos($perms,$perm) !== FALSE) {
		$view_rights = 1;
	}
	return $view_rights;
}

/******************************************************/
/**
 * @return unknown_type
 */
function get_file_token() {

	// loop until a unique token is found
	while (1) {
		$token = md5(uniqid(rand(),1));
		$q = db_query('SELECT token FROM files WHERE token="'.$token.'" LIMIT 1');
		if (@db_numrows($q) < 1) {
			return $token;
		}
	}
}
?>