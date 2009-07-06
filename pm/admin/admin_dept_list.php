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

$content .= "<a href=\"javascript:void(0);\" onclick='fb.start({ href:\"admin.php?action=deptPopupAdd\", rev:\"width:765 height:515 infoPos:tc disableScroll:true caption:`Add Department` doAnimations:false\" });'>Add Department</a>\n";
$content .= "<p />\n";

//get employees
$q = db_query('SELECT * FROM departments ORDER BY Dept_Name');

if (@db_numrows($q)>0) {
	$content .= "<div style=\"width:400px; margin-left: auto; margin-right: auto;\">\n";
	$content .= "	<table class=\"tablesorter\">\n";
	$content .= "		<tbody>\n";
	$content .= "			<th>Department Name</th>\n";
	$content .= "		</tbody>\n";
	for ($i=0 ; $row = @db_fetch_array($q, $i) ; ++$i) {
		$content .= "		<tr>\n";
		$content .= "			<td>\n";
		$content .= "				<a href=\"javascript:void(0);\" onclick='fb.start({ href:\"admin.php?action=deptPopupEdit&amp;department_id=".$row['department_ID']."\", rev:\"width:765 height:515 infoPos:tc disableScroll:true caption:`Edit Department` doAnimations:false\" });'>\n";
		$content .= "					".$row['Dept_Name']."\n";
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