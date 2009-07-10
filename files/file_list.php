<?php
//function runSQL($rsql) {
//	$connect = mysql_connect($hostname,$username,$password) or die ("Error: could not connect to database");
//	$db = mysql_select_db($dbname);
//
//	$result = mysql_query($rsql) or die ('test');
//	return $result;
//	mysql_close($connect);
//}
//
function countRec($fname,$tname,$p_id) {
//	$sql = "SELECT count($fname) FROM $tname WHERE project_id=$p_id";
	return db_simplequery("files","count(*)","project_id",$p_id);
//	$result = db_query($sql);
//	while ($row = mysql_fetch_array($result)) {
//		return $row[0];
//	}
}

$page = $_POST['page'];
$rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];
$project_id = $_REQUEST['project_id'];

if (!$sortname) $sortname = 'name';
if (!$sortorder) $sortorder = 'desc';

$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 10;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

//$sql = "SELECT iso,name,printable_name,iso3,numcode FROM country $sort $limit";
//$result = runSQL($sql);

$SQL = "SELECT f.*, emp.FirstName, emp.LastName from files f, employees emp where f.project_id=".$project_id." and emp.employee_ID=f.uploaded_by $sort $limit";
$result = db_query($SQL);

$total = db_simplequery("files","count(*)","project_id",$project_id);

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");
$json = "";
$json .= "{\n";
$json .= "page: $page,\n";
$json .= "total: $total,\n";
$json .= "rows: [";
$rc = false;
while ($row = mysql_fetch_array($result)) {
	if ($rc) $json .= ",";
	$json .= "\n{";
	$json .= "id:'".$row['file_id']."',";
	$json .= "cell:['".$row['filename']."'";
	$json .= ",'".$row['size']."'";
	$json .= ",'".$row['uploaded_date']."'";
	$json .= ",'".$row['FirstName']." ".$row['LastName']."'";
	$json .= "]}";
	$rc = true;
}
$json .= "]\n";
$json .= "}";
echo $json;
?>