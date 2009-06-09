<?php
/* $Id: note_edit.php,v 1.8 2009/06/03 04:19:51 markp Exp $ */

//security check
if (!isset($_SESSION['UID'])) {
  die('Direct file access not permitted');
}

//includes
require_once('path.php');
require_once(BASE.'includes/security.php');

//secure vars
$content = '';

if (!@safe_integer($_REQUEST['project_id'])) {
  error('Note Add/Edit', 'Not a valid value for project_id');
}
$project_id = $_REQUEST['project_id'];

if (!@safe_integer($_REQUEST['task_id'])) {
  error('Note Add/Edit', 'Not a valid value for task_id');
}
$task_id = $_REQUEST['task_id'];

// Populate Local Variables
if ($_REQUEST['action'] == "popupAdd") {
	$form_submit = "submit_insert";
	$note = '';
	$note_id = 0;
	$percentcomplete = db_result(db_query('SELECT PercentComplete FROM tasks WHERE task_ID='.$task_id),0,0);

} else if ($_REQUEST['action'] == "popupEdit") {
	if (!@safe_integer($_REQUEST['note_id'])) {
	  error('Note Add/Edit', 'Not a valid value for note_id');
	}
	$note_id = $_REQUEST['note_id'];

	//query to get the children for this project_id
	$q = db_query('SELECT * FROM task_notes WHERE note_id='.$note_id);

	//check for any posts
	if (db_numrows($q) < 1) {
		error("Task Note Edit", "Unable to Find Message");
	}

	if (!$row = db_fetch_array($q, 0)) {
	  error("Task Note Edit", "Unable to Find Task");
	}

	$form_submit = "submit_update";
	$note = $row['Note'];
	$percentcomplete = $row['PercentComplete'];

	db_free_result($q);
}


//$mod_id = db_result(db_query('SELECT id FROM data_table WHERE type="MODULES" AND description="Messages"'),0,0);
//$side = $_SESSION['side'];
//$sec_lvl = $_SESSION['sec_lvl_id'];
//get the text from the parent and the username of the person that posted that text
////$q = db_query('SELECT * FROM messages WHERE messages.id='.$message_id);

$content .= "<script type='text/javascript' src='/public/slider/js/selectToUISlider.jQuery.js'></script>\n";
$content .= "<script type='text/javascript' src='/public/tinymce/jscripts/tiny_mce/tiny_mce.js'></script>\n";

$content .= "<script type='text/javascript'>\n";
$content .= "$(function() {\n";
$content .= "	$(\".button\").click(function() {\n";
$content .= "		var parameter1 = $(\"input\").serialize();\n";
$content .= "		var parameter2 = $(\"select\").serialize();\n";
$content .= "   	var parameter3 = 'note=' + escape(tinyMCE.get('note').getContent());\n";
$content .= "		var parameters = parameter1 + '&' + parameter2 + '&' + parameter3;\n";
$content .= "		$.ajax({\n";
$content .= "			type: \"POST\",\n";
$content .= "			url: \"task_notes.php\",\n";
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
$content .= "	$('select#percentcomplete').selectToUISlider();\n";
$content .= "});\n";
$content .= "</script>\n";

$content .= "<link type='text/css' rel='stylesheet' href='/public/slider/css/redmond/jquery-ui-1.7.1.custom.css'>\n";
$content .= "<link type='text/css' rel='stylesheet' href='/public/slider/css/ui.slider.extras.css'>\n";

$content .= "<style type=\"text/css\">\n";
$content .= "	fieldset { border:0; margin-right: 1em; margin-left: 1em; margin-top: 1em;}\n";
$content .= "	.fspc { width: 88%; height: 40px; padding: 7px 0 0 2px; #padding-top: 17px;}\n";
$content .= "</style>\n";

$content .= "<script type='text/javascript'>\n";
$content .= "tinyMCE.init({\n";
$content .= "  plugins : 'layer,table,advhr,advimage,advlink,iespell,insertdatetime,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',\n";
$content .= "  themes : 'advanced',\n";
$content .= "  languages : 'en',\n";
$content .= "  disk_cache : true,\n";
$content .= "  debug : false\n";
$content .= "});\n";
$content .= "</script>\n";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "tinyMCE.init({\n";
$content .= "  mode : 'textareas',\n";
$content .= "  theme : 'advanced',\n";
$content .= "  auto_focus : 'note',\n";
$content .= "  elements : 'note',\n";
$content .= "  plugins : 'layer,table,advhr,advimage,advlink,iespell,insertdatetime,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',\n";
$content .= "  theme_advanced_buttons1 : 'bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,|,undo,redo,|,formatselect,fontselect,fontsizeselect,|,fullscreen',\n";
$content .= "  theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,forecolor,backcolor',\n";
$content .= "  theme_advanced_buttons3 : '',\n";
$content .= "  theme_advanced_toolbar_location : 'top',\n";
$content .= "  theme_advanced_toolbar_align : 'left',\n";
$content .= "  theme_advanced_path_location : 'bottom',\n";
$content .= "  plugin_insertdate_dateFormat : '%Y-%m-%d',\n";
$content .= "  plugin_insertdate_timeFormat : '%H:%M:%S',\n";
$content .= "  extended_valid_elements : 'a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]'\n";
$content .= "});\n";
$content .= "</script>\n";

//find out the project name
///$project_name = db_result(db_query('SELECT project_name FROM projects WHERE id='.$project_id), 0, 0);

// FORM
//$content .= "<div class=\"container\">";
$content .= "<form action=\"\" name=\"UpdateForm\" method=\"post\">\n";
$content .= "<input type=\"hidden\" name=\"action\" value=\"".$form_submit."\" />\n";
$content .= "<input type=\"hidden\" name=\"note_id\" value=\"".$note_id."\" />\n";
$content .= "<input type=\"hidden\" name=\"project_id\" value=\"".$project_id."\" />\n";
$content .= "<input type=\"hidden\" name=\"task_id\" value=\"".$task_id."\" />\n";

//build up the text-entry part
$content .= "<textarea id=\"note\" name=\"note\" style=\"width:100%\">".$note."</textarea>\n";

$content .= "<p />";

$content .= "<div style=\"width: 98%; height: 100px;\">\n";
$content .= "	<div style=\"float: left; width: 49%;\">";
$content .= "		<fieldset class=\"gfs\" style=\"width: 90%;\">\n";
$content .= "			<legend><span class=\"gl\" style=\"width: 120px;\">Percent Complete</span></legend>\n";
$content .= "			<fieldset class=\"fspc\">\n";
$content .= "				<select style=\"display: none;\" name=\"percentcomplete\" id=\"percentcomplete\">\n";
$content .= "					<option value=\"0\"".(($percentcomplete=='0') ? ' selected=\'selected\'' : '') .">0%</option>\n";
$content .= "					<option value=\"10\"".(($percentcomplete=='10') ? ' selected=\'selected\'' : '') .">10%</option>\n";
$content .= "					<option value=\"20\"".(($percentcomplete=='20') ? ' selected=\'selected\'' : '') .">20%</option>\n";
$content .= "					<option value=\"30\"".(($percentcomplete=='30') ? ' selected=\'selected\'' : '') .">30%</option>\n";
$content .= "					<option value=\"40\"".(($percentcomplete=='40') ? ' selected=\'selected\'' : '') .">40%</option>\n";
$content .= "					<option value=\"50\"".(($percentcomplete=='50') ? ' selected=\'selected\'' : '') .">50%</option>\n";
$content .= "					<option value=\"60\"".(($percentcomplete=='60') ? ' selected=\'selected\'' : '') .">60%</option>\n";
$content .= "					<option value=\"70\"".(($percentcomplete=='70') ? ' selected=\'selected\'' : '') .">70%</option>\n";
$content .= "					<option value=\"80\"".(($percentcomplete=='80') ? ' selected=\'selected\'' : '') .">80%</option>\n";
$content .= "					<option value=\"90\"".(($percentcomplete=='90') ? ' selected=\'selected\'' : '') .">90%</option>\n";
$content .= "					<option value=\"100\"".(($percentcomplete=='100') ? ' selected=\'selected\'' : '') .">100%</option>\n";
$content .= "				</select>\n";
$content .= "			</fieldset>\n";
$content .= "		</fieldset>\n";
$content .= "	</div>\n";
$content .= "	<div style=\"float: right; width: 49%;\">";
$content .= "		<fieldset class=\"gfs\" style=\"width: 90%;\">\n";
$content .= "			<legend><span class=\"gl\" style=\"width: 90px;\">Notifications</span></legend>\n";
$content .= "			<input type=\"radio\" name=\"notify\" value=\"None\" checked />None<br />\n";
$content .= "			<input type=\"radio\" name=\"notify\" value=\"Lead\"/>Notify Project Lead<br />\n";
$content .= "			<input type=\"radio\" name=\"notify\" value=\"Part\"/>Notify All Project Participants\n";
$content .= "		</fieldset>\n";
$content .= "	</div>\n";
$content .= "</div>\n";

$content .= "<div style=\"width: 98%; height: 100px;\">\n";
$content .= "	<div style=\"float: left; width: 49%;\">";
$content .= "		<fieldset class=\"gfs\" style=\"width: 90%;\">\n";
$content .= "			<legend><span class=\"gl\" style=\"width: 90px;\">Next Action</span></legend>\n";
$content .= "			<input type=\"radio\" name=\"task_action\" value=\"None\" checked />None<br />\n";
$content .= "			<input type=\"radio\" name=\"task_action\" value=\"Prev\"/>Send to Previous Task<br />\n";
$content .= "			<input type=\"radio\" name=\"task_action\" value=\"Comp\"/>Complete Task\n";
$content .= "		</fieldset>\n";
$content .= "	</div>\n";
$content .= "	<div style=\"float: right; width: 49%;\">";
$content .= "		<fieldset class=\"gfs\" style=\"width: 90%; height: 83px;\">\n";
$content .= "			<legend><span class=\"gl\" style=\"width: 90px;\">Attachments</span></legend>\n";
$content .= "			<br /><div align=\"center\"><input type=\"button\" name=\"add_attach\" value=\"Add\" onclick='parent.fb.start({ href:\"files.php?action=popupAdd&project_id=".$project_id."\", rev:\"width:665 height:515 infoPos:tc disableScroll:true caption:`Add Attachment` doAnimations:false\" });' /></div>\n";
$content .= "		</fieldset>\n";
$content .= "	</div>\n";
$content .= "</div>\n";

$content .= "<p /><br />\n";

$content .= "<div align=\"center\">\n";
$content .= "	<input type=\"submit\" name=\"submit\" class=\"button\" id=\"submit_btn\" value=\"Save\" />\n";
$content .= "	&nbsp;&nbsp;&nbsp;\n";
$content .= "	<input type=\"button\" value=\"Cancel\" onClick=\"parent.fb.end(true); return false;\" />\n";
$content .= "</div>\n";

$content .= "</form>\n";

echo $content;

?>