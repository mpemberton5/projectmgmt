<?php
/* $Id$ */

//security check
if (!defined('UID')) {
  die('Direct file access not permitted');
}

function get_document_list($project_id,$task_id) {

  global $x;

	$content = "";

  $mod_id = db_result(db_query('SELECT id FROM data_table WHERE type="MODULES" AND description="Documents"'),0,0);
  $side = $_SESSION['side'];
  $sec_lvl = $_SESSION['sec_lvl_id'];

	if ($task_id <> 0) {
		//get the files from this task
/*
		$q = db_query('SELECT *,
 (select permissions from perms where module_id='.$mod_id.' and rec_id=files.id and side="'.$side.'" and sec_lvl_id='.$sec_lvl.') as perm_val
 FROM files WHERE project_id='.$project_id.' AND task_id='.$task_id.' ORDER BY uploaded_date DESC');
*/

//query to get the children for this project_id
$q = db_query('SELECT f.*,
              p.permissions as perm_val
               FROM files f
              LEFT JOIN perms p ON
                p.module_id='.$mod_id.' AND
                p.rec_id=f.id AND
                p.side="'.$side.'" AND
                p.sec_lvl_id='.$sec_lvl.'
               WHERE f.project_id='.$project_id.' AND
								f.task_id='.$task_id.'
               ORDER BY f.uploaded_date DESC');



	} else {
		//get all files for project
/*
		$q = db_query('SELECT *,
 (select permissions from perms where module_id='.$mod_id.' and rec_id=files.id and side="'.$side.'" and sec_lvl_id='.$sec_lvl.') as perm_val
 FROM files WHERE project_id='.$project_id.' ORDER BY uploaded_date DESC');
*/

//query to get the children for this project_id
$q = db_query('SELECT f.*,
              p.permissions as perm_val
               FROM files f
              LEFT JOIN perms p ON
                p.module_id='.$mod_id.' AND
                p.rec_id=f.id AND
                p.side="'.$side.'" AND
                p.sec_lvl_id='.$sec_lvl.'
               WHERE f.project_id='.$project_id.'
               ORDER BY f.uploaded_date DESC');
	}

	//check if there are project
	if (db_numrows($q) < 1) {
		return "";
	}

	//STYLE FOR TABLE
	$content .= "<style type=\"text/css\">";
	$content .= ".mydiv table { border-collapse: collapse; border: 2px solid #3f7c5f; font: normal 80%/140% arial, verdana, helvetica, sans-serif; color: #000; background: #fff; }";
	$content .= ".mydiv td, th { border: 1px solid #e0e0e0; padding: 0.5em; }";
	$content .= ".mydiv thead th { border: 1px solid #e0e0e0; text-align: left; font-size: 1em; font-weight: bold; background: #c6d7cf; }";
	$content .= ".mydiv tbody td a { background: transparent; color: #00c; text-decoration: underline; }";
	$content .= ".mydiv tbody th a { background: transparent; color: #3f7c5f; font-size: 1.3em; text-decoration: underline; font-weight: bold; }";
	$content .= ".mydiv tbody th a:visited { color: #b98b00; }";
	$content .= ".mydiv tbody th, tbody td { vertical-align: top; text-align: left; }";
	$content .= ".mydiv tbody tr:hover { background: #d8d9cc; }";
	$content .= ".mydiv tbody tr { background:#feffee; hover:expression( this.onmouseover=new Function(\"this.style.background='#d8d9cc';\"), this.onmouseout=new Function(\"this.style.background='#feffee';\")); }";
	$content .= "</style>";

	$content .= "<p>";

	//setup content table
	$content .= "<div class=\"mydiv\">";
	$content .= "  <table>\n";
	$content .= "    <thead>";
	$content .= "      <tr>";
	$content .= "        <th scope=\"col\">File</th>";
	$content .= "        <th scope=\"col\">Uploaded By</th>";
	$content .= "        <th scope=\"col\">Uploaded Date</th>";
	$content .= "        <th scope=\"col\">&nbsp;</th>";
	$content .= "        <th scope=\"col\">Type</th>";
	$content .= "        <th scope=\"col\">Size</th>";
	$content .= "        <th scope=\"col\">Delete</th>";
	$content .= "      </tr>";
	$content .= "    </thead>";
	$content .= "    <tbody>";

	//show all projects
	for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {

    $view_rights = 0;
    // CHECK IF USER HAS RIGHTS TO SEE ITEM
    if ($row['uploaded_by'] == UID) {
      $view_rights = 1;
    } else {
      if (stripos($row['perm_val'],"V") !== FALSE) {
        $view_rights = 1;
      }
    }

		if (ADMIN) {
       $view_rights = 1;
		}

    if ($view_rights > 0) {

		if (($i % 2) == 1) {
			$content .= "      <tr>";
		} else {
			$content .= "      <tr class=\"odd\">";
		}

		//show name and a link
		$content .= "        <th scope=\"row\">";
		if ($row['summary'] <> "") {
			$listname = $row['summary'];
		} else {
			$listname = $row['filename'];
		}
		$content .= "<a href=\"files.php?x=".$x."&amp;action=details&amp;project_id=".$row['project_id']."&amp;task_id=".$task_id."&amp;file_id=".$row['id']."\">".$listname."</a>\n";
		$content .= "</th>";

		//uploaded by
		$uploaded_by_name = db_result(db_query('SELECT CONCAT(firstname," ",lastname) as fullname FROM users WHERE id='.$row["uploaded_by"]),0,0);
		$content .= "<td>".$uploaded_by_name."</td>\n";

		//uploaded date
		$content .= "<td>".nicedate($row['uploaded_date'])."</td>\n";


		if (strlen($row['content']) > 0) {
			$content .= "        <td><a href=\"files.php?x=".$x."&amp;action=view_content&amp;project_id=".$row['project_id']."&amp;task_id=".$task_id."&amp;file_id=".$row['id']."\">View</a></td>";
		} else {
			$content .= "        <td><a href=\"files.php?x=".$x."&amp;action=download&amp;project_id=".$row['project_id']."&amp;task_id=".$task_id."&amp;file_id=".$row['id']."\" onclick=\"window.open('files.php?x=".$x."&amp;action=download&amp;file_id=".$row['id']."'); return false\">View</a></td>";
		}

		//document type
		$doc_type_desc = db_result(db_query('SELECT description FROM data_table WHERE type="DOCTYP" AND code="'.$row["doc_type"].'"'),0,0);
		$content .= "<td>".$doc_type_desc."</td>\n";

		if ($row['size'] > 0) {
			$content .= "        <td>".$row['size']."</td>";
		} else {
			$content .= "        <td>Online</td>";
		}
		$content .= "        <td>";
    if ($row['uploaded_by'] == UID || ADMIN) {
		$content .= "        	<center><a href=\"files.php?x=".$x."&amp;action=submit_del&amp;project_id=".$row['project_id']."&amp;task_id=".$task_id."&amp;file_id=".$row['id']."\" onclick=\"return confirm('Confirm Document Delete!')\"> <img src=\"images/icon-delete.gif\" alt=\"Remove Document from Project\" title=\"Remove Document from Project\" /></a></center>";
    }
		$content .= "        </td>";

		$content .= "      </tr>";
	}
	}

	$content .= "    </tbody>";
	$content .= "  </table>";
	$content .= "</div>";
	$content .= "</p>\n";

	return $content;
}

?>
