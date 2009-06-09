<?php
/* $Id: task_list.php,v 1.12 2009/06/05 18:16:39 markp Exp $ */

//security check
if (!isset($_SESSION['UIDssssssssss'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

//init values
$content = '';

//is the project_id set in tasks.php ?
if (!@safe_integer($_REQUEST['project_id'])) {
	error('Task list', 'Not a valid value for project_id');
}
$project_id = $_REQUEST['project_id'];

// button stuff
$content .= "<style type=\"text/css\">\n";
$content .= "input.groovybutton\n";
$content .= "{\n";
$content .= "   font-size:8px;\n";
$content .= "   font-family:Arial,sans-serif;\n";
$content .= "   border-style:solid;\n";
$content .= "   border-color:#330000;\n";
$content .= "   border-width:1px;\n";
$content .= "}\n";
$content .= "</style>\n";
$content .= "<script language=\"javascript\">\n";
$content .= "function goLite(node)\n";
$content .= "{\n";
$content .= "   document.getElementById(node).style.backgroundColor = \"#99DDFF\";\n";
$content .= "}\n";
$content .= "function goDim(node)\n";
$content .= "{\n";
$content .= "   document.getElementById(node).style.backgroundColor = \"\";\n";
$content .= "}\n";
$content .= "</script>\n";







//query to get the children for this project_id
$SQL  = "SELECT tasks.status, tasks.project_id, tasks.End_Date, tasks.task_id, tasks.task_name, ";
$SQL .= "(select CONCAT(firstname,' ',lastname) as fullname FROM employees WHERE employee_id=tasks.Assigned_To_ID) as Task_AT ";
$SQL .= "FROM tasks, projects proj ";
$SQL .= "WHERE proj.project_id=tasks.project_id ";
$SQL .= "AND tasks.project_id=".$project_id." ";
$SQL .= "AND tasks.parent_task_id=0 ";
$SQL .= "ORDER BY tasks.start_date";
$q = db_query($SQL);

$content .= "<div style=\"float: right\">\n";
$content .= "<div align='center'><button type='button' onclick='fb.start({ href: \"tasks.php?action=popupAdd&project_id=".$project_id."\", rev:\"width:650 height:430 infoPos:tc showClose:false disableScroll:true caption:`NEW Task` doAnimations:false\" });'>New Task</button></div>";
$content .= "</div>\n";
$content .= "<div style=\"float: left; font-size: large; font-weight: bold\">Tasks</div>\n";

$content .= "<div style=\"clear: both;\"><table align='left' style=\"border-right: 1px solid #C1DAD7; border-bottom: 1px solid #C1DAD7;\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\">\n";
$content .= "<thead>\n";
$content .= "<th>Summary</th>\n";
$content .= "<th>Assigned To</th>\n";
$content .= "<th>Status</th>\n";
$content .= "<th>Due</th></tr>\n";
$content .= "<th>SubTask</th></tr>\n";
$content .= "<th>Note</th></tr>\n";
$content .= "</thead>\n";
$content .= "<tbody>\n";

//check for any tasks
if (db_numrows($q) > 0) {

	//show all tasks
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {

		$content .= "<tr". ( ($i & 1) ? ' class="alt"' : '').">";

		$content .= "<td width='100%' style=\"font-weight: bold; border-right:0; border-bottom:0;\"><input type=\"checkbox\" id=\"checkbox-1\">&nbsp;&nbsp;<a href=\"projects.php?action=showtask&amp;project_id=".$row['project_id']."&amp;task_id=".$row['task_id']."\">".$row['task_name']."</a></td>\n";

		// Assigned To
		$content .= "<td style=\"border-right:0; border-bottom:0;\">".$row['Task_AT']."</td>";
		$content .= "<td style=\"border-right:0; border-bottom:0;\">".$row['status']."</td>";
		if (empty($row['End_Date']) or $row['End_Date']==='0000-00-00') {
			$enddate = "";
		} else {
			$enddate = date('m-d-Y',strtotime($row['End_Date']));
		}
		$content .= "<td style=\"border-right:0; border-bottom:0;\">".$enddate."</td>";

		$content .= "<td align=\"center\" style=\"border-right:0; border-bottom:0;\">";
		//$content .= "<a onclick='fb.start({ href: \"tasks.php?action=popupAdd&project_id=".$row['project_id']."&task_id=".$row['task_id']."\", rev:\"width:650 height:430 scrolling:no infoPos:tc resizeDuration:1 showClose:false disableScroll:true caption:`NEW Sub Task` resizeDuration=1\" });'>New</a>";
		// button
		$content .= "<input\n";
		$content .= "   type=\"button\"\n";
		$content .= "   id=\"groovybtn".$row['task_id']."A\"\n";
		$content .= "   name=\"groovybtn".$row['task_id']."A\"\n";
		$content .= "   class=\"groovybutton\"\n";
		$content .= "   value=\"New\"\n";
		$content .= "   title=\"Add Subtask\"\n";
		$content .= "   onclick='fb.start({ href: \"tasks.php?action=popupAdd&project_id=".$row['project_id']."&task_id=".$row['task_id']."\", rev:\"width:650 height:430 infoPos:tc showClose:false disableScroll:true caption:`NEW SubTask` doAnimations:false\" });'\n";
		$content .= "   onMouseOver=\"goLite(this.id)\"\n";
		$content .= "   onMouseOut=\"goDim(this.id)\">\n";
		$content .= "</td>\n";
		$content .= "<td align=\"center\" style=\"border-right:0; border-bottom:0;\">";
		$content .= "<input\n";
		$content .= "   type=\"button\"\n";
		$content .= "   id=\"groovybtn".$row['task_id']."B\"\n";
		$content .= "   name=\"groovybtn".$row['task_id']."B\"\n";
		$content .= "   class=\"groovybutton\"\n";
		$content .= "   value=\"New\"\n";
		$content .= "   title=\"Add Note\"\n";
		$content .= "   onclick='fb.start({ href: \"task_notes.php?action=popupAdd&project_id=".$row['project_id']."&task_id=".$row['task_id']."\", rev:\"width:650 height:430 infoPos:tc showClose:false disableScroll:true caption:`NEW Task Note` doAnimations:false\" });'\n";
		$content .= "   onMouseOver=\"goLite(this.id)\"\n";
		$content .= "   onMouseOut=\"goDim(this.id)\">\n";
		$content .= "</td>";

		//finish the line
		$content .= "</tr>";

		//query to get the subtasks
		$SQLsub  = "SELECT tasks.status, tasks.project_id, tasks.End_Date, tasks.task_id, tasks.task_name, ";
		$SQLsub .= "(select CONCAT(firstname,' ',lastname) as fullname FROM employees WHERE employee_id=tasks.Assigned_To_ID) as Task_AT ";
		$SQLsub .= "FROM tasks, projects proj ";
		$SQLsub .= "WHERE proj.project_id=tasks.project_id ";
		$SQLsub .= "AND tasks.project_id=".$row['project_id']." ";
		$SQLsub .= "AND tasks.parent_task_id=".$row['task_id']." ";
		$SQLsub .= "ORDER BY tasks.start_date";
		$s = db_query($SQLsub);

		//check for any tasks
		if (db_numrows($s) > 0) {

			//show all tasks
			for ($j=0; $sub_row = @db_fetch_array($s, $j); ++$j) {
				// if subtasks, start here
				//--------------------------
				$content .= "<tr>\n";
				$content .= "<td width='100%' style=\"border-right:0; border-bottom:0;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" id=\"checkbox-1\">&nbsp;&nbsp;<a href=\"projects.php?action=showtask&amp;project_id=".$sub_row['project_id']."&amp;task_id=".$sub_row['task_id']."\">".$sub_row['task_name']."</a></td>\n";

				// Assigned To
				$content .= "<td style=\"border-right:0; border-bottom:0;\">".$sub_row['Task_AT']."</td>";
				$content .= "<td style=\"border-right:0; border-bottom:0;\">".$sub_row['status']."</td>";
				if (empty($row['End_Date']) or $row['End_Date']==='0000-00-00') {
					$enddate = "";
				} else {
					$enddate = date('m-d-Y',strtotime($row['End_Date']));
				}
				$content .= "<td style=\"border-right:0; border-bottom:0;\">".$enddate."</td>";

				$content .= "<td align=\"center\" style=\"border-right:0; border-bottom:0;\">&nbsp;</td>";
				$content .= "<td align=\"center\" style=\"border-right:0; border-bottom:0;\">";

				// button
				$content .= "<input\n";
				$content .= "   type=\"button\"\n";
				$content .= "   id=\"groovybtn".$sub_row['task_id']."\"\n";
				$content .= "   name=\"groovybtn".$sub_row['task_id']."\"\n";
				$content .= "   class=\"groovybutton\"\n";
				$content .= "   value=\"New\"\n";
				$content .= "   title=\"Add Note\"\n";
				$content .= "   onclick='fb.start({ href: \"task_notes.php?action=popupAdd&project_id=".$sub_row['project_id']."&task_id=".$sub_row['task_id']."\", rev:\"width:650 height:430 infoPos:tc showClose:false disableScroll:true caption:`NEW Task Note` doAnimations:false\" });'\n";
				$content .= "   onMouseOver=\"goLite(this.id)\"\n";
				$content .= "   onMouseOut=\"goDim(this.id)\">\n";

				$content .= "</td>";

				// end subtasks
				$content .= "</td></tr>\n";
			} // for each sub task
		} // end if db_numrows
	} // for each task

	// free memory
	db_free_result($s);

} else {
	$content .= "<tr><td style=\"border-right:0; border-bottom:0\"></td><td style=\"border-right:0; border-bottom:0\"></td><td style=\"border-right:0; border-bottom:0\"></td><td style=\"border-right:0; border-bottom:0\"></td></tr>";
} // no tasks

$content .= "</tbody>\n";
$content .= "</table></div>\n";
$content .= "</p>\n";

//free memory
db_free_result($q);

echo $content;
?>