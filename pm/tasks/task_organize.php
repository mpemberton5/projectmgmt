<?php
/* $Id$ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');
require_once(BASE.'includes/screen.php');
include_once(BASE.'includes/time.php');

//secure vars
$content = "";
$project_id = 0;
$task_id = 0;
//$parent_task_id = 0;

if (!@safe_integer($_REQUEST['project_id'])) {
	error('Task Add/Edit', 'Not a valid value for project_id');
}
$project_id = $_REQUEST['project_id'];

if (@safe_integer($_REQUEST['task_id'])) {
	$task_id = $_REQUEST['task_id'];
}

$SQL = "SELECT * FROM tasks WHERE Project_ID=".$project_id." and parent_task_ID=".$task_id." order by order_num";

// START CONTEXT
$content .= "<script type='text/javascript'>\n";
$content .= "$(document).ready(function(){\n";
// SUBMIT BUTTON FOR REORDERING TAB
$content .= "	$(\"#ReorderTasks\").submit(function() {\n";
$content .= "		var parameterA = $(this).serialize();\n";
$content .= "		var parameterB = $('#sortable').sortable('serialize');\n";
$content .= "		var parameters = parameterA + '&' + parameterB;\n";
$content .= "		$.ajax({\n";
$content .= "			type: \"POST\",\n";
$content .= "			url: \"tasks.php\",\n";
$content .= "			data: parameters,\n";
$content .= "			error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "				parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "    		},\n";
$content .= "			success: function(del){\n";
$content .= "				parent.fb.loadPageOnClose='self';\n";
$content .= "				parent.fb.end(true);\n";
$content .= "			}\n";
$content .= "		});\n";
$content .= "		return false;\n";
$content .= "	});\n";
// SUBMIT BUTTON FOR REWEIGHT TAB
$content .= "	$(\"#ReWeighTasks\").submit(function() {\n";
$content .= "		var parameters = $(this).serialize();\n";
$content .= "		$.ajax({\n";
$content .= "			type: \"POST\",\n";
$content .= "			url: \"tasks.php\",\n";
$content .= "			data: parameters,\n";
$content .= "			error: function(xhr, ajaxOptions, thrownError){\n";
$content .= "				parent.fb.start({href:'error.php?error='+xhr.responseText, rev:'theme:red showClose:true width:560 height:240', title:'Unexpected Error'});\n";
$content .= "    		},\n";
$content .= "			success: function(del){\n";
$content .= "				parent.fb.loadPageOnClose='self';\n";
$content .= "				parent.fb.end(true);\n";
$content .= "			}\n";
$content .= "		});\n";
$content .= "		return false;\n";
$content .= "	});\n";
// SORTABLE JQUERY CODE
$content .= "	$(\"#sortable\").sortable({\n";
$content .= "		placeholder: 'ui-state-highlight',\n";
$content .= "		items: 'li:not(.ui-state-disabled)'\n";
$content .= "	});\n";
$content .= "	$(\"#sortable\").disableSelection();\n";
// SLIDER JQUERY CODE
$content .= "	$.extend($.ui.slider.defaults, {\n";
$content .= "		range: \"min\",\n";
$content .= "		animate: true,\n";
$content .= "		orientation: \"vertical\"\n";
$content .= "	});\n";
$content .= "	$(\".slide\").each(function() {\n";
$content .= "		// read initial values from markup and remove that\n";
$content .= "		var value = parseInt($(this).text());\n";
$content .= "		$(this).empty();\n";
$content .= "		$(this).slider({\n";
$content .= "			slide: function(event, ui) { $('#taskw-'+$(this).attr('id').substr(7)).val(ui.value); },\n";
$content .= "			value: value, step:1, min:1, max:10,\n";
$content .= "			orientation: \"horizontal\"\n";
$content .= "		})\n";
$content .= "	});\n";
// ACTIVATE TABS
$content .= "	$(\"#tabs\").tabs();\n";
$content .= "});\n";
$content .= "</script>\n";

$content .= "<link type='text/css' rel='stylesheet' href='/public/slider/css/redmond/jquery-ui-1.7.1.custom.css'>\n";
$content .= "<link type='text/css' rel='stylesheet' href='/public/slider/css/ui.slider.extras.css'>\n";

$content .= "<div id=\"tabs\" class=\"ui-tabs\">\n";
$content .= "	<ul>\n";
$content .= "		<li><a href=\"#tabs-1\"><div style=\"font-size: 12px;\">Modify Weight</div></a></li>\n";
$content .= "		<li><a href=\"#tabs-2\"><div style=\"font-size: 12px;\">Reorder</div></a></li>\n";
$content .= "	</ul>\n";
$content .= "	<div id=\"tabs-1\" class=\"ui-tabs-hide\" style=\"padding-left: 2px; padding-right: 2px;\">\n";

if ($task_id==0) {
	$content .= "<br />Unable to Modify at Milestone Level<br />";
} else {
	$content .= "<div style=\"position:relative;\">\n"; // top div
	$content .= "<br /><br />\n";
	$content .= "<div style=\"position:absolute; top:10px; right:10px;\">10</div>\n";
	$content .= "<div style=\"position:absolute; top:10px; right:42px;\">9</div>\n";
	$content .= "<div style=\"position:absolute; top:10px; right:72px;\">8</div>\n";
	$content .= "<div style=\"position:absolute; top:10px; right:101px;\">7</div>\n";
	$content .= "<div style=\"position:absolute; top:10px; right:130px;\">6</div>\n";
	$content .= "<div style=\"position:absolute; top:10px; right:158px;\">5</div>\n";
	$content .= "<div style=\"position:absolute; top:10px; right:186px;\">4</div>\n";
	$content .= "<div style=\"position:absolute; top:10px; right:215px;\">3</div>\n";
	$content .= "<div style=\"position:absolute; top:10px; right:245px;\">2</div>\n";
	$content .= "<div style=\"position:absolute; top:10px; right:274px;\">1</div>\n";

	$q = db_query($SQL);

	$vert_h = 0;
	$tot_rows = db_numrows($q);
	if ($tot_rows > 0) {
		$vert_h += $tot_rows*30;
		$vert_h += $tot_rows/2;
	}
	$content .= "<div class=\"vert\" style=\"right:45px; height:".$vert_h."px;\">&nbsp;</div>\n";
	$content .= "<div class=\"vert\" style=\"right:75px; height:".$vert_h."px;\">&nbsp;</div>\n";
	$content .= "<div class=\"vert\" style=\"right:104px; height:".$vert_h."px;\">&nbsp;</div>\n";
	$content .= "<div class=\"vert\" style=\"right:133px; height:".$vert_h."px;\">&nbsp;</div>\n";
	$content .= "<div class=\"vert\" style=\"right:161px; height:".$vert_h."px;\">&nbsp;</div>\n";
	$content .= "<div class=\"vert\" style=\"right:190px; height:".$vert_h."px;\">&nbsp;</div>\n";
	$content .= "<div class=\"vert\" style=\"right:219px; height:".$vert_h."px;\">&nbsp;</div>\n";
	$content .= "<div class=\"vert\" style=\"right:249px; height:".$vert_h."px;\">&nbsp;</div>\n";
	$content .= "</div>\n"; // end top div

	$content .= "<form action=\"\" id=\"ReWeighTasks\" name=\"ReWeighTasks\" method=\"post\">\n";
	$content .= "	<input type=\"hidden\" name=\"action\" value=\"submit_weight\" />\n";
	$content .= "	<input type=\"hidden\" name=\"project_id\" value=\"".$project_id."\" />\n";
	$content .= "<table style=\"width:100%; padding:0px; cellspacing:0px; border-width: 0px 0px 0px 0px;\">\n";

	for ($i=0; $task_row = @db_fetch_array($q, $i); ++$i) {
		$content .= "	<tr>\n";
		$content .= "		<td style=\"width:100%; border-width: 0px 0px 0px 0px;\"><div class=\"txtmaxsize\" style=\"width:280px\">".$task_row['task_name'].":</div></td>\n";
		$content .= "		<td style=\"width:100%; border-width: 0px 0px 0px 0px;\">\n";
		$content .= "			<div class=\"slide\" id=\"slider-".$task_row['task_ID']."\" style=\"width:260px; margin:5px; clear:right;\">".$task_row['weight']."</div>\n";
		$content .= "			<input id=\"taskw-".$task_row['task_ID']."\" type=\"hidden\" name=\"taskw-".$task_row['task_ID']."\" value=\"".$task_row['weight']."\" />\n";
		$content .= "		</td>\n";
		$content .= "	</tr>\n";
	}
	db_free_result($q);
	$content .= "</table>\n";

	$content .= "<p />\n";
	$content .= "<div align=\"center\">\n";
	$content .= "	<input type=\"submit\" name=\"submit\" class=\"button2\" id=\"submit_btnw\" value=\"Save\" />\n";
	$content .= "	&nbsp;&nbsp;&nbsp;\n";
	$content .= "	<input type=\"button\" value=\"Cancel\" onClick=\"parent.fb.end(true); return false;\" />\n";
	$content .= "</div>\n";
	$content .= "</form>\n";

}

$content .= "	</div>\n";
$content .= "	<div id=\"tabs-2\" class=\"ui-tabs-hide\" style=\"padding: 2px; padding-right: 2px;\">\n";

// query all tasks/subtasks
//query to get the children for this project_id
//$SQL  = "SELECT * FROM tasks where project_ID=".$project_id." and parent_task_ID=".$task_id." ORDER BY order_num";
$q = db_query($SQL);

$content .= "<form action=\"\" id=\"ReorderTasks\" name=\"ReorderTasks\" method=\"post\">\n";
$content .= "	<input type=\"hidden\" name=\"action\" value=\"submit_task_list_order\" />\n";
$content .= "	<input type=\"hidden\" name=\"project_id\" value=\"".$project_id."\" />\n";
$content .= "	<input type=\"hidden\" name=\"parent_task_id\" value=\"".$task_id."\" />\n";

//check for any tasks
if (db_numrows($q) > 0) {
	$content .= "Note: drag items to change order.<p />\n";
	$content .= "<ul id=\"sortable\">\n";
	//show all tasks
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {
		$content .= "	<li id=\"task-".$row['task_ID']."\" class=\"ui-state-default\">\n";
		$content .= "		<span class=\"ui-icon ui-icon-arrowthick-2-n-s\"></span>\n";
		$content .= "		<div class=\"txtmaxsize\" style=\"width:550px\">".$row['task_name']."</div>\n";
		$content .= "	</li>\n";
	}
	$content .= "</ul>\n";
}
db_free_result($q);


$content .= "<p />\n";
$content .= "<p />\n";
$content .= "<div align=\"center\">\n";
$content .= "	<input type=\"submit\" name=\"submit\" class=\"button1\" id=\"submit_btnrt\" value=\"Save\" />\n";
$content .= "	&nbsp;&nbsp;&nbsp;\n";
$content .= "	<input type=\"button\" value=\"Cancel\" onClick=\"parent.fb.end(true); return false;\" />\n";
$content .= "</div>\n";
$content .= "</form>\n";

$content .= "	</div>\n";
$content .= "</div>\n";

echo $content;

?>