<?php
/* $Id$ */

//includes
require_once('path.php');
require_once(BASE.'includes/security.php');

$message = "";
$message .= "<B>Sent By: </B>\n";
$message .= db_result(db_query('SELECT MedCtrLogin FROM employees WHERE employee_ID='.$_SESSION['UID']),0,0);
$message .= "<br />\n";
$message .= "<br />\n";
$message .= "<B>Current Form: </B>";
$message .= $_REQUEST['currform'];
$message .= "<br />\n";
$message .= "<br />\n";
$message .= $_REQUEST['comments'];

send_html_email(FEEDBACKUSER,"noreply@wfubmc.edu","Project Feedback",$message);

?>