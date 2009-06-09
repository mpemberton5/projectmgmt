<?php
/* $Id: projects_list.php,v 1.20 2009/06/05 18:16:39 markp Exp $ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/time.php');

//
//START OF MAIN PROGRAM
//

//some inital values
$content = '';

$content .= "<link type='text/css' rel='stylesheet' href='/public/slider/css/redmond/jquery-ui-1.7.1.custom.css'>\n";

/*
 $to = "mpembert@wfubmc.edu";
 $subject = "Hi!";
 $body = "Hi,\n\nHow are you?";
 $headers = "From: noreply@wfubmc.edu\r\n" . "X-Mailer: php";
 if (mail($to, $subject, $body, $headers)) {
 echo("<p>Message sent!</p>");
 } else {
 echo("<p>Message delivery failed...</p>");
 }

 send_html_email("mpembert@wfubmc.edu","noreply@wfubmc.edu","This is Great!","Please include the following degrees");
 */

////*********************************************
//// MANAGEMENT
////*********************************************
//if ($_SESSION['MGMT']) {
//	$content .= "<span class=\"textlink\">[<a href=\"index.php\">Return to Dashboard</a>]</span>\n";
//
//	if (isset($_GET['ptype'])) {
//		$_SESSION['ptype'] = $_GET['ptype'];
//	}
//
//	// query to get the projects
//	$SQL  = "SELECT *, ";
//	$SQL .= "(select count(*) from tasks where tasks.project_id=proj.project_id) as TotTasks, ";
//	$SQL .= "(select Dept_Name from departments where departments.department_id=emp.department_id) as Dept ";
//	$SQL .= "FROM projects proj, employees emp ";
//	$SQL .= "WHERE emp.employee_id=proj.owner_id ";
//	$SQL .= "AND Status='".$_SESSION['ptype']."' ";
//	$SQL .= "AND managed=1 ";
//	$SQL .= "ORDER BY EndDate";
//
//	$q = db_query($SQL);
//
//	//check if there are project
//	if (db_numrows($q) > 0) {
//
//		$content .= "<h1>".$_SESSION['ptype']." Projects</h1>";
//
//		//setup content table
//		$content .= "<div class=\"mydiv\">";
//		$content .= "  <table id=\"theTable\" cellpadding=\"0\" cellspacing=\"0\" class=\"sortable-onload-3 no-arrow rowstyle-alt colstyle-alt paginate-15 max-pages-10 paginationcallback-callbackTest-calculateTotalRating paginationcallback-callbackTest-displayTextInfo\">\n";
//		$content .= "    <thead>";
//		$content .= "      <tr>";
//		$content .= "        <th class=\"sortable-text\">Project Name</th>";
//		$content .= "        <th class=\"sortable-text\">Lead Contact</th>";
//		$content .= "        <th class=\"sortable-text\">Department</th>";
//		$content .= "        <th class=\"sortable-date\">Date Started</th>";
//		$content .= "        <th class=\"sortable-numeric\">Tasks</th>";
//		$content .= "        <th class=\"sortable-numeric\">Alloc. Hours</th>";
//		$content .= "      </tr>";
//		$content .= "    </thead>";
//		$content .= "    <tbody>";
//
//		//show all projects
//		for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
//
//			if (($i % 2) == 1) {
//				$content .= "      <tr>";
//			} else {
//				$content .= "      <tr class=\"alt\">";
//			}
//
//			//show name and a link
//			$content .= "        <td>";
//			$content .= "<a href=\"projects.php?action=show&amp;project_id=".$row['project_ID'];
//			$content .= "\"><b>".$row['Project_Name']."</b></a>\n";
//			$content .= "</td>";
//
//			$content .= "<td>".$row['FirstName']." ".$row['LastName']."</td>";
//			$content .= "<td>".$row['Dept']."</td>";
//			if (empty($row['StartDate']) or $row['StartDate']==='0000-00-00') {
//				$startdate = "";
//			} else {
//				$startdate = date('m-d-Y',strtotime($row['StartDate']));
//			}
//			$content .= "<td>".$startdate."</td>";
//
//			$content .= "        <td><div align=\"center\">".$row['TotTasks']."</div></td>";
//
//			$content .= "<td><div align=\"center\">".$row['BudgetHours']."</div></td>";
//
//			$content .= "      </tr>";
//
//		}
//		db_free_result($q);
//
//		$content .= "    </tbody>";
//		$content .= "  </table>";
//		$content .= "</div>";
//	}
//
//
//	//*********************************************
//	// USERS
//	//*********************************************
//} else {

$content .= "<script type=\"text/javascript\" src=\"".BASE."js/jquery.tablesorter.min.js\"></script>\n";
$content .= "<script type=\"text/javascript\" src=\"".BASE."js/jquery.metadata.min.js\"></script>\n";
$content .= "<script type=\"text/javascript\" src=\"/public/jquery-treeview/lib/jquery.cookie.js\"></script>\n";

$content .= "<script language=\"javascript\">\n";
$content .= "	function goLite(node) {\n";
$content .= "	   document.getElementById(node).style.backgroundColor = \"#99DDFF\";\n";
$content .= "	}\n";
$content .= "	function goDim(node) {\n";
$content .= "	   document.getElementById(node).style.backgroundColor = \"\";\n";
$content .= "	}\n";
$content .= "	$(document).ready(function() {\n";
$content .= "		$(\"#tabs\").tabs({\n";
$content .= "			load: function(event, ui) {\n";
//	$content .= "alert('test');";
$content .= "			}\n";
$content .= "		});\n";
$content .= "		$(\"#mainTable\").tablesorter( {sortList: [[7,1],[4,1]]} );\n";
$content .= "		$(\"#mainTable2\").tablesorter( {sortList: [[4,0]]} );\n";
$content .= "		$(\"#mainTable3\").tablesorter( {sortList: [[4,0]]} );\n";
$content .= "	});\n";
$content .= "	$.cookie('ui-dynatree-cookie-select', '');\n";
$content .= "	$.cookie('ui-dynatree-cookie-active', '');\n";
$content .= "	$.cookie('ui-dynatree-cookie-expand', '');\n";
$content .= "</script>\n";

$content .= "<style type=\"text/css\">\n";
$content .= "/* Caching CSS created with the help of;\n";
$content .= "	Klaus Hartl <klaus.hartl@stilbuero.de> */\n";
$content .= "@media projection, screen {\n";
$content .= "     div.imgCache { position: absolute; left: -9999px; top: -9999px; }\n";
$content .= "     div.imgCache img { display:block; }\n";
$content .= "}\n";
$content .= "@media print { div.imgCache, div.imgCache img { visibility: hidden; display: none; } }\n";
$content .= "</style>\n";

$content .= "<div class=\"imgCache\">\n";
$content .= "	<img src=\"/public/floatbox/graphics/close_white.gif\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/cornerBottom_white_r12_b1.png\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/cornerLeft_white_r12_b1.png\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/cornerRight_white_r12_b1.png\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/cornerTop_white_r12_b1.png\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/dragger_white.gif\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/loader_black.gif\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/loader_white.gif\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/next_white.gif\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/pause_white.gif\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/play_white.gif\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/prev_white.gif\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/resizer_white.gif\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/shadowBottom_s12_r12.png\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/shadowLeft_drop_s12_r12.png\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/shadowRight_drop_s12_r12.png\" />\n";
$content .= "	<img src=\"/public/floatbox/graphics/shadowTop_s12_r12.png\" />\n";
$content .= "	<img src=\"/public/jquery/development-bundle/demos/datepicker/images/calendar.gif\" />\n";
$content .= "</div>\n";


// TO DO LIST
$SQL = "";
// query to get the projects
//		$SQL  = "SELECT t.*, p.managed ";
//		$SQL .= "FROM tasks t, projects p ";
//		$SQL .= "WHERE p.project_ID=t.Project_ID AND t.Assigned_To_ID=".$_SESSION['UID']." AND t.parent_task_ID<>0 AND t.PercentComplete<>100 ";
//		$SQL .= "ORDER BY p.managed desc,t.Priority desc";

//	$SQL = "select t.*, p.managed from tasks t, projects p where p.project_ID=t.Project_ID and t.task_ID in ( ";
//	$SQL .= "select t1 from ( ";
//	$SQL .= "select Project_ID, (select task_ID from tasks where tasks.Project_ID=projects.project_ID and Assigned_To_ID=".$_SESSION['UID']." and parent_task_ID=0 and PercentComplete<>100 order by Project_ID,order_num limit 1) as t1 from projects where Status<>\"Archived\") as zz ";
//	$SQL .= "where t1 is not null); ";

$uid = $_SESSION['UID'];
if ($_SESSION['MGMT']==1) {
	if (isset($_REQUEST['uid'])) {
		$uid = $_REQUEST['uid'];
	}
}

$SQL .= "select tsk.*, proj.managed from tasks tsk, projects proj where proj.project_ID=tsk.Project_ID and tsk.Assigned_To_ID=".$uid." and tsk.task_ID in (";
$SQL .= "	select * from (";
$SQL .= "		select ";
$SQL .= "			(select Curr_Task_ID from tasks ";
$SQL .= "			 where tasks.Project_ID=projects.project_ID ";
$SQL .= "			 and parent_task_ID=0 ";
$SQL .= "			 and PercentComplete<>100 ";
$SQL .= "			 order by Project_ID,order_num limit 1) as tsk ";
$SQL .= "		from projects where status='Active') as dta";
$SQL .= "	where tsk is not null)";

$q = db_query($SQL);

$content .= "				<div class=\"demo\" id=\"demo\">\n";
$content .= "					<div id=\"tabs\" class=\"ui-tabs\">\n";
$content .= "						<ul class=\"ui-tabs-nav\">\n";
$content .= "							<li><a href=\"#tabs-1\"><div style=\"font-size: 15px;\">To Do List</div></a></li>\n";
$content .= "							<li><a href=\"#tabs-2\"><div style=\"font-size: 15px;\">Open Projects</div></a></li>\n";
$content .= "							<li><a href=\"#tabs-3\"><div style=\"font-size: 15px;\">My Projects</div></a></li>\n";
$content .= "							<li><a href=\"#tabs-4\"><div style=\"font-size: 15px;\">Archives</div></a></li>\n";
$content .= "						</ul>\n";
$content .= "						<div id=\"tabs-1\" class=\"ui-tabs-container ui-tabs-hide\">\n";

//check if there are project
if (db_numrows($q) > 0) {

	//setup content table
	$content .= "								<table id=\"mainTable\" cellpadding=\"0\" cellspacing=\"0\" class=\"tablesorter\">\n";
	$content .= "									<thead>\n";
	$content .= "										<tr>\n";
	$content .= "											<th>Task Name</th>\n";
	$content .= "											<th class=\"{sorter: 'shortDate'}\">Last Updated</th>\n";
	$content .= "											<th>% Complete</th>\n";
	$content .= "											<th>Status</th>\n";
	$content .= "											<th>Priority</th>\n";
	$content .= "											<th class=\"{sorter: 'shortDate'}\">Start Date</th>\n";
	$content .= "											<th class=\"{sorter: 'shortDate'}\">End Date</th>\n";
	$content .= "											<th>Managed</th>\n";
	$content .= "											<th style=\"width:50px; text-align:center\" class=\"{sorter: false}\">Note</th>\n";
	$content .= "										</tr>\n";
	$content .= "									</thead>\n";
	$content .= "									<tbody>\n";

	//show all projects
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {

		$content .= "      <tr>";

		//http://localhost/pm/projects.php?action=show&project_id=7#tasks.php?action=showSub&project_id=7&task_id=25
		//show name and a link
		$content .= "<td>";
		$content .= "<a href=\"projects.php?action=show&project_id=".$row['Project_ID']."#tasks.php?action=showSub&amp;project_id=".$row['Project_ID']."&amp;task_id=".$row['task_ID'];
		$content .= "\"><b>".$row['task_name']."</b></a>\n";
		$content .= "</td>";

		$content .= "<td>".date('m-d-Y',strtotime($row['LastUpdated']))."</td>";
		$content .= "<td>".$row['PercentComplete']."</td>";
		$content .= "<td>".$row['Status']."</td>";
		$content .= "<td>".$row['Priority']."</td>";
		if (empty($row['Start_Date']) or $row['Start_Date']==='0000-00-00') {
			$startdate = "";
		} else {
			$startdate = date('m-d-Y',strtotime($row['Start_Date']));
		}
		$content .= "<td>".$startdate."</td>";
		if (empty($row['End_Date']) or $row['End_Date']==='0000-00-00') {
			$enddate = "";
		} else {
			$enddate = date('m-d-Y',strtotime($row['End_Date']));
		}
		$content .= "<td>".$enddate."</td>";
		$content .= "<td>".$row['managed']."</td>";

		$content .= "<td style=\"text-align:center\">";

		$content .= "<a href=\"javascript:void(0);\" onclick='fb.start({ href: \"task_notes.php?action=popupAdd&project_id=".$row['Project_ID']."&task_id=".$row['task_ID']."\", rev:\"width:650 height:530 infoPos:tc showClose:false disableScroll:true caption:`NEW Task Note` doAnimations:false\" });'\">New</a>\n";

		$content .= "</td>";

		$content .= "      </tr>";

	}
	db_free_result($q);

	$content .= "    </tbody>";
	$content .= "  </table>";
	//		$content .= "</div>";
}
$content .= "</div>\n";
$content .= "<div id=\"tabs-2\" class=\"ui-tabs-container ui-tabs-hide\">\n";

// PROJECT LIST THAT USER IS ASSOCIATED WITH
// query to get the projects
$SQL  = "SELECT *, ";
$SQL .= "(select Dept_Name from departments where departments.department_id=emp.department_id) as Dept ";
$SQL .= "FROM projects proj, employees emp ";
$SQL .= "WHERE emp.employee_id=proj.owner_id ";
$SQL .= "AND (select count(*) from tasks where tasks.project_id=proj.project_id AND Assigned_To_ID=".$_SESSION['UID'].") > 0 ";
$SQL .= "AND proj.status<>'Complete' ";
$SQL .= "ORDER BY EndDate";

$q = db_query($SQL);

//check if there are project
if (db_numrows($q) > 0) {

	//setup content table
	$content .= "<table id=\"mainTable2\" cellpadding=\"0\" cellspacing=\"0\" class=\"tablesorter\">\n";
	$content .= "    <thead>";
	$content .= "      <tr>";
	$content .= "        <th>Project Name</th>";
	$content .= "        <th>Lead Contact</th>";
	$content .= "        <th>Department</th>";
	$content .= "        <th>Date Started</th>";
	$content .= "        <th>Alloc. Hours</th>";
	$content .= "      </tr>";
	$content .= "    </thead>";
	$content .= "    <tbody>";

	//show all projects
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
		$content .= "      <tr>";
		//show name and a link
		$content .= "<td>";
		$content .= "<a href=\"projects.php?action=show&amp;project_id=".$row['project_ID'];
		$content .= "\"><b>".$row['Project_Name']."</b></a>\n";
		$content .= "</td>";

		$content .= "<td>".$row['FirstName']." ".$row['LastName']."</td>";
		$content .= "<td>".$row['Dept']."</td>";
		if (empty($row['StartDate']) or $row['StartDate']==='0000-00-00') {
			$startdate = "";
		} else {
			$startdate = date('m-d-Y',strtotime($row['StartDate']));
		}
		$content .= "<td>".$startdate."</td>";

		$content .= "<td><div align=\"center\">".$row['BudgetHours']."</div></td>";

		$content .= "      </tr>";

	}
	db_free_result($q);

	$content .= "    </tbody>";
	$content .= "  </table>";
}
$content .= "</div>\n";
$content .= "<div id=\"tabs-3\" class=\"ui-tabs-container ui-tabs-hide\">\n";

// PROJECT LIST THAT USER IS ASSOCIATED WITH
// query to get the projects
$SQL  = "SELECT *, ";
$SQL .= "(select count(*) from tasks where tasks.project_id=proj.project_id) as TotTasks, ";
$SQL .= "(select Dept_Name from departments where departments.department_id=emp.department_id) as Dept ";
$SQL .= "FROM projects proj, employees emp ";
$SQL .= "WHERE emp.employee_id=proj.owner_id ";
$SQL .= "AND proj.owner_id='".$_SESSION['UID']."' ";
$SQL .= "ORDER BY EndDate";

$q = db_query($SQL);

//check if there are project
if (db_numrows($q) > 0) {

	//setup content table
	//		$content .= "<div class=\"mydiv\">";
	$content .= "<table id=\"mainTable3\" cellpadding=\"0\" cellspacing=\"0\" class=\"tablesorter\">\n";
	$content .= "    <thead>";
	$content .= "      <tr>";
	$content .= "        <th>Project Name</th>";
	$content .= "        <th>Lead Contact</th>";
	$content .= "        <th>Department</th>";
	$content .= "        <th>Date Started</th>";
	$content .= "        <th>Tasks</th>";
	$content .= "        <th>Alloc. Hours</th>";
	$content .= "      </tr>";
	$content .= "    </thead>";
	$content .= "    <tbody>";

	//show all projects
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
		$content .= "      <tr>";

		//show name and a link
		$content .= "<td>";
		$content .= "<a href=\"projects.php?action=show&amp;project_id=".$row['project_ID'];
		$content .= "\"><b>".$row['Project_Name']."</b></a>\n";
		$content .= "</td>";

		$content .= "<td>".$row['FirstName']." ".$row['LastName']."</td>";
		$content .= "<td>".$row['Dept']."</td>";
		if (empty($row['StartDate']) or $row['StartDate']==='0000-00-00') {
			$startdate = "";
		} else {
			$startdate = date('m-d-Y',strtotime($row['StartDate']));
		}
		$content .= "<td>".$startdate."</td>";

		//$inc_tasks = db_result(db_query('SELECT count(*) FROM tasks WHERE project_id='.$row['id'].' AND creator='.$_SESSION['UID'].' AND status <> "deleted" AND status <> "done"'),0,0);
		$content .= "<td><div align=\"center\">".$row['TotTasks']."</div></td>";

		$content .= "<td><div align=\"center\">".$row['BudgetHours']."</div></td>";

		$content .= "      </tr>";

	}
	db_free_result($q);

	$content .= "    </tbody>";
	$content .= "  </table>";
	//		$content .= "</div>";
}
$content .= "</div>\n";
$content .= "<div id=\"tabs-4\" class=\"ui-tabs-container ui-tabs-hide\">\n";
$content .= "	<div>Search Closed Projects</div>";
$content .= "</div>\n";
$content .= "</div>\n";
//}

echo $content;

?>