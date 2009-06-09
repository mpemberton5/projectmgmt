<?php
/* $Id: admin_user_list.php,v 1.1 2009/06/08 21:13:03 markp Exp $ */

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

$content .= "<script type='text/javascript' charset='utf-8'>\n";
$content .= "$(function(){\n";
$content .= "	$('select#depts').change(function(){\n";
$content .= "		$.getJSON('/admin.php',{action: 'admin_get_user_list', id: $(this).val(), ajax: 'true'}, function(j){\n";
$content .= "			var options = '';\n";
$content .= "			for (var i = 0; i < j.length; i++) {\n";
$content .= "				options += '<option value=\"' + j[i].optionValue + '\">' + j[i].optionDisplay + '</option>';\n";
$content .= "			}\n";
$content .= "			$('select#users').html(options);\n";
$content .= "		})\n";
$content .= "	})\n";
$content .= "})\n";
$content .= "</script>\n";

$content .= "<BR />\n";
$content .= "<div id=\"doc\">\n";

$q = db_query('select * FROM departments ORDER BY Dept_Name');
$content .= "<div class=\"selHolder\">\n";
$content .= "<H2>Departments</H2>\n";
$content .= "<select size='10' name='depts' id='depts'>\n";
if (db_numrows($q) > 0) {
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
		$content .= "<option>".$row['Dept_Name']."</option>\n";
	}
} else {
	$content .= "<option>2</option>\n";
}
db_free_result($q);
$content .= "</select>\n";
$content .= "</div>\n";


$content .= "<div class=\"selHolder\">\n";
$content .= "<H2>Employees</H2>\n";
$content .= "<select size='10' name='users' id='users'>\n";
			$content .= "<option>1</option>\n";
$content .= "</select>\n";
$content .= "</div>\n";

$content .= "</div>\n";
//	//setup main table
//	$content .= "<span class=\"textlink\">[<a href=\"admin.php?x=".$x."&amp;action=admin\">Return to Administration</a>]</span>";
//	$content .= "<table><tr><td>\n";
//	$content .= "<span class=\"textlink\">[<a href=\"users.php?x=".$x."&amp;action=add\">Add New User</a>]</span>\n";
//	$content .= "</td></tr><tr><td>&nbsp;";
//
//	//setup content table
//	$content .= "<div class=\"mydiv\">";
//	$content .= "  <table class=\"sortable\" id=\"sort_table\">\n";
//	$content .= "    <thead>";
//	$content .= "      <tr>";
//	$content .= "        <th scope=\"col\">Users</th>";
//	$content .= "        <th scope=\"col\">Login</th>";
//	$content .= "        <th scope=\"col\">Join Date</th>";
//	$content .= "        <th scope=\"col\">Security Level</th>";
//	$content .= "        <th scope=\"col\">Billing Status</th>";
//	$content .= "        <th>&nbsp;</th>";
//	$content .= "      </tr>";
//	$content .= "    </thead>";
//	$content .= "    <tbody>";
//
//	//show all rows
//	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
//		if (($i % 2) == 1) {
//			$content .= "      <tr>";
//		} else {
//			$content .= "      <tr class=\"odd\">";
//		}
//
//		//show name and a link
//		$content .= "        <th scope=\"row\">";
//		$content .= "<a href=\"users.php?x=".$x."&amp;user_id=".$row['id']."&amp;action=edit\"><b>".$row['firstname']." ".$row['lastname']."</b></a>\n";
//		$content .= "</th>";
//		$content .= "<td>".$row['login_name']."</td>";
//		$content .= "<td>".nicedate($row['join_date'])."</td>";
//		$content .= "<td>".$sec_lvl."</td>";
//		$content .= "<td>".$bill_stat."</td>";
//		$content .= "<td><a href=\"users.php?x=".$x."&amp;user_id=".$row['id']."&amp;action=del\"><img src=\"images/icon-delete.gif\" alt=\"Remove User\" /></a></td>\n";
//		$content .= "</tr>\n";
//	}
//
//	$content .= "    </tbody>";
//	$content .= "  </table>";
//	$content .= "</div>";
//
//	$content .= "</table>\n";
//
//	$content .= "</td></tr></table>";
//} //end if rows

//show it
echo $content;

?>