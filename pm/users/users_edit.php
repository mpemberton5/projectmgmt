<?php
/* $Id: users_edit.php,v 1.1 2009/04/22 00:05:05 markp Exp $ */

//security check
if (!defined('UID')) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

//secure vars
$user_id = '';
$content = '';

//is an admin everything can be queried otherwise only yourself can be queried
if (ADMIN) {
  //is there a uid ?
  if (!safe_integer($_REQUEST['user_id'])) {
    error('User edit', 'No user_id was specified');
  }
  $user_id = $_REQUEST['user_id'];

  //query for user
  $q = db_query('SELECT * FROM users WHERE id='.$user_id);

} else {
  //user
  $q = db_query('SELECT * FROM users WHERE id='.UID);
  $user_id = UID;
}

//fetch data
if (!($row = db_fetch_array($q , 0))) {
  error('Database result', 'Error in retrieving user-data from database');
}

$content = "";

$content .= "<!-- CSS -->\n";
$content .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/tabcontent.css\" />\n";

$content .= "<!-- JavaScript -->\n";
$content .= "<script type=\"text/javascript\" src=\"js/tabcontent.js\">\n";
$content .= "  /***********************************************\n";
$content .= "  * Tab Content script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)\n";
$content .= "  * This notice MUST stay intact for legal use\n";
$content .= "  * Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code\n";
$content .= "  ***********************************************/\n";
$content .= "</script>\n";

$content .= "<style type=\"text/css\">";
$content .= "PRE.exampletext";
$content .= "{";
$content .= "	background-color:#edf1f3;";
$content .= "	border-style:solid;";
$content .= "	border-width:1px;";
$content .= "	border-color:#9aaab4;";
$content .= "	padding:2pt;";
$content .= "	text-align:left;";
$content .= "	margin-left:6px;";
$content .= "	margin-right:6px;";
$content .= "}";
$content .= "</style>";

$content .= "\n";

$content .= "<form method=\"POST\" name=\"Users\" action=\"users.php\" enctype=\"multipart/form-data\">\n";
$content .= "<fieldset><input type=\"hidden\" name=\"action\" value=\"submit_edit\" />\n";
$content .= "<input type=\"hidden\" name=\"user_id\" value=\"".$user_id."\" />\n";
$content .= "<input type=\"hidden\" name=\"x\" value=\"".$x."\" /></fieldset>\n";

$fullname = trim($row['prefix'] . " " . $row['firstname'] . " " . $row['lastname']);

$content .= "	<div>\n";
$content .= "		<table>\n";
$content .= "			<tr>\n";
$content .= "				<td colspan=\"2\" align=\"left\">\n";
$content .= "\n";
$content .= "					<table>\n";
$content .= "						<tr>\n";
$content .= "							<td align=\"left\"><font size=\"+2\"><b>".$fullname."</b></font></td>\n";
$content .= "						</tr>\n";
$content .= "					</table>\n";
$content .= "\n";
$content .= "				</td>\n";
$content .= "			</tr>\n";
$content .= "			<tr valign=\"top\">\n";
$content .= "				<td colspan=\"2\" align=\"left\">\n";
$content .= "\n";
$content .= "					<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n";
$content .= "						<tr>\n";
$content .= "							<td align=\"left\">\n";
$content .= "\n";

$content .= "								<ul id=\"contacttab\" class=\"shadetabs\">\n";
$content .= "									<li class=\"selected\"><a href=\"#\" rel=\"general\">General</a></li>\n";
$content .= "									<li><a href=\"#\" rel=\"address\">Address</a></li>\n";
$content .= "									<li><a href=\"#\" rel=\"notes\">Notes</a></li>\n";
$content .= "									<li><a href=\"#\" rel=\"security\">Security</a></li>\n";
$content .= "									<li><a href=\"#\" rel=\"mls\">MLS</a></li>\n";
if (ADMIN) {
	$content .= "									<li><a href=\"#\" rel=\"admin\">Admin</a></li>\n";
}
$content .= "								</ul>\n";

$content .= "								<div class=\"tabcontentstyle\">\n";

$content .= "\n";
$content .= "								<!-- START GENERAL -->\n";
$content .= "\n";
$content .= "								<div style=\"display: inline;\" class=\"tabcontent\" id=\"general\">\n";
$content .= "									<div style=\"width: 100%; height: 385px; width: 420px; overflow: auto;\">\n";
$content .= "\n";
$content .= "										<table width=\"100%\">\n";
$content .= "											<tr><td></td></tr>\n";
$content .= "											<tr>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "												<td align=\"left\"><label for=\"prefix\">Title</label></td>\n";

$content .= "												<td align=\"left\"><select name=\"prefix\">\n";
$content .= "													<option value=\"\"".(($row['prefix']=='') ? ' selected' : '')."></option>\n";
$content .= "													<option value=\"Mr.\"".(($row['prefix']=='Mr.') ? ' selected' : '').">Mr.</option>\n";
$content .= "													<option value=\"Mrs.\"".(($row['prefix']=='Mrs.') ? ' selected' : '').">Mrs.</option>\n";
$content .= "													<option value=\"Ms.\"".(($row['prefix']=='Ms.') ? ' selected' : '').">Ms.</option>\n";
$content .= "													<option value=\"Miss\"".(($row['prefix']=='Miss') ? ' selected' : '').">Miss</option>\n";
$content .= "													<option value=\"Dr.\"".(($row['prefix']=='Dr.') ? ' selected' : '').">Dr.</option>\n";
$content .= "												</select></td>\n";

$content .= "												<td>&nbsp;</td>\n";
$content .= "											</tr>\n";
$content .= "											<tr>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "												<td align=\"left\"><label for=\"firstname\">First Name</label></td>\n";
$content .= "												<td align=\"left\"><input name=\"firstname\" value=\"".html_escape($row['firstname'])."\" id=\"firstname\" size=\"45\" maxlength=\"64\" /></td>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "											</tr>\n";
$content .= "											<tr>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "												<td align=\"left\"><label for=\"lastname\">Last Name</label></td>\n";
$content .= "												<td align=\"left\"><input name=\"lastname\" value=\"".html_escape($row['lastname'])."\" id=\"lastname\" size=\"45\" maxlength=\"64\" /></td>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "											</tr>\n";
$content .= "											<tr>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "												<td align=\"left\">&nbsp;</td>\n";
$content .= "												<td align=\"left\">&nbsp;</td>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "											</tr>\n";
$content .= "											<tr>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "												<td align=\"left\"><label for=\"email\">Email</label></td>\n";
$content .= "												<td align=\"left\"><input name=\"email\" value=\"".html_escape($row['email'])."\" id=\"email\" size=\"45\" maxlength=\"64\" /></td>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "											</tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td>&nbsp;</td>\n";
$content .= "                       <td align=\"left\"><label for=\"bus_name\">Company</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"bus_name\" value=\"".html_escape($row['bus_name'])."\" id=\"bus_name\" size=\"45\" maxlength=\"64\" /></td>\n";
$content .= "                       <td>&nbsp;</td>\n";
$content .= "                     </tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td>&nbsp;</td>\n";
$content .= "                       <td align=\"left\"><label for=\"title\">Job Title</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"title\" value=\"".html_escape($row['title'])."\" id=\"title\" size=\"45\" maxlength=\"64\" /></td>\n";
$content .= "                       <td>&nbsp;</td>\n";
$content .= "                     </tr>\n";
$content .= "											<tr>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "												<td align=\"left\"><label for=\"bus_url\">Website:</label></td>\n";
$content .= "												<td align=\"left\"><input name=\"bus_url\" value=\"".html_escape($row['bus_url'])."\" id=\"bus_url\" size=\"45\" maxlength=\"64\" /></td>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "											</tr>\n";

$content .= "											<tr>\n";
$content .= "												<td>&nbsp;</td>\n";
//get all business categories
$q = db_query('SELECT * FROM data_table WHERE type=\'BUSCAT\' ORDER BY seq');


//select business category
$content .= "<td><label for=\"business_category\">Category:&nbsp;&nbsp;</label></td><td><select name=\"business_category\">\n";
for ($i=0; $user_row = @db_fetch_array($q, $i); ++$i) {

	$content .= "<option value=\"".$user_row['id']."\"";

	if ($user_row['id'] == $row['business_category']) {
		$content .= " selected=\"selected\"";
	}
	$content .= ">".$user_row['description']."</option>\n";
}
$content .= "</select></td>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "											</tr>\n";

$content .= "										<tr><td colspan=4><pre class=\"exampletext\"><table width=\"100%\">\n";

$content .= "											<tr><td colspan=2><font size=3><b>Phones</b></font></td></tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td align=\"left\"><label for=\"bus_phone\">Office</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"bus_phone\" value=\"".html_escape($row['bus_phone'])."\" id=\"bus_phone\" size=\"16\" maxlength=\"40\" /></td>\n";
$content .= "                       <td align=\"left\"><label for=\"fax\">Fax</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"fax\" value=\"".html_escape($row['fax'])."\" id=\"fax\" size=\"16\" maxlength=\"40\" /></td>\n";
$content .= "                     </tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td align=\"left\"><label for=\"home_phone\">Home</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"home_phone\" value=\"".html_escape($row['home_phone'])."\" id=\"home_phone\" size=\"16\" maxlength=\"40\" /></td>\n";
$content .= "                       <td align=\"left\"><label for=\"pager\">Pager</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"pager\" value=\"".html_escape($row['pager'])."\" id=\"pager\" size=\"16\" maxlength=\"40\" /></td>\n";
$content .= "                     </tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td align=\"left\"><label for=\"cell_phone\">Cell</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"cell_phone\" value=\"".html_escape($row['cell_phone'])."\" id=\"cell_phone\" size=\"16\" maxlength=\"40\" /></td>\n";
$content .= "                     </tr>\n";

$content .= "</table></pre></td></tr>";

$content .= "										</table>\n";
$content .= "\n";
$content .= "									</div>\n";
$content .= "								</div>\n";
$content .= "\n";
$content .= "								<!-- START ADDRESS -->\n";
$content .= "\n";
$content .= "								<div style=\"display: none;\" class=\"tabcontent\" id=\"address\">\n";
$content .= "									<div style=\"width: 100%; height: 385px; width: 420px; overflow: auto;\">\n";
$content .= "\n";
$content .= "										<table width=\"100%\">\n";

$content .= "										<tr><td colspan=4><pre class=\"exampletext\"><table width=\"100%\">\n";

$content .= "											<tr><td><font size=3><b>Personal</b></font></td>\n";
$content .= "											<td align=\"right\"><input type=\"radio\" name=\"address_default\" value=\"P\"";
if ($row['address_default'] <> "B") $content .= " checked";
$content .= ">Default</td></tr>\n";
$content .= "											<tr>\n";
$content .= "												<td align=\"left\"><label for=\"address1\">Address</label></td>\n";
$content .= "												<td align=\"left\"><input name=\"address1\" value=\"".html_escape($row['address1'])."\" id=\"address1\" size=\"45\" maxlength=\"64\" /></td>\n";
$content .= "											</tr>\n";
$content .= "											<tr>\n";
$content .= "												<td align=\"left\"><label for=\"address2\">&nbsp;</label></td>\n";
$content .= "												<td align=\"left\"><input name=\"address2\" value=\"".html_escape($row['address2'])."\" id=\"address2\" size=\"45\" maxlength=\"64\" /></td>\n";
$content .= "											</tr>\n";
$content .= "											<tr>\n";
$content .= "												<td align=\"left\"><label for=\"city\">City</label></td>\n";
$content .= "												<td align=\"left\"><input name=\"city\" value=\"".html_escape($row['city'])."\" id=\"city\" size=\"45\" maxlength=\"64\" /></td>\n";
$content .= "											</tr>\n";
$content .= "											<tr>\n";
$content .= "												<td align=\"left\"><label for=\"state\">State</label></td>\n";
$content .= "												<td align=\"left\">".select_state("state", $row['state']);
$content .= "&nbsp;&nbsp;<label for=\"zip\">Zip:&nbsp;</label>";
$content .= "<input name=\"zip\" value=\"".html_escape($row['zip'])."\" id=\"zip\" size=\"20\" maxlength=\"20\" /></td>\n";
$content .= "											</tr>\n";

$content .= "</table></pre>";

$content .= "										<pre class=\"exampletext\"><table width=\"100%\">\n";

$content .= "											<tr><td><font size=3><b>Business</b></font></td>\n";
$content .= "											<td align=\"right\"><input type=\"radio\" name=\"address_default\" value=\"B\"";
if ($row['address_default'] == "B") $content .= " checked";
$content .= ">Default</td></tr>\n";
$content .= "											<tr>\n";
$content .= "												<td align=\"left\"><label for=\"bus_addr1\">Address</label></td>\n";
$content .= "												<td align=\"left\"><input name=\"bus_addr1\" value=\"".html_escape($row['bus_addr1'])."\" id=\"bus_addr1\" size=\"45\" maxlength=\"64\" /></td>\n";
$content .= "											</tr>\n";
$content .= "											<tr>\n";
$content .= "												<td align=\"left\"><label for=\"bus_addr2\">&nbsp;</label></td>\n";
$content .= "												<td align=\"left\"><input name=\"bus_addr2\" value=\"".html_escape($row['bus_addr2'])."\" id=\"bus_addr2\" size=\"45\" maxlength=\"64\" /></td>\n";
$content .= "											</tr>\n";
$content .= "											<tr>\n";
$content .= "												<td align=\"left\"><label for=\"bus_city\">City</label></td>\n";
$content .= "												<td align=\"left\"><input name=\"bus_city\" value=\"".html_escape($row['bus_city'])."\" id=\"bus_city\" size=\"45\" maxlength=\"64\" /></td>\n";
$content .= "											</tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td align=\"left\"><label for=\"bus_state\">State</label></td>\n";
$content .= "                       <td align=\"left\">".select_state("bus_state", $row['bus_state'])."&nbsp;&nbsp;<label for=\"bus_zip\">Zip:&nbsp;</label><input name=\"bus_zip\" value=\"".html_escape($row['bus_zip'])."\" id=\"bus_zip\" size=\"20\" maxlength=\"20\" /></td>\n";
$content .= "                     </tr>\n";

$content .= "</table></pre></td></tr>";

$content .= "										</table>\n";
$content .= "\n";
$content .= "									</div>\n";
$content .= "								</div>\n";
$content .= "\n";
$content .= "								<!-- START NOTE -->\n";
$content .= "\n";
$content .= "								<div style=\"display: none;\" class=\"tabcontent\" id=\"notes\">\n";
$content .= "									<div style=\"width: 100%; height: 385px; width: 420px; overflow: auto;\">\n";
$content .= "\n";
$content .= "										<table width=\"100%\">\n";
$content .= "											<tr><td></td></tr>\n";
$content .= "											<tr>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "												<td align=\"left\">Notes</td>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "											</tr>\n";
$content .= "											<tr valign=\"top\">\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "												<td align=\"left\"><textarea name=\"notes\" id=\"notes\" rows=\"10\" cols=\"46\">".html_escape($row['notes'])."</textarea></td>\n";
$content .= "												<td>&nbsp;</td>\n";
$content .= "											</tr>\n";
$content .= "										</table>\n";
$content .= "\n";
$content .= "									</div>\n";
$content .= "								</div>\n";
$content .= "\n";
$content .= "								<!-- START SECURITY -->\n";
$content .= "\n";
$content .= "								<div style=\"display: none;\" class=\"tabcontent\" id=\"security\">\n";
$content .= "									<div style=\"width: 100%; height: 385px; width: 420px; overflow: auto;\">\n";
$content .= "\n";
$content .= "										<table width=\"100%\">\n";
$content .= "											<tr><td></td></tr>\n";

$content .= "										<tr><td colspan=4><pre class=\"exampletext\"><table width=\"100%\">\n";
$content .= "											<tr><td colspan=2><font size=3><b>Change Password</b></font>&nbsp;&nbsp;(leave blank if no change)</td></tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td align=\"left\"><label for=\"password1\">Enter New Password</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"password1\" value=\"\" id=\"password1\" size=\"20\" maxlength=\"40\" /></td>\n";
$content .= "                     </tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td align=\"left\"><label for=\"password2\">Re-Enter New Password</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"password2\" value=\"\" id=\"password2\" size=\"20\" maxlength=\"40\" /></td>\n";
$content .= "                     </tr>\n";
$content .= "                     </table></pre></td></tr>";

$content .= "										<tr><td colspan=2><pre class=\"exampletext\"><table width=\"100%\">\n";
$content .= "    <tr>\n";
$content .= "      <td align=\"left\"><label for=\"secret_question_id\">Secret Question:</label></td>\n";
$content .= "      <td align=\"left\"><select name=\"secret_question_id\" id=\"secret_question_id\"><option value=\"\">--- Select One ---</option>\n";

//get all statuses
$q = db_query('SELECT id,description FROM data_table WHERE type="SECQSTN" ORDER BY seq');

//select status
for ($i=0; $table_row = @db_fetch_array($q, $i); ++$i) {
//  $content .= "          <option value=\"".$table_row['id']."\">".$table_row['description']."</option>\n";
	$content .= "<option value=\"".$table_row['id']."\"";

	if ($table_row['id'] == $row['secret_question_id']) {
		$content .= " selected=\"selected\"";
	}
	$content .= ">".$table_row['description']."</option>\n";
}

$content .= "        </select>";
$content .= "      </td>\n";
$content .= "    </tr>\n";

$content .= "    <tr>\n";
$content .= "      <td align=\"left\"><label for=\"secretanswer\">Secret Answer:</label></td>\n";
$content .= "      <td align=\"left\"><input type=\"text\" name=\"secret_question_answer\" id=\"secret_question_answer\" size=\"40\" maxlength=\"50\" value=\"".$row['secret_question_answer']."\"></td>\n";
$content .= "    </tr>\n";
$content .= "</table></pre></td></tr>";



$content .= "										</table>\n";
$content .= "\n";
$content .= "									</div>\n";
$content .= "								</div>\n";

$content .= "\n";
$content .= "								<!-- START MLS INFO -->\n";
$content .= "\n";
$content .= "								<div style=\"display: none;\" class=\"tabcontent\" id=\"mls\">\n";
$content .= "									<div style=\"width: 100%; height: 385px; width: 420px; overflow: auto;\">\n";
$content .= "\n";
$content .= "										<table width=\"100%\">\n";
$content .= "											<tr><td></td></tr>\n";

$content .= "										<tr><td colspan=4><pre class=\"exampletext\"><table width=\"100%\">\n";
$content .= "											<tr><td colspan=2><font size=3><b>MLS Info</b></font></td></tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td align=\"left\"><label for=\"mls_location\">Enter MLS Location</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"mls_location\" value=\"".$row['mls_location']."\" id=\"mls_location\" size=\"20\" maxlength=\"40\" /></td>\n";
$content .= "                     </tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td align=\"left\"><label for=\"mls_id\">MLS ID</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"mls_id\" value=\"".$row['mls_id']."\" id=\"mls_id\" size=\"20\" maxlength=\"40\" /></td>\n";
$content .= "                     </tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td align=\"left\"><label for=\"nar_id\">NAR ID</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"nar_id\" value=\"".$row['mls_id']."\" id=\"nar_id\" size=\"20\" maxlength=\"40\" /></td>\n";
$content .= "                     </tr>\n";
$content .= "                     </table></pre></td></tr>";

$content .= "											</tr>\n";
$content .= "										</table>\n";
$content .= "\n";
$content .= "									</div>\n";
$content .= "								</div>\n";

if (ADMIN) {
$content .= "\n";
$content .= "								<!-- START ADMIN AREA -->\n";
$content .= "\n";
$content .= "								<div style=\"display: none;\" class=\"tabcontent\" id=\"admin\">\n";
$content .= "									<div style=\"width: 100%; height: 385px; width: 420px; overflow: auto;\">\n";
$content .= "\n";
$content .= "										<table width=\"100%\">\n";
$content .= "											<tr><td></td></tr>\n";

$content .= "										<tr><td colspan=4><pre class=\"exampletext\"><table width=\"100%\">\n";
$content .= "											<tr><td colspan=2><font size=3><b>Admin Info</b></font></td></tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td align=\"left\"><label for=\"usergroup_id\">Select User Group</label></td>\n";
$content .= "                       <td align=\"left\"><input name=\"usergroup_id\" value=\"\" id=\"usergroup_id\" size=\"20\" maxlength=\"40\" /></td>\n";
$content .= "                     </tr>\n";
$content .= "                     <tr>\n";

$content .= "                       <td align=\"left\"><label for=\"security_level\">Security Level</label></td>\n";
//$content .= "                       <td align=\"left\"><input name=\"security_level\" value=\"\" id=\"security_level\" size=\"20\" maxlength=\"40\" /></td>\n";


  $content .= "<td align=\"left\"><select name=\"security_level\" id=\"security_level\">\n";
  $content .= "<option value=\"0\">Disabled</option>\n";

  $r = db_query('SELECT id, description FROM data_table WHERE type="SECLVL" ORDER BY id');

  for ($i=0; $level_row = @db_fetch_array($r, $i); ++$i) {

    $content .= "<option value=\"".$level_row['id']."\"";

    if ($row['security_level'] == $level_row['id']) {
      $content .= " selected=\"selected\" >";
    } else {
      $content .= ">";
    }
    $content .= $level_row['description']."</option>\n";
  }
  $content .= "</select></td>\n";





$content .= "                     </tr>\n";
$content .= "                     <tr>\n";
$content .= "                       <td align=\"left\"><label for=\"billing_status\">Billing Status</label></td>\n";
//$content .= "                       <td align=\"left\"><input name=\"billing_status\" value=\"\" id=\"billing_status\" size=\"20\" maxlength=\"40\" /></td>\n";


  $content .= "<td align=\"left\"><select name=\"billing_status\" id=\"billing_status\">\n";
  $content .= "<option value=\"0\">Disabled</option>\n";

  $r = db_query('SELECT id, description FROM data_table WHERE type="BILLSTAT" ORDER BY id');

  for ($i=0; $level_row = @db_fetch_array($r, $i); ++$i) {

    $content .= "<option value=\"".$level_row['id']."\"";

    if ($row['billing_status'] == $level_row['id']) {
      $content .= " selected=\"selected\" >";
    } else {
      $content .= ">";
    }
    $content .= $level_row['description']."</option>\n";
  }
  $content .= "</select></td>\n";


$content .= "                     </tr>\n";
$content .= "                     </table></pre></td></tr>";

$content .= "											</tr>\n";
$content .= "										</table>\n";
$content .= "\n";
$content .= "									</div>\n";
$content .= "								</div>\n";
/**** END ADMIN SECTION ****/
}

$content .= "								<script type=\"text/javascript\">\n";
$content .= "									initializetabcontent(\"contacttab\")\n";
$content .= "								</script>\n";

$content .= "								</div>\n";

$content .= "							</td>\n";
$content .= "						</tr>\n";
$content .= "					</table>\n";
$content .= "\n";
$content .= "				</td>\n";
$content .= "			</tr>\n";
$content .= "			<tr>\n";
$content .= "				<td align=\"left\">\n";
$content .= "\n";
$content .= "					<table>\n";
$content .= "						<tr>\n";

$content .= "							<td><input type=\"submit\" name=\"Save\" value=\"Save\" id=\"Save\" accesskey=\"s\" /></td>\n";
$content .= "							<td><input type=\"submit\" name=\"Cancel\" value=\"Cancel\" id=\"Cancel\" onclick=\"if(0) return true; history.back(); return false;\" /></td>\n";
$content .= "						</tr>\n";
$content .= "					</table>\n";
$content .= "\n";
$content .= "				</td>\n";

$content .= "			</tr>\n";
$content .= "		</table>\n";
$content .= "\n";
$content .= "	</div>\n";
$content .= "</form>\n";

new_box('User Information', $content);
?>

