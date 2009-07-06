<?php
/* $Id$ */

//security check
if (!defined('UID')) {
  die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

//admins only
if (!ADMIN) {
  error('Unauthorized access', 'This function is for admins only.');
}

$content =
           "<form method=\"post\" action=\"users.php\">\n".
             "<fieldset><input type=\"hidden\" name=\"action\" value=\"submit_insert\" />\n".
             "<input type=\"hidden\" name=\"x\" value=\"".$x."\" /></fieldset>\n".
             "<table class=\"celldata\">\n".
               "<tr><td>Login Name:</td><td><input id=\"login_name\" type=\"text\" name=\"login_name\" size=\"30\" /></td></tr>\n".
               "<tr><td>Full Name:</td><td><input type=\"text\" name=\"fullname\" size=\"30\" /></td></tr>\n".
               "<tr><td>Password:</td><td><input type=\"text\" name=\"password\" size=\"30\" /></td></tr>\n".
               "<tr><td>Email Address:</td><td><input type=\"text\" name=\"email\" size=\"30\" /></td></tr>\n".

               "<tr><td>Security Level:</td><td><input type=\"text\" name=\"security_level\" size=\"30\" /></td></tr>\n".
               "<tr><td>MLS Location:</td><td><input type=\"text\" name=\"mls_location\" size=\"30\" /></td></tr>\n".
               "<tr><td>MLS ID:</td><td><input type=\"text\" name=\"mls_id\" size=\"30\" /></td></tr>\n".
               "<tr><td>Address 1:</td><td><input type=\"text\" name=\"address1\" size=\"30\" /></td></tr>\n".
               "<tr><td>Address 2:</td><td><input type=\"text\" name=\"address2\" size=\"30\" /></td></tr>\n".
               "<tr><td>City:</td><td><input type=\"text\" name=\"city\" size=\"30\" /></td></tr>\n".
               "<tr><td>State:</td><td><input type=\"text\" name=\"state\" size=\"30\" /></td></tr>\n".
               "<tr><td>Zip:</td><td><input type=\"text\" name=\"zip\" size=\"30\" /></td></tr>\n".
               "<tr><td>Office Phone:</td><td><input type=\"text\" name=\"office_phone\" size=\"30\" /></td></tr>\n".
               "<tr><td>Cell Phone:</td><td><input type=\"text\" name=\"cell_phone\" size=\"30\" /></td></tr>\n".
               "<tr><td>Home Phone:</td><td><input type=\"text\" name=\"home_phone\" size=\"30\" /></td></tr>\n".
               "<tr><td>Pager:</td><td><input type=\"text\" name=\"pager\" size=\"30\" /></td></tr>\n".
               "<tr><td>Fax:</td><td><input type=\"text\" name=\"fax\" size=\"30\" /></td></tr>\n".
               "<tr><td>&nbsp;</td></tr>\n";

$content .=    "</table>\n".
               "<p><input type=\"submit\" value=\"Add\" /></p>\n".
               "</form>";

new_box('User Information', $content);

?>
