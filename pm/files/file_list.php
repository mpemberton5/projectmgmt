<?php
/* $Id: file_list.php,v 1.3 2009/06/08 05:04:44 markp Exp $ */

//security check
if (!isset($_SESSION['UID'])) {
  die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

$content = '';

if (!@safe_integer($_REQUEST['project_id'])) {
	error('File upload', 'Not a valid project_id');
}
$project_id = $_REQUEST['project_id'];

if (!@safe_integer($_REQUEST['task_id'])) {
	$task_id = 0;
} else {
	$task_id = $_REQUEST['task_id'];
}

$SQL = "SELECT f.*, emp.FirstName, emp.LastName from files f, employees emp where f.project_id=".$project_id." and emp.employee_ID=f.uploaded_by";
$q = db_query($SQL);

//TODO: add scrollable table
//http://www.webtoolkit.info/demo/javascript/scrollable/demo.html

//check if there are project
if (db_numrows($q) > 0) {
	$content .= "<table style=\"width:100%\">\n";
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
		$content .= "	<tr>\n";
		$content .= "		<td><a href=\"files.php?action=download&file_id=".$row['file_id']."\">".$row['filename']."</a></td>\n";
		$content .= "		<td>".$row['size']."</td>\n";
		$content .= "		<td>".$row['uploaded_date']."</td>\n";
		$content .= "		<td>".$row['FirstName']." ".$row['LastName']."</td>\n";
		$content .= "	</tr>\n";
	}
	$content .= "</table>\n";
}
db_free_result($q);

echo $content;

?>