<?php
/* $Id: screen.php,v 1.16 2009/06/08 05:04:44 markp Exp $ */
/*
 Create the windowed interface and define a simple API

 The screen is split in 3 components. The overall table is called main_table

 +----------------+
 |  info          |  <- create_complete_top()
 +----------------+
 |   |            |
 | m |            |
 | e |            |
 | n |  main      |
 | u |            |
 |   |            |
 |   |            |
 +---+------------+


 And the api is :
 ----------------

 new_box(title, content);
 goto_main();

 This implicates that all the boxes you create before calling goto_main() will be menu boxes. After
 the calling goto_main() all boxes are main window boxes.


 the internal functions are:
 ---------------------------

 create_complete_top();
 create_bottom();
 */

/******************************************************************************/
/******************************************************************************/
/**
 * @param $redirect_time
 * @return unknown_type
 */
function html_header($redirect_time=0) {
	//we don't want any caching of these pages
	////  header('Cache-Control: no-store, no-cache, must-revalidate');
	////  header('Cache-Control: post-check=0, pre-check=0', false);
	  header('Expires: Wed, 28 Jul 1997 12:37:00 GMT');
	////  header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	////  header('Pragma: no-cache');
	header('Content-Type: text/html; charset=ISO-8859-1');

	//do a refresh if required
	if ($redirect_time != 0) {
		header('Refresh: '.$redirect_time.'; url='.BASE_URL.'index.php');
	}

	return;
}

/******************************************************************************/
/******************************************************************************/
/**
 * Function that starts the HTML document
 *
 * @return unknown_type
 */
function html_pre_html() {
	//$content = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
	//           "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	//$content = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n";
	//$content = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	//$content = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
	$content = "<!DOCTYPE html>";
	//flush buffer
	echo $content;
	return;
}

/******************************************************************************/
/******************************************************************************/
/**
 * @return unknown_type
 */
function html_html_start() {
	$content  = "<html>\n";
	$content .= "<!-- Project Management Version ".PM_VERSION." -->\n";

	//flush buffer
	echo $content;
	return;
}

/******************************************************************************/
/******************************************************************************/
/**
 * @param $title
 * @return content containing title
 */
function html_head_start($title='') {
	$content = "<head>\n";

	if ($title == '') {
		$title = MANAGER_NAME;
	} else {
		$title = ABBR_MANAGER_NAME . " - " . $title;
	}

	$content .= "	<title>".$title."</title>\n";

	//flush buffer
	echo $content;
	return;
}

/******************************************************************************/
/******************************************************************************/
/**
 * @param $redirect_time
 * @return unknown_type
 */
function html_meta($redirect_time=0) {
	$content = "	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />\n";
//	$content = "	<meta http-equiv=\"Pragma\" content=\"no-cache\" />\n";
//	$content = "	<meta http-equiv=\"Expires\" content=\"-1\" />\n";

	//do a refresh if required
	if ($redirect_time != 0) {
		$content .= "	<meta http-equiv=\"Refresh\" content=\"".$redirect_time.";url=".BASE_URL."index.php\" />\n";
	}

	//flush buffer
	echo $content;

	return;
}

/******************************************************************************/
/******************************************************************************/
/**
 * @param $page_type
 * @return unknown_type
 */
function html_css($page_type=0) {
	$content = "";
	switch($page_type) {
		case 2: //print
			$content .= "	<link rel=\"StyleSheet\" href=\"".BASE_CSS.CSS_PRINT."\" type=\"text/css\" />\n";
			break;

		case 3: //calendar
			$content .= "	<link rel=\"StyleSheet\" href=\"".BASE_CSS.CSS_MAIN."\" type=\"text/css\" />\n";
			$content .= "	<link rel=\"StyleSheet\" href=\"".BASE_CSS.CSS_CALENDAR."\" type=\"text/css\" />\n";
			break;

		case 10: //NO Style Sheets
			break;
		case 0: //main window + menu sidebar
		case 1: //single main window (no menu sidebar)
		default:
			$content .= "	<link type='text/css' rel='stylesheet' href='".BASE_CSS.CSS_MAIN."' />\n";
			$content .= "	<link type='text/css' rel='stylesheet' href='/public/jquery/development-bundle/themes/base/ui.all.css' />\n";
			$content .= "	<link type='text/css' rel='stylesheet' href='/public/jquery/development-bundle/demos/demos.css' />\n";
			$content .= "	<link type='text/css' rel='stylesheet' href='/public/floatbox/compressed/floatbox.css' />\n";
			$content .= "	<link type='text/css' rel='stylesheet' href='/public/dynatree/src/skin/ui.dynatree.css' />\n";
			$content .= "	<link type='text/css' rel='stylesheet' href='/public/jgrowl/jquery.jgrowl.css' />\n";
			break;
	}
	//flush buffer
	echo $content;
	return;
}

/******************************************************************************/
/******************************************************************************/
/**
 * @param $cursor
 * @param $check
 * @param $date
 * @param $calendar
 * @return unknown_type
 */
function html_javascript($cursor=0, $check=0, $date=0, $calendar=0) {
	//global javascripts
	$content = "";

	$content .= "	<script type='text/javascript' src='/public/floatbox/compressed/floatbox.js'></script>\n";
	$content .= "	<script type=\"text/javascript\" src=\"js/tablesort.min.js\"></script>\n";
	$content .= "	<script type=\"text/javascript\" src=\"js/paginate.min.js\"></script>\n";
	$content .= "	<script type=\"text/javascript\" src=\"/public/jquery/js/jquery-1.3.2.min.js\"></script>\n";
	$content .= "	<script type=\"text/javascript\" src=\"/public/jquery/js/jquery-ui-1.7.1.custom.min.js\"></script>\n";
	$content .= "	<script type=\"text/javascript\" src=\"/public/jgrowl/jquery.jgrowl_minimized.js\"></script>\n";

	//flush buffer
	echo $content;
	return;
}

/******************************************************************************/
/******************************************************************************/
/**
 * @param $cursor
 * @return unknown_type
 */
function html_body_start($cursor=0) {
	$content = "";
	$content .= "</head>\n\n";
	if ($cursor) {
		$content .= "<body onload=\"placeCursor()\">\n";
	} else {
		$content .= "<body>\n";
	}

	//flush buffer
	echo $content;
	return;
}

/******************************************************************************/
/******************************************************************************/
/**
 * @param $page_type
 * @return unknown_type
 */
function html_body_top($page_type=0) {
	global $bottom_text;

	$content = "";
	//create the main table
	$content .= "	<!-- start main table -->\n";
	$content .= "	<table width=\"100%\" cellspacing=\"0\" class=\"main\">\n";

	switch ($page_type) {

		case 0: //main window + menu sidebar
			//create the masthead part of the main window
			$content .= "		<tr valign=\"top\">\n";
			$content .= "			<td class=\"masthead\">\n";
			$content .= "				<div style=\"position: relative;\">\n";
			$content .= "					<div style=\"position:absolute; width: 72px; top:1px;left:8px;\">\n";
			$content .= "						<a href=\"javascript:void(0);\" onclick='fb.start({ href: \"projects.php?action=popupAdd\", rev:\"width:665 height:515 infoPos:tc showClose:false disableScroll:true caption:`NEW Project` doAnimations:false\" });'>New Project</a><hr />\n";
			$content .= "						<a href=\"javascript:void(0);\" onclick='fb.start({ href: \"projects.php?action=popupQuickAdd\", rev:\"width:665 height:220 infoPos:tc showClose:false disableScroll:true caption:`NEW Quick To-Do Project` doAnimations:false\" });'>Quick To-Do</a>\n";
			$content .= "					</div>\n";
			$content .= "				</div>\n";
			$content .= "				<table width=\"100%\" cellspacing=\"0\">\n";
			$content .= "					<tr>\n";
			$content .= "						<td style=\"font-weight: bold; font-size: large;\">WFUBMC Project Management</td>\n";
			$content .= "						<td align=\"right\">\n";

			//show username
			$content .= "							<a href=\"index.php\">";
			$content .= sprintf('%s\'s Home', $_SESSION['UID_NAME']);
			$content .= "</a>\n";
			//$content .= "&nbsp;&nbsp;|&nbsp;&nbsp;";
			//$content .= "<a href=\"users.php?action=edit&amp;user_id=".$_SESSION['UID']."\">My Profile</a>";
			if ($_SESSION['ADMIN']) {
				$content .= "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"admin.php?action=admin\">Administration</a>\n";
			}
			$content .= "						</td>\n";
			$content .= "					</tr>\n";
			$content .= "				</table>\n";
			$content .= "			</td>\n";
			$content .= "		</tr>\n";
			//create menu sidebar
			$content .= "		<tr valign=\"top\">\n";
			$content .= "			<td style=\"width: 100%;\" align=\"center\">\n";
			$bottom_text = 1;
			break;

		case 1: //single main window (no menu sidebar)
		case 3: //calendar
			$content .= "		<tr valign=\"top\">\n";
			$content .= "			<td class=\"masthead\">\n";
			if (defined('UID_NAME')) {
				$content .= sprintf('				%s\'s HomePage', $_SESSION['UID_NAME']);
			}
			$content .= "</td></tr>\n";
			//create single window over entire screen
			$content .= "<tr valign=\"top\"><td style=\"width: 100%\" align=\"center\">\n";
			$bottom_text = 2;
			break;

		case 2: //printable screen
			//create single window over paper width
			$content .= "<tr valign=\"top\"><td style=\"width: 576pt\" align=\"center\">\n";
			//don't want bottom text
			$bottom_text = 0;
		case 4:
			$content .= "		<tr valign=\"top\">\n";
			$content .= "			<td style=\"width: 100%; padding:0;\">\n";
	}

	//flush buffer
	echo $content;
	return;
}
/******************************************************************************/
/* Creates the initial window */
/******************************************************************************/
/**
 * @param $title
 * @param $page_type
 * @param $cursor
 * @param $check
 * @param $date
 * @param $calendar
 * @param $redirect_time
 * @return unknown_type
 */
function create_complete_top($title='', $page_type=0, $cursor=0, $check=0, $date=0, $calendar=0, $redirect_time=0) {

	//remove /* and */ in section below to use compressed HTML output:
	//Note: PHP manual recommends use of zlib.output_compression in php.ini instead of ob_gzhandler in here
	//use compressed output (if web browser supports it) _and_ zlib.output_compression is not already enabled
	/*
	 if (!ini_get('zlib.output_compression')) {
	 ob_start("ob_gzhandler");
	 }
	 */
	html_header($redirect_time);
	html_pre_html();
	html_html_start();
	html_head_start($title);
	html_meta($redirect_time);
	html_css($page_type);
	html_javascript();
	html_body_start($cursor);
	html_body_top($page_type);

	return;
}

/******************************************************************************/
/*  Creates a new box */
/******************************************************************************/
/**
 * @param $title
 * @param $content
 * @param $style
 * @param $size
 * @return unknown_type
 */
function new_box($title, $content, $style="boxdata", $size="tablebox") {

	echo "\n<!-- start of ".$title." - box -->\n".
       "<br />\n";
	echo "<table class=\"".$size."\" cellspacing=\"0\">\n".
       "<tr><td class=\"boxhead\">&nbsp;".$title."</td></tr>\n".
       "<tr><td class=\"".$style."\">\n".
	$content."</td></tr>\n".
       "</table>\n".
       "<!-- end -->\n";
	return;
}

/******************************************************************************/
/*  Creates a new tab box */
/******************************************************************************/
/**
 * @param $title
 * @param $content
 * @param $style
 * @param $size
 * @return unknown_type
 */
function new_tab_box($title, $content, $style="boxdata", $size="tablebox") {

	echo  "\n<!-- start of ".$title." - tab box -->\n".
        "<br />\n".
	$content."\n".
        "<!-- end -->\n";
	return;
}

/******************************************************************************/
/* End the left frame and go the the right one */
/******************************************************************************/
/**
 * @return unknown_type
 */
function goto_main() {

	//  echo "\n</td><td align=\"center\">\n";
	echo "\n</td><td>\n";
	return;
}

/******************************************************************************/
/* Finish the page nicely */
/******************************************************************************/
/**
 * @return unknown_type
 */
function create_bottom() {

	global $bottom_text;

	//clean
	$content =  "";

	//end the main table row
	$content .= "</td></tr>\n</table>";

	switch($bottom_text) {
		case 0: //no bottom text
			$align = '';
			break;

		case 1:
			$align = "style=\"text-align: left\"";
			break;

		case 2:
		default:
			$align = "style=\"text-align: center\"";
			break;
	}

	// ENTER HERE ANY BOTTOM SECTION


	//html_javascript(1,1,1,1);

	//end xml parsing
	$content .= "</body>\n</html>\n";
	echo $content;
	return;
}

/******************************************************************************/
/* Finish the tab nicely */
/******************************************************************************/
/**
 * @return unknown_type
 */
function create_tab_bottom() {

	global $bottom_text;

	//clean
	$content = "\n<br />\n";

	switch($bottom_text) {
		case 0: //no bottom text
			$align = '';
			break;

		case 1:
			$align = "style=\"text-align: left\"";
			break;

		case 2:
		default:
			$align = "style=\"text-align: center\"";
			break;
	}

	//shows the logo
	if ($bottom_text) {
		$content .= "<div class=\"bottomtext\" ".$align.">Copyright &copy;&nbsp;Mark Pemberton";
		$content .= "<br /><span onclick='javascript:window.open(\"legal/index.php\",null,\"height=320,width=405,scrollbars=yes,status=no,toolbar=no,menubar=no,location=no\");' style='text-decoration: underline; cursor: pointer; cursor: hand;'>Terms of Use</span><br /></div>";
	}

	//end xml parsing
	$content .= "</body>\n</html>\n";
	echo $content;
	return;
}

?>