<?php
/* $Id$ */

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

$content .= "<link type='text/css' rel='stylesheet' href='/public/slider/css/redmond/jquery-ui-1.7.1.custom.css' />\n";

$content .= "<script type=\"text/javascript\" src=\"".BASE."js/jquery.tablesorter.min.js\"></script>\n";
$content .= "<script type=\"text/javascript\" src=\"".BASE."js/jquery.metadata.min.js\"></script>\n";
$content .= "<script type=\"text/javascript\" src=\"/public/jquery-treeview/lib/jquery.cookie.js\"></script>\n";

$content .= "<script>\n";
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
$content .= "		$(\"#mainTable2\").tablesorter( {sortList: [[3,0]]} );\n";
$content .= "		$(\"#mainTable3\").tablesorter( {sortList: [[3,0]]} );\n";
$content .= "		$(\"#mainTable4\").tablesorter( {sortList: [[3,0]]} );\n";
$content .= "	});\n";
$content .= "	$.cookie('ui-dynatree-cookie-select', '');\n";
$content .= "	$.cookie('ui-dynatree-cookie-active', '');\n";
$content .= "	$.cookie('ui-dynatree-cookie-expand', '');\n";

//$content .= "	$('ul.ui-tabs-nav a:eq(0)').toggleClass('ui-tabs-loading');\n";
//$content .= "	$.get(\"projects.php?action=getMonitoredProjects\", function(data) {\n";
//$content .= "		$(\"#MonitoredProjects\").html( data );\n";
//$content .= "	});\n";
$content .= "</script>\n";

$content .= "<style>\n";
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
$content .= "							<li><a href=\"#tabs-1\"><div style=\"font-size: 12px;\">To Do List</div></a></li>\n";
$content .= "							<li><a href=\"#tabs-2\"><div style=\"font-size: 12px;\">Contributor</div></a></li>\n";
$content .= "							<li><a href=\"#tabs-3\"><div style=\"font-size: 12px;\">Owner</div></a></li>\n";
$content .= "							<li><a href=\"#tabs-4\"><div style=\"font-size: 12px;\">Watched</div></a></li>\n";
$content .= "							<li><a href=\"#tabs-5\"><div style=\"font-size: 12px;\">Browse</div></a></li>\n";
//$content .= "							<li><a href=\"projects.php?action=getMonitoredProjects\"><div style=\"font-size: 12px;\">test</div></a></li>\n";
$content .= "						</ul>\n";




$content .= "						<div id=\"tabs-1\" class=\"ui-tabs-container ui-tabs-hide\">\n";

//check if there are project
if (db_numrows($q) > 0) {

	//setup content table
	$content .= "								<table id=\"mainTable\" cellpadding=\"0\" cellspacing=\"0\" class=\"tablesorter general\">\n";
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

		$content .= "      <tr>\n";

		//http://localhost/pm/projects.php?action=show&project_id=7#tasks.php?action=showTaskLevel&project_id=7&task_id=25
		//show name and a link
		$content .= "<td>\n";
		$content .= "<a href=\"projects.php?action=show&project_id=".$row['Project_ID']."#tasks.php?action=showTaskLevel&amp;project_id=".$row['Project_ID']."&amp;task_id=".$row['task_ID']."\"><b>".$row['task_name']."</b></a>\n";
		$content .= "</td>\n";

		$content .= "<td>".date('m-d-Y',strtotime($row['LastUpdated']))."</td>\n";
		$content .= "<td>".$row['PercentComplete']."</td>\n";
		$content .= "<td>".$row['Status']."</td>\n";
		$content .= "<td>".$row['Priority']."</td>\n";
		if (empty($row['Start_Date']) or $row['Start_Date']==='0000-00-00') {
			$startdate = "";
		} else {
			$startdate = date('m-d-Y',strtotime($row['Start_Date']));
		}
		$content .= "<td>".$startdate."</td>\n";
		if (empty($row['End_Date']) or $row['End_Date']==='0000-00-00') {
			$enddate = "";
		} else {
			$enddate = date('m-d-Y',strtotime($row['End_Date']));
		}
		$content .= "<td>".$enddate."</td>\n";
		$content .= "<td>".$row['managed']."</td>\n";

		$content .= "<td style=\"text-align:center\">\n";

		$content .= "<a href=\"javascript:void(0);\" onclick='fb.start({ href: \"task_notes.php?action=popupAdd&project_id=".$row['Project_ID']."&task_id=".$row['task_ID']."\", rev:\"width:650 height:530 infoPos:tc info:`feedback.php?currform=projects_list.php-New Task Note` infoText:Feedback infoOptions:`width:555 height:350` disableScroll:true caption:`NEW Task Note` doAnimations:false\" }); return false;'\">New</a>\n";

		$content .= "</td>\n";

		$content .= "      </tr>\n";

	}
	db_free_result($q);

	$content .= "    </tbody>\n";
	$content .= "  </table>\n";
}
$content .= "</div>\n";




$content .= "						<div id=\"tabs-2\" class=\"ui-tabs-container ui-tabs-hide\">\n";

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
	$content .= "    <thead>\n";
	$content .= "      <tr>\n";
	$content .= "        <th>Project Name</th>\n";
	$content .= "        <th>Lead Contact</th>\n";
	$content .= "        <th>Department</th>\n";
	$content .= "        <th>Date Started</th>\n";
	$content .= "      </tr>\n";
	$content .= "    </thead>\n";
	$content .= "    <tbody>\n";

	//show all projects
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
		$content .= "      <tr>\n";
		//show name and a link
		$content .= "<td>\n";
		$content .= "<a href=\"projects.php?action=show&amp;project_id=".$row['project_ID']."\"><b>".$row['Project_Name']."</b></a>\n";
		$content .= "</td>\n";

		$content .= "<td>".$row['FirstName']." ".$row['LastName']."</td>\n";
		$content .= "<td>".$row['Dept']."</td>\n";
		if (empty($row['StartDate']) or $row['StartDate']==='0000-00-00') {
			$startdate = "";
		} else {
			$startdate = date('m-d-Y',strtotime($row['StartDate']));
		}
		$content .= "<td>".$startdate."</td>\n";

		$content .= "      </tr>\n";

	}
	db_free_result($q);

	$content .= "    </tbody>\n";
	$content .= "  </table>\n";
}
$content .= "</div>\n";




$content .= "						<div id=\"tabs-3\" class=\"ui-tabs-container ui-tabs-hide\">\n";

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
	$content .= "    <thead>\n";
	$content .= "      <tr>\n";
	$content .= "        <th>Project Name</th>\n";
	$content .= "        <th>Lead Contact</th>\n";
	$content .= "        <th>Department</th>\n";
	$content .= "        <th>Date Started</th>\n";
	$content .= "        <th>Tasks</th>\n";
	$content .= "      </tr>\n";
	$content .= "    </thead>\n";
	$content .= "    <tbody>\n";

	//show all projects
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
		$content .= "      <tr>\n";

		//show name and a link
		$content .= "<td>\n";
		$content .= "<a href=\"projects.php?action=show&amp;project_id=".$row['project_ID']."\"><b>".$row['Project_Name']."</b></a>\n";
		$content .= "</td>\n";

		$content .= "<td>".$row['FirstName']." ".$row['LastName']."</td>\n";
		$content .= "<td>".$row['Dept']."</td>\n";
		if (empty($row['StartDate']) or $row['StartDate']==='0000-00-00') {
			$startdate = "";
		} else {
			$startdate = date('m-d-Y',strtotime($row['StartDate']));
		}
		$content .= "<td>".$startdate."</td>\n";

		//$inc_tasks = db_result(db_query('SELECT count(*) FROM tasks WHERE project_id='.$row['id'].' AND creator='.$_SESSION['UID'].' AND status <> "deleted" AND status <> "done"'),0,0);
		$content .= "<td><div align=\"center\">".$row['TotTasks']."</div></td>\n";

		$content .= "      </tr>\n";

	}
	db_free_result($q);

	$content .= "    </tbody>\n";
	$content .= "  </table>\n";
}
$content .= "</div>\n";




$content .= "						<div id=\"tabs-4\" class=\"ui-tabs-container ui-tabs-hide\">\n";
// PROJECT LIST THAT USER IS WATCHING
//"select value1 from user_prefs where user_ID=".$_SESSION['UID'] and pref_type='watchedProject'

// query to get the projects
$SQL  = "SELECT *, ";
$SQL .= "(select Dept_Name from departments where departments.department_id=emp.department_id) as Dept ";
$SQL .= "FROM projects proj, employees emp ";
$SQL .= "WHERE emp.employee_id=proj.owner_id ";
$SQL .= "AND proj.project_id in (select value1 from user_prefs where user_ID=".$_SESSION['UID']." and pref_type='watchedProject') ";
$SQL .= "AND (select count(*) from tasks where tasks.project_id=proj.project_id AND Assigned_To_ID=".$_SESSION['UID'].") > 0 ";
$SQL .= "AND proj.status<>'Complete' ";
$SQL .= "ORDER BY EndDate";

$q = db_query($SQL);

//check if there are project
if (db_numrows($q) > 0) {

	//setup content table
	$content .= "<table id=\"mainTable4\" cellpadding=\"0\" cellspacing=\"0\" class=\"tablesorter\">\n";
	$content .= "    <thead>\n";
	$content .= "      <tr>\n";
	$content .= "        <th>Project Name</th>\n";
	$content .= "        <th>Lead Contact</th>\n";
	$content .= "        <th>Department</th>\n";
	$content .= "        <th>Date Started</th>\n";
	$content .= "      </tr>\n";
	$content .= "    </thead>\n";
	$content .= "    <tbody>\n";

	//show all projects
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
		$content .= "      <tr>\n";
		//show name and a link
		$content .= "<td>\n";
		$content .= "<a href=\"projects.php?action=show&amp;project_id=".$row['project_ID']."\"><b>".$row['Project_Name']."</b></a>\n";
		$content .= "</td>\n";

		$content .= "<td>".$row['FirstName']." ".$row['LastName']."</td>\n";
		$content .= "<td>".$row['Dept']."</td>\n";
		if (empty($row['StartDate']) or $row['StartDate']==='0000-00-00') {
			$startdate = "";
		} else {
			$startdate = date('m-d-Y',strtotime($row['StartDate']));
		}
		$content .= "<td>".$startdate."</td>\n";

		$content .= "      </tr>\n";

	}
	db_free_result($q);

	$content .= "    </tbody>\n";
	$content .= "  </table>\n";
}
$content .= "</div>\n";




$content .= "						<div id=\"tabs-5\" class=\"ui-tabs-container ui-tabs-hide\">\n";
$content .= "	<div>Browse Projects</div>\n";
$content .= "</div>\n";
$content .= "</div>\n";

echo $content;

?>