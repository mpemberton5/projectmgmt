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

//set variables
$content = '';

$content .= "<link rel=\"stylesheet\" href=\"".BASE_SUB."css/admin.css\" type=\"text/css\" />\n";

$content .= "<br />\n";

//$content .= "<div style=\"align:center;\">\n";
$content .= "<table class=\"cpanel\">\n";
$content .= "	<tr>\n";
$content .= "		<td>\n";
//$content .= "				<a href=\"javascript:void(0);\" onclick='fb.start({ href:\"admin.php?action=users\", rev:\"width:765 height:515 infoPos:tc disableScroll:true caption:`Add Attachment` doAnimations:false\" });'>\n";
$content .= "			<a href=\"admin.php?action=users\">";
$content .= "				<img src=\"".BASE_SUB."images/admin/support.png\" height=\"48\" width=\"48\" alt=\"\" />";
$content .= "				<br />";
$content .= "				Users";
$content .= "			</a>\n";
$content .= "		</td>\n";

$content .= "		<td>\n";
$content .= "			<a href=\"admin.php?action=depts\">";
$content .= "				<img src=\"".BASE_SUB."images/admin/user.png\" height=\"48\" width=\"48\" alt=\"\" />";
$content .= "				<br />";
$content .= "				Departments";
$content .= "			</a>\n";
$content .= "		</td>\n";

$content .= "		<td>\n";
$content .= "			<a href=\"admin.php?action=user_level\">";
$content .= "				<img src=\"".BASE_SUB."images/admin/addedit.png\" height=\"48\" width=\"48\" alt=\"\" />";
$content .= "				<br />";
$content .= "				User Levels";
$content .= "			</a>\n";
$content .= "		</td>\n";

//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=bus_cat\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/frontpage.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Business Categories";
//$content .= "			</a>";
//$content .= "		</td>";
$content .= "	</tr>\n";

//$content .= "	<tr>";
//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=close_stat\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/sections.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Project Statuses";
//$content .= "			</a>";
//$content .= "		</td>";
//
//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=task_stat\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/categories.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Task Statuses";
//$content .= "			</a>";
//$content .= "		</td>";
//
//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=task_pri\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/mediamanager.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Task Priorities";
//$content .= "			</a>";
//$content .= "		</td>";
//
//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=doc_types\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/trash.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Document Types";
//$content .= "			</a>";
//$content .= "		</td>";
//$content .= "	</tr>";
//
//$content .= "	<tr>";
//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=structures\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/trash.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Data Structures";
//$content .= "			</a>";
//$content .= "		</td>";
//
//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=site_announce\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/menu.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Site Announcements";
//$content .= "			</a>";
//$content .= "		</td>";
//
//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=modules\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/user.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Modules";
//$content .= "			</a>";
//$content .= "		</td>";
//
//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=global_config\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/config.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Global Configuration";
//$content .= "			</a>";
//$content .= "		</td>";
//$content .= "	</tr>";
//
//$content .= "	<tr>";
//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=realty_news\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/trash.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Realty News";
//$content .= "				</a>";
//$content .= "		</td>";
//
//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=bill_stat\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/menu.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Billing Statuses";
//$content .= "			</a>";
//$content .= "		</td>";
//
//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=option2\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/user.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Option 2";
//$content .= "			</a>";
//$content .= "		</td>";
//
//$content .= "		<td style=\"height: 100px;\" align=\"center\">";
//$content .= "			<a href=\"admin.php?action=option3\" style=\"text-decoration: none;\">";
//$content .= "				<img src=\"".BASE_SUB."images/admin/config.png\" align=\"middle\" height=\"48\" width=\"48\" alt=\"\" />";
//$content .= "				<br />";
//$content .= "				Option 3";
//$content .= "			</a>";
//$content .= "		</td>";
//$content .= "	</tr>";

$content .= "</table>\n";

$content .= "<HR>\n";

echo $content;

?>