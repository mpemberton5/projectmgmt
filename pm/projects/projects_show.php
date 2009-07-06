<?php
/* $Id$ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

//includes
require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'includes/time.php');


//
// Recursive function for listing all posts of a task
//
function list_posts_from_task($project_id) {

	global $post_array, $parent_array, $post_count;

	$post_array   = array();
	$parent_array = array();
	$post_count   = 0;

	//query to get the children for this project_id
	$SQL = "SELECT * FROM tasks where project_ID=".$project_id." ORDER BY parent_task_ID, order_num";

	$q = db_query($SQL);

	//check for any posts
	if (db_numrows($q) < 1) {
		return "<p>";
	}

	$content = "	<ul>\n";

	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {

		$task_id = $row['task_ID'];
		$task_name = $row['task_name'];
		$parent_task_id = $row['parent_task_ID'];

			//put values into array
			$post_array[$i]['id'] = $task_id;
			$post_array[$i]['parent_id'] = $parent_task_id;

			//if this is a subpost, store the parent id
			if ($parent_task_id != 0) {
				$parent_array[$parent_task_id] = $parent_task_id;
				//$this_post = "				<li><a class=\"clickMe\" id=\"task-".$task_id."\" href=\"tasks.php?action=showTaskLevel&project_id=".$project_id."&task_id=".$task_id."\">".$task_name."</a></li>\n";
				$this_post = "				<li id=\"task-".$task_id."\" data=\"addClass: 'txtmaxsize', url: 'tasks.php?action=showTaskLevel&project_id=".$project_id."&task_id=".$task_id."'\">".$task_name."</li>\n";
			} else {
				//$this_post = "		<li><a class=\"clickMe\" id=\"task-".$task_id."\" style=\"font-weight: bold;\" href=\"tasks.php?action=showMilestoneLevel&project_id=".$project_id."&task_id=".$task_id."\">".$task_name."</a>\n";
				$this_post = "		<li id=\"task-".$task_id."\" data=\"addClass: 'txtmaxsize', url: 'tasks.php?action=showMilestoneLevel&project_id=".$project_id."&task_id=".$task_id."'\">".$task_name."\n";
			}

			$post_array[$i]['post'] = $this_post;
			++$post_count;

	}

	//iteration for first level posts
	for ($i=0; $i < $post_count; ++$i) {

		//ignore subtasks in this iteration
		if ($post_array[$i]['parent_id'] != 0) {
			continue;
		}
		$content .= $post_array[$i]['post'];

		//if this post has children (subposts), iterate recursively to find them
		if (isset($parent_array[($post_array[$i]['id'])])) {
			$content .= find_children($post_array[$i]['id']);
		}
		$content .= "		</li>\n";
	}
	$content .= "	</ul>\n";

	db_free_result($q);

	return $content;
}

//
// List subposts (recursive function)
//
function find_children($parent_id) {

	global $post_array, $parent_array, $post_count;

	$content = "			<ul>\n";

	for($i=0; $i < $post_count ; ++$i) {

		if ($post_array[$i]['parent_id'] != $parent_id) {
			continue;
		}
		$content .= $post_array[$i]['post'];

		//if this post has children (subposts), iterate recursively to find them
		if (isset($parent_array[($post_array[$i]['id'])])) {
			$content .= find_children($post_array[$i]['id']);
		}
		//$content .= "</li>\n";
	}
	$content .= "			</ul>\n";

	return $content;
}




//secure variables
$content = '';

//CHECK PASSED CLOSING ID
if (!@safe_integer($_GET['project_id']) || $_GET['project_id'] == 0) {
	//error('Project show', 'Not a valid value for project_id');
	error('Project Details Error', 'The requested item has either been deleted, invalid, or you do not have access to this project.', 'boxdata2');
	die();
}
$project_id = $_GET['project_id'];

//CREATE QUERY
$project_name = db_simplequery("projects","Project_Name","project_id",$project_id);

// TREEVIEW SCRIPTS
$content .= "<script type=\"text/javascript\" src=\"/public/dynatree/jquery/jquery.cookie.js\"></script>\n";
$content .= "<script type=\"text/javascript\" src=\"/public/dynatree/src/jquery.dynatree.js\"></script>\n";
// - http://projects.allmarkedup.com/jquery_url_parser/
$content .= "<script type=\"text/javascript\" src=\"/public/url/jquery.url.js\"></script>\n";
$content .= "<script language=\"javascript\">\n";

$content .= "$(document).ready(function() {\n";
$content .= "	function hideLoader() {\n";
$content .= "		$('#loader').fadeOut('normal');\n";
$content .= "	}\n";
$content .= "	$(\"#treev\").dynatree({\n";
$content .= "		persist: true,\n";
$content .= "		selectMode: 1,\n";
$content .= "		onActivate: function(dtnode) {\n";
$content .= "			if( dtnode.data.url ) {\n";
$content .= "				$('#loader').remove();\n";
$content .= "				$('#wrapper').append('<span id=\"loader\">LOADING...<\/span>');\n";
$content .= "				$('#loader').fadeIn('normal');\n";
$content .= "				$('#contentArea').load(dtnode.data.url,hideLoader);\n";
$content .= "				$(\"#echoActive\").text(dtnode.data.title);\n";
$content .= "				window.location.hash = dtnode.data.url;\n";
$content .= "				return false;\n";
$content .= "			}\n";
$content .= "		},\n";
$content .= "		onDeactivate: function(dtnode) {\n";
$content .= "			$(\"#echoActive\").text(\"-\");\n";
$content .= "		},\n";
$content .= "		onDblClick: function(dtnode, event) {\n";
$content .= "			dtnode.toggleExpand();\n";
$content .= "		}\n";
$content .= "	});\n";

// AJAX DYNAMIC CONTENT LOADING
// http://nettuts.com/javascript-ajax/how-to-load-in-and-animate-content-with-jquery/
$content .= "	var hash = window.location.hash.substr(1);\n";
//$content .= "alert(hash);";
$content .= "	$('.clickMe').each(function(){\n";
$content .= "		var href = $(this).attr('href');\n";
$content .= "		$(\"#treev\").dynatree(\"getRoot\").visit(function(dtnode){\n;";
$content .= "			if (hash==dtnode.data.url){\n";
$content .= "				var toLoad = hash;\n";
$content .= "				var taskid = jQuery.url.setUrl(hash).param(\"task_id\");\n";
$content .= "				$(\"#treev\").dynatree(\"getTree\").getNodeByKey(\"task-\"+taskid).activate();\n";
$content .= "				$('#contentArea').load(toLoad);\n";
$content .= "			}\n";
$content .= "		});\n";
$content .= "	});\n";
$content .= "});\n";

$content .= "function gotoTopLevel() {\n";
$content .= "	$.cookie('ui-dynatree-cookie-select', '');\n";
$content .= "	$.cookie('ui-dynatree-cookie-active', '');\n";
$content .= "	$.cookie('ui-dynatree-cookie-expand', '');\n";
$content .= "}\n";

$content .= "</script>\n";

// TABLE START
$content .= "<table style=\"width:100%;\">\n";
$content .= "	<tr>\n";
$content .= "		<td align=\"left\" style=\"width: 180px; overflow:hidden; vertical-align:top;\">\n";

$content .= "			<div class=\"clickMe txtmaxsize\" style=\"font-family: sans-serif; font-weight: bold; font-size: large; width: 250px; overflow:hidden;\">\n";
$content .= "				<a onClick=\"gotoTopLevel();\" href=\"projects.php?action=show&project_id=".$project_id."\">".$project_name."</a>\n";
$content .= "			</div>\n";

$content .= "			<div id=\"treev\">\n";
$content .= list_posts_from_task($project_id);
$content .= "			</div>\n";

$content .= "		</td>\n";
$content .= "		<td valign=\"top\" align=\"left\">\n";
$content .= "			<div id=\"wrapper\">\n";
$content .= "				<div id=\"contentArea\"></div>\n";
$content .= "			</div>\n";
$content .= "		</td>\n";
$content .= "	</tr>\n";
$content .= "</table>\n";

$content .= "<script language='javascript' type='text/javascript'>\n";
$content .= "	var hash = window.location.hash.substr(1);\n";
$content .= "	if (hash.length==0) {\n";
$content .= "		$('#contentArea').load('tasks.php?action=showTopLevel&project_id=".$project_id."');\n";
$content .= "	}\n";
$content .= "</script>\n";
echo $content;

?>