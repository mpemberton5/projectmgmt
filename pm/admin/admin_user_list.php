<?php
/* $Id$ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

//only for admins
if(!$_SESSION['ADMIN']) {
	error('Not permitted', 'This function is for admins only');
}

$content = '';

$content .= "<a href=\"javascript:void(0);\" onclick='fb.start({ href:\"admin.php?action=userPopupAdd\", rev:\"width:765 height:515 infoPos:tc disableScroll:true caption:`Add Employee` doAnimations:false\" });'>Add Employee</a>\n";
$content .= "<p />\n";

//get employees
$q = db_query('SELECT * FROM employees emp, departments dept WHERE dept.department_ID=emp.Department_ID ORDER BY emp.Department_ID,emp.LastName,emp.FirstName');

if (@db_numrows($q)>0) {
	$content .= "<div style=\"width:400px; margin-left: auto; margin-right: auto;\">\n";
	$content .= "	<table class=\"tablesorter\">\n";
	$content .= "		<tbody>\n";
	$content .= "			<th>Department</th>\n";
	$content .= "			<th>Employee Name</th>\n";
	$content .= "		</tbody>\n";
	for ($i=0 ; $row = @db_fetch_array($q, $i) ; ++$i) {
		$content .= "		<tr>\n";
		$content .= "			<td width=\"100\">".$row['Dept_Name']."</td>\n";
		$content .= "			<td>\n";
		$content .= "				<a href=\"javascript:void(0);\" onclick='fb.start({ href:\"admin.php?action=userPopupEdit&amp;employee_id=".$row['employee_ID']."\", rev:\"width:765 height:515 infoPos:tc disableScroll:true caption:`Edit Employee` doAnimations:false\" });'>\n";
		$content .= "					".$row['LastName'].", ".$row['FirstName']."\n";
		$content .= "				</a>\n";
		$content .= "			</td>\n";
		$content .= "		</tr>\n";
	}
	$content .= "	</table>\n";
	$content .= "</div>\n";
}

db_free_result($q);

//show it
echo $content;

?>