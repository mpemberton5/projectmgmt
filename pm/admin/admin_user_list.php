<?php
/* $Id: admin_user_list.php,v 1.1 2009/06/08 21:13:03 markp Exp $ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

//only for admins
if(!$_SESSION['ADMIN']) {
	error('Not permitted', 'This function is for admins only');
}

$content = '';

/************************************/
/* sortDir= ASC DEC */
/* sortBy= field name */
/* POS= start at 0, increment by 10 */
/************************************/

/*** Paging information
http://www.freephpwebhost.org/php_site/ver1/index.php
****/

if (isset($_GET['clearSettings'])) {
    $_SESSION['nameFilter'] = "";
    $_SESSION['sortBy'] = "fullname";
    $_SESSION['sortDir'] = "ASC";
    $_SESSION['ContactDispItems'] = "10";
}

/******* GET FIELDS *******/
//NAME FILTER
if (isset($_GET['nameFilter'])) {
    $_SESSION['nameFilter'] = @safe_data($_GET['nameFilter']);
} else {
    if (!isset($_SESSION['nameFilter'])) {
        $_SESSION['nameFilter'] = "";
    }
}
//SORT BY
if (isset($_GET['sortBy'])) {
    if ($_SESSION['sortBy'] == @safe_data($_GET['sortBy'])) {
        if ($_SESSION['sortDir'] == "DESC") {
            $_SESSION['sortDir'] = "ASC";
        } else {
            $_SESSION['sortDir'] = "DESC";
        }
    } else {
        $_SESSION['sortDir'] = "ASC";
    }
    $_SESSION['sortBy'] = @safe_data($_GET['sortBy']);
} else {
    if (!isset($_SESSION['sortBy'])) {
        $_SESSION['sortBy'] = "fullname";
    }
    $_SESSION['sortDir'] = "ASC";
}
//DISPLAY ITEMS
$set_start_item = 1;
if (isset($_POST['ContactDispItems'])) {
    if ($_SESSION['ContactDispItems'] != $_POST['ContactDispItems']) {
        $startItem = 0;
        $set_start_item = 0;
    }
    $_SESSION['ContactDispItems'] = $_POST['ContactDispItems'];
} else if (!isset($_SESSION['ContactDispItems'])) {
    $_SESSION['ContactDispItems'] = 10;
}
//START ITEM
if (isset($_GET['startItem']) && $set_start_item == 1) {
    $startItem = $_GET['startItem'];
} else {
    $startItem = 0;
}
/******* GET FIELDS *******/

/******* QUERY ******/
//get contacts
$query = "SELECT * FROM employees WHERE active=1";

// NAME FILTER
if ($_SESSION['nameFilter'] != "") {
    $query .= " AND LastName LIKE '" . $_SESSION['nameFilter'] . "%'";
}
// ORDER BY
if ($_SESSION['sortBy'] == "fullname") {
    $query .= " ORDER BY LastName " . $_SESSION['sortDir'] . ",FirstName " . $_SESSION['sortDir'];
} else if ($_SESSION['sortBy'] == "dept") {
    $query .= " ORDER BY Department_ID " . $_SESSION['sortDir'];
} else if ($_SESSION['sortBy'] == "phone") {
    $query .= " ORDER BY Phone " . $_SESSION['sortDir'];
} else if ($_SESSION['sortBy'] == "login") {
    $query .= " ORDER BY MedCtrLogin " . $_SESSION['sortDir'];
} else if ($_SESSION['sortBy'] == "email") {
    $query .= " ORDER BY EMail " . $_SESSION['sortDir'];
} else {
    $query .= " ORDER BY LastName " . $_SESSION['sortDir'] . ",FirstName " . $_SESSION['sortDir'];
}
// LIMIT #
$query .= " LIMIT " . $startItem . "," . $_SESSION['ContactDispItems'];

// DEBUG
//echo $query;

// RUN QUERY
$q = db_query($query);

// FIND TOTAL ROWS OF QUERY
$tot_q = "SELECT count(*) FROM employees WHERE active = 1";
if ($_SESSION['nameFilter'] != "") {
    $tot_q .= " AND LastName LIKE '" . $_SESSION['nameFilter'] . "%'";
}
$tot_q .= " LIMIT 1";
$total_records = db_result(db_query($tot_q),0,0);
/******* QUERY ******/

$content = "";

$content .= "<style type=\"text/css\">\n";
$content .= "    .row_on { color: #000000; background-color: #F1F1F1; }\n";
$content .= "    .row_off { color: #000000; background-color: #ffffff; }\n";
$content .= "    .th { color: #000000; background-color: #D3DCE3; }\n";
$content .= "    .narrow_column { width: 1%; white-space: nowrap; }\n";
$content .= "    .letter_box, .letter_box_active { border: 1px #D3DCE3 solid; text-align: center; cursor: pointer; }\n";
$content .= "    .letter_box_active { font-weight: bold; border: 1px black solid; background-color: #E8F0F0; }\n";
$content .= "    .letter_box:hover { border: 1px black solid; }\n";
$content .= "</style>\n";

//STYLE FOR TABLE
$content .= "<style type=\"text/css\">";
$content .= ".cont_div table { border-collapse: collapse; border: 2px solid #3f7c5f; font: normal 80%/140% arial, verdana, helvetica, sans-serif; color: #000; background: #fff; }";
$content .= ".cont_div td, th { border: 1px solid #e0e0e0; }";
$content .= ".cont_div thead th { border: 1px solid #e0e0e0; text-align: left; font-size: 1em; font-weight: bold; background: #c6d7cf; }";
$content .= ".cont_div tbody td a { background: transparent; color: #00c; text-decoration: underline; }";
$content .= ".cont_div tbody th a { background: transparent; color: #3f7c5f; font-size: 1.3em; text-decoration: underline; font-weight: bold; }";
$content .= ".cont_div tbody th a:visited { color: #b98b00; }";
$content .= ".cont_div tbody th, .mydiv tbody td { vertical-align: middle; text-align: left; }";
$content .= ".cont_div tbody tr:hover { background: #d8d9cc; }";
$content .= ".cont_div tbody tr { background:#feffee; hover:expression( this.onmouseover=new Function(\"this.style.background='#d8d9cc';\"), this.onmouseout=new Function(\"this.style.background='#feffee';\")); }";
$content .= "</style>";

$content .= "<div>\n";

//$content .= "<span class=\"textlink\">[<a href=\"".BASE_URL."admin.php?action=Useradd\">Add User</a>]</span><p>\n";

$content .= "   <form method=\"POST\">\n";
$content .= " <table bgcolor=\"#d3dce3\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" cols=\"5\" width=\"100%\">\n";
$content .= "  <tbody><tr>\n";

//$content .= "<td align=\"left\" width=\"2%\">\n";
//
//$content .= "    <table bgcolor=\"\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
//$content .= "    <tbody><tr>\n";

if ($startItem > 0) {
    $new_url = "admin.php?action=manage&amp;nameFilter=".$_SESSION['nameFilter']."&amp;startItem=" . ($startItem - $_SESSION['ContactDispItems']);
    $content .= "        <td align=\"left\"><a href=\"".$new_url."\"><img src=\"images/new/left.png\" title=\"Previous page\" border=\"0\" hspace=\"2\"></a></td>\n";
} else {
    $content .= "        <td align=\"left\"><img src=\"images/new/left-grey.png\" title=\"Previous page\" border=\"0\" hspace=\"2\"></td>\n";
}

//$content .= "    </tr>\n";
//$content .= "    </tbody></table>\n";
//
//$content .= "</td>\n";
//$content .= "   <td align=\"center\" bgcolor=\"#d3dce3\" valign=\"middle\" width=\"92%\">\n";
//
//$content .= "    <table bgcolor=\"#d3dce3\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
//$content .= "      <tbody><tr>\n";
//$content .= "              <td>\n";
//$content .= "        Items to Display:&nbsp;<select name=\"ContactDispItems\" onchange=\"this.form.submit();\">\n";
//
//$array = array('5', '10', '20', '50', '100');
//foreach ($array as $key => $var) {
//    $content .= "<option value=\"" . $var . "\"";
//    if ($var == $_SESSION['ContactDispItems']) {
//        $content .= " selected=\"selected\"";
//    }
//    $content .= ">" . $var . "</option>\n";
//}
//
//$content .= "        </select>\n";
//
//$content .= "        </td>\n";
//$content .= "      </tr>\n";
//$content .= "    </tbody></table>\n";
//$content .= "   </td>\n";
//$content .= "<td align=\"right\" width=\"2%\">\n";
//
//$content .= "    <table bgcolor=\"\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
//
//$content .= "    <tbody><tr>\n";

if (($startItem+$_SESSION['ContactDispItems']) < $total_records) {
    $new_url = "admin.php?action=manage&amp;nameFilter=".$_SESSION['nameFilter']."&amp;startItem=" . ($startItem + $_SESSION['ContactDispItems']);
    $content .= "        <td align=\"right\"><a href=\"".$new_url."\"><img src=\"images/new/right.png\" title=\"Next page\" border=\"0\" hspace=\"2\"></a></td>\n";
} else {
    $content .= "        <td align=\"right\"><img src=\"images/new/right-grey.png\" title=\"Next page\" border=\"0\" hspace=\"2\"></td>\n";
}

//$content .= "    </tr>\n";
//$content .= "    </tbody></table>\n";
//$content .= "</td>\n";

$content .= "  </tr>\n";
$content .= " </tbody></table>\n";


$content .= "<br />\n";

// START OF FILTER LETTERS -------------------------
$content .= "<table border=\"0\" width=\"100%\">\n";
$content .= "<tbody><tr>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=';\">ALL</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='a') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=a';\">A</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='b') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=b';\">B</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='c') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=c';\">C</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='d') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=d';\">D</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='e') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=e';\">E</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='f') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=f';\">F</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='g') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=g';\">G</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='h') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=h';\">H</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='i') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=i';\">I</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='j') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=j';\">J</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='k') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=k';\">K</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='l') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=l';\">L</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='m') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=m';\">M</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='n') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=n';\">N</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='o') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=o';\">O</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='p') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=p';\">P</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='q') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=q';\">Q</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='r') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=r';\">R</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='s') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=s';\">S</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='t') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=t';\">T</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='u') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=u';\">U</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='v') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=v';\">V</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='w') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=w';\">W</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='x') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=x';\">X</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='y') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=y';\">Y</td>\n";
$content .= "<td class=\"letter_box".(($_SESSION['nameFilter']=='z') ? '_active' : '')."\" onclick=\"location.href='admin.php?action=manage&amp;nameFilter=z';\">Z</td>\n";
$content .= "</tr>\n";
$content .= "</tbody></table>\n";
// END OF FILTER LETTERS -------------------------

$content .= "<br>\n";

$content .= "<div class=\"cont_div\">";
$content .= "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">\n";

$content .= "<thead><tr class=\"th\">  <td height=\"21\">\n";
$content .= "    <font face=\"Arial, Helvetica, sans-serif\" size=\"1px\"><a href=\"admin.php?action=manage&amp;sortBy=fname\">Full Name";
if ($_SESSION['sortBy'] == "fullname") {
    if ($_SESSION['sortDir'] == "DESC") {
        $content .= "&nbsp;&nbsp;<img src=\"images/arrow_down.gif\">";
    } else {
        $content .= "&nbsp;&nbsp;<img src=\"images/arrow_up.gif\">";
    }
}
$content .= "</a></font>\n";
$content .= "  </td>\n";
$content .= "  <td height=\"21\">\n";
$content .= "    <font face=\"Arial, Helvetica, sans-serif\" size=\"1px\"><a href=\"admin.php?action=manage&amp;sortBy=dept\">Department";
if ($_SESSION['sortBy'] == "dept") {
    if ($_SESSION['sortDir'] == "DESC") {
        $content .= "&nbsp;&nbsp;<img src=\"images/arrow_down.gif\">";
    } else {
        $content .= "&nbsp;&nbsp;<img src=\"images/arrow_up.gif\">";
    }
}
$content .= "</a></font>\n";
$content .= "  </td>\n";
$content .= "  <td height=\"21\">\n";

$content .= "    <font face=\"Arial, Helvetica, sans-serif\" size=\"1px\"><a href=\"admin.php?action=manage&amp;sortBy=login\">MEDCTR Login";
if ($_SESSION['sortBy'] == "login") {
    if ($_SESSION['sortDir'] == "DESC") {
        $content .= "&nbsp;&nbsp;<img src=\"images/arrow_down.gif\">";
    } else {
        $content .= "&nbsp;&nbsp;<img src=\"images/arrow_up.gif\">";
    }
}
$content .= "</a></font>\n";
$content .= "  </td>\n";

$content .= "  <td height=\"21\">\n";
$content .= "    <font face=\"Arial, Helvetica, sans-serif\" size=\"1px\"><a href=\"admin.php?action=manage&amp;sortBy=phone\">Phone";
if ($_SESSION['sortBy'] == "phone") {
    if ($_SESSION['sortDir'] == "DESC") {
        $content .= "&nbsp;&nbsp;<img src=\"images/arrow_down.gif\">";
    } else {
        $content .= "&nbsp;&nbsp;<img src=\"images/arrow_up.gif\">";
    }
}
$content .= "</a></font>\n";
$content .= "  </td>\n";

$content .= "  <td height=\"21\">\n";
$content .= "    <font face=\"Arial, Helvetica, sans-serif\" size=\"1px\"><a href=\"admin.php?action=manage&amp;sortBy=email\">E-Mail";
if ($_SESSION['sortBy'] == "email") {
    if ($_SESSION['sortDir'] == "DESC") {
        $content .= "&nbsp;&nbsp;<img src=\"images/arrow_down.gif\">";
    } else {
        $content .= "&nbsp;&nbsp;<img src=\"images/arrow_up.gif\">";
    }
}
$content .= "</a></font>\n";
$content .= "  </td>\n";

$content .= "  <td height=\"21\" width=\"3%\" align=\"center\">\n";
$content .= "    <font face=\"Arial, Helvetica, sans-serif\" size=\"1px\">Delete</font>\n";
$content .= "  </td>\n";
$content .= "</tr>\n";
$content .= "</tbody></thead>";

// START - CONTACT
for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {

    $cname = substr($row['LastName'], 0, 30).", ".substr($row['FirstName'], 0, 15);

    $content .= "  <tr bgcolor=\"#dddddd\">\n";
    $content .= "   <td valign=\"top\"><font face=\"Arial, Helvetica, san-serif\" size=\"2\"><a href=\"admin.php?action=edit&amp;contact_id=".$row['employee_ID']."\">".$cname."</a></font></td>\n";
    $content .= "   <td valign=\"top\"><font face=\"Arial, Helvetica, san-serif\" size=\"2\">".$row['Department_ID']."&nbsp;</font></td>\n";
    $content .= "   <td valign=\"top\"><font face=\"Arial, Helvetica, san-serif\" size=\"2\">".$row['MedCtrLogin']."&nbsp;</font></td>\n";
    $content .= "   <td valign=\"top\"><font face=\"Arial, Helvetica, san-serif\" size=\"2\">".$row['Phone']."&nbsp;</font></td>\n";
    $content .= "   <td valign=\"top\"><font face=\"Arial, Helvetica, san-serif\" size=\"2\">".$row['EMail']."&nbsp;</font></td>\n";
    $content .= "   <td >\n";
    //$content .= "     <a href=\"FILE.php\" onclick=\"window.open(this,this.target,'dependent=yes,width=850,height=440,location=no,menubar=no,toolbar=no,scrollbars=yes,status=yes'); return false;\"><img src=\"images/new/edit.png\" title=\"Edit\" border=\"0\"></a><a href=\"FILE.php\" onclick=\"return confirm('Are you sure you want to delete this entry ?');\"><img src=\"images/delete.jpg\" title=\"Delete\" border=\"0\"></a><input name=\"select[1]\" type=\"checkbox\"></td>\n";
    $content .= "     <center><a href=\"admin.php?action=submit_delete&amp;contact_id=".$row['employee_ID']."\" onclick=\"return confirm('Are you sure you want to delete this entry ?');\"><img src=\"images/icon-delete.gif\" title=\"Delete\" border=\"0\"></a></center></td>\n";
    $content .= "  </tr>\n";
}

// END - CONTACT
db_free_result($q);

$content .= " </tbody></table>\n";
//$content .= "</div>";

$content .= "</form>\n";
$content .= "\n </div>\n";

//show it
echo $content;

?>