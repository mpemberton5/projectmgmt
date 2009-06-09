<?php
/* $Id: index.php,v 1.12 2009/06/08 21:13:04 markp Exp $ */

require_once('path.php');
require_once(BASE.'path_config.php');
require_once(BASE_CONFIG.'config.php');
include_once(BASE.'includes/common.php');
include_once(BASE.'includes/security.php');
include_once(BASE.'includes/screen.php');

//secure variables
$content = "";

/* USER */
if ($_SESSION['MGMT']==0) {
	header('Location: '.BASE_URL.'projects.php?action=list');
	die();
	/* MGR AND ABOVE */
} elseif ($_SESSION['MGMT']==1) {
	//CSS LINK
	$content .= "<!-- CSS -->\n";
	$content .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".BASE_SUB."css/announcements.css\" />\n";

	$content .= "<script type='text/javascript' src='/public/charts/version-2-Jorm-2/js/swfobject.js'></script>\n";
	$content .= "<script type='text/javascript'>\n";
	$content .= "	swfobject.embedSWF('/public/charts/version-2-Jorm-2/open-flash-chart.swf', 'my_chart','350', '350', '9.0.0', 'expressInstall.swf',{'data-file':'charts.php?action=manager_chart1'} );\n";
	$content .= "	swfobject.embedSWF('/public/charts/version-2-Jorm-2/open-flash-chart.swf', 'pie_chart','350', '350', '9.0.0', 'expressInstall.swf',{'data-file':'charts.php?action=overall_status','loading':'Overall Project Status Pie is loading ...'} );\n";
	$content .= "	function pie_slice_clicked( index ) {\n";
	$content .= "		window.location='projects.php?action=list&uid=' + index;\n";
	$content .= "	}\n";
	$content .= "</script>\n";

	/*******************/
	/* OVERVIEW CHARTS */
	/*******************/
	$content .= "    <table class=\"tablebox\" cellpadding=\"0\" cellspacing=\"0\">\n";
	$content .= "      <tr class=\"panelHeading\">\n";
	$content .= "        <td><img src=\"".BASE_SUB."images/icon-announcements-big.gif\" width=\"24\" height=\"24\" vspace=\"4\" hspace=\"4\" border=\"0\" alt=\"\"> </td>\n";
	$content .= "        <td width=\"100%\">Management Dashboard</td>\n";
	$content .= "      </tr>\n";
	$content .= "      <tr>\n";
	$content .= "        <td class=\"panel\" colspan=\"3\"><div id=\"divGroupAnnouncements\" style=\"padding: 4px\">\n";
	$content .= "          <table>\n";
	$content .= "             <tr valign=\"top\">\n";
	$content .= "               <td width=\"100%\">\n";
	$content .= "					<div id='my_chart'></div>\n";
	$content .= "					<div id='pie_chart'></div>\n";
	$content .= "               </td>\n";
	$content .= "             </tr>\n";
	$content .= "           </table>\n";
	$content .= "         </div>\n";
	$content .= "       </td>\n";
	$content .= "     </tr>\n";
	$content .= "   </table>\n";

	/* TESTING */
} elseif ($_SESSION['MGMT']==2) {

	// http://www.stanlemon.net/projects/jgrowl.html

	// BUILD PORTLETS - START
	$portlet_content = "";
	$portlet_js = "";
	// READ user_prefs TO GET COLUMN PREFERENCES
	$SQL = "SELECT * FROM user_prefs WHERE user_id=".$_SESSION['UID']." AND pref_type='mgmtDesktop'";
	$q = db_query($SQL);

	if (db_numrows($q) > 0) {
		$row = @db_fetch_array($q, 0);
		$col_array = array($row['value1'],$row['value2'],$row['value3']);
	} else {
		// default portlet view
		$col_array = array("cht_pbe,cht_to","cht_pso","cht_pbs");
	}
	db_free_result($q);

	$portlet_content .= "<div id=\"container\">\n";
	$colctr = 0;
	foreach($col_array as $var) {
		$portlet_content .= "	<div class=\"column\" id=\"col".$colctr."\">\n";
		$tmpArr1 = split(",",$var);
		foreach ($tmpArr1 as &$value) {
			if (strlen($value)>0) {
				$portlet_content .= "		<div class=\"portlet\" id=\"".$value."\">\n";
				$portlet_content .= "			<div class=\"portlet-header\">Projects by Employee</div>\n";
				$portlet_content .= "			<div class=\"portlet-content\"><div id='".$value."_div'></div></div>\n";
				$portlet_content .= "		</div>\n";
				$portlet_js .= "	swfobject.embedSWF('/public/charts/version-2-Jorm-2/open-flash-chart.swf', '".$value."_div','350', '350', '9.0.0', 'expressInstall.swf',{'data-file':'charts.php?action=".$value."'},{wmode:'transparent'});\n";
			}
		}
		$portlet_content .= "	</div>\n";
		$colctr++;
	}
	$portlet_content .= "</div>\n";
	// BUILD PORTLETS - END


	$content .= "<link rel='stylesheet' type='text/css' href='css/portlets.css' />\n";
	$content .= "<script type='text/javascript' src='/public/charts/version-2-Jorm-2/js/swfobject.js'></script>\n";
	$content .= "<script type='text/javascript'>\n";
	$content .= $portlet_js;
	$content .= "	function pie_slice_clicked( index ) {\n";
	$content .= "		window.location='projects.php?action=list&uid=' + index;\n";
	$content .= "	}\n";
	$content .= "</script>\n";

	$content .= "<script type='text/javascript'>\n";

	$content .= "//set the list selector\n";
	$content .= "var setSelector = '.column';\n";
	$content .= "// set the cookie name\n";
	$content .= "var setCookieName = 'listOrder';\n";
	$content .= "// set the cookie expiry time (days):\n";
	$content .= "var setCookieExpiry = 7;\n";

	$content .= "function saveOrder(arrVal) {\n";
	$content .= "	parameters = 'action=savePos&user_id=' + ".$_SESSION['UID'].";\n";
	$content .= "	var arrLen=arrVal.length;\n";
	$content .= "	for (var i=0; i<arrLen; i++){\n";
	$content .= "		parameters = parameters + '&col' + i + '=' + arrVal[i];\n";
	$content .= "	}\n";
	//	$content .= "	alert(parameters);\n";
	$content .= "	$.ajax({\n";
	$content .= "		type: 'POST',\n";
	$content .= "		url: 'charts.php',\n";
	$content .= "		data: parameters,\n";
	$content .= "		success: function(msg){\n";
	$content .= "			$.jGrowl(\"Position Saved\");\n";
	$content .= "		},\n";
	$content .= "		error: function(msg){\n";
	$content .= "			alert( 'Error saving Positions: ' + msg );\n";
	$content .= "		}\n";
	$content .= "	});\n";

	$content .= "}\n";

	$content .= "$(document).ready(function() {\n";
	$content .= "	$(setSelector).sortable({\n";
	$content .= "		connectWith: setSelector,\n";
	$content .= "        stop: function() {\n";
	$content .= "			var myColumns=new Array();\n";
	$content .= "			var ctrColumns=0;\n";
	$content .= "			$(setSelector).each(function() {\n";
	$content .= "				myColumns[ctrColumns]=$(this).sortable('toArray');\n";
	$content .= "				ctrColumns++;\n";
	$content .= "			});\n";
	$content .= "			saveOrder(myColumns);\n";
	$content .= "		}\n";
	$content .= "	});\n";

	$content .= "	$('.portlet').addClass('ui-widget ui-widget-content ui-helper-clearfix ui-corner-all')\n";
	$content .= "		.find('.portlet-header')\n";
	$content .= "			.addClass('ui-widget-header ui-corner-all')\n";
	$content .= "			.prepend('<span class=\"ui-icon ui-icon-plusthick\"></span>')\n";
	$content .= "			.end()\n";
	$content .= "		.find('.portlet-content');\n";

	$content .= "	$('.portlet-header .ui-icon').click(function() {\n";
	$content .= "		$(this).toggleClass('ui-icon-minusthick');\n";
	$content .= "		$(this).parents('.portlet:first').find('.portlet-content').toggle();\n";
	$content .= "	});\n";

	$content .= "	$(setSelector).disableSelection();\n";
	$content .= "});\n";
	$content .= "</script>\n";

	$content .= $portlet_content;

}

create_complete_top();
echo $content;
create_bottom();

?>