<?php
/* $Id$ */

//security check
if (!isset($_SESSION['UID'])) {
  die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

function readfile_chunked($filename,$retbytes=true) {
   $chunksize = 1*(1024*1024); // how many bytes per chunk
   $buffer = '';
   $cnt =0;
   // $handle = fopen($filename, 'rb');
   $handle = fopen($filename, 'rb');
   if ($handle === false) {
       return false;
   }
   while (!feof($handle)) {
       $buffer = fread($handle, $chunksize);
       echo $buffer;
       if ($retbytes) {
           $cnt += strlen($buffer);
       }
   }
       $status = fclose($handle);
   if ($retbytes && $status) {
       return $cnt; // return num. bytes delivered like readfile() does.
   }
   return $status;

}


//set variables
$fp = '';

if (!@safe_integer($_REQUEST['file_id'])) {
  return;
}

$file_id = $_REQUEST['file_id'];

//$mod_id = db_result(db_query('SELECT id FROM data_table WHERE type="MODULES" AND description="Documents"'),0,0);
//$side = $_SESSION['side'];
//$sec_lvl = $_SESSION['sec_lvl_id'];

//get the files info
if (!($q = db_query('SELECT * FROM files WHERE file_id='.$file_id, 0)))  {
  error('Download file', 'There was an error in the data query');
}

if (!$row = db_fetch_array($q, 0)) {
	error('Download file', 'Invalid file_id given');
}

//$folder = db_result(db_query('SELECT description FROM data_table WHERE type="FOLDERS" AND id='.$row['folder_id']),0,0);
$folder = "A";

// get filename - backward compatible
if ($row['token'] == "") {
	$os_file = $file_id . "__" . $row['filename'];
} else {
	$os_file = $row['token'];
}

//check the file exists
if (!(file_exists(FILE_BASE.'/'.$folder.'/'.$os_file))) {
  error('Download file', 'The file '.$row['filename'].' is missing from the server');
}

//open the file
if (!($fp = @fopen(FILE_BASE.'/'.$folder.'/'.$os_file, 'rb'))) {
  error('Download file', 'File handle for '.$row['filename'].' cannot be opened' );
}

//get rid of some problematic system settings
@ob_end_clean();
@ini_set('zlib.output_compression', 'Off');

//uncomment the line below if PHP script timeout occurs before download finishes
//@set_time_limit(0);

//these headers are for IE 6
header('Pragma: public');
header('Cache-Control: no-store, max-age=0, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Cache-control: private');

//send the headers describing the file type
header('Content-Type: '.$row['mime']);
// changed disposition from inline to attachment
header('Content-Disposition: attachment; filename='.$row['filename']);
header('Content_Length: '.$row['size']);

//send it
//fpassthru($fp);
readfile_chunked(FILE_BASE.'/'.$folder.'/'.$os_file);
//don't send any more characters (would corrupt data in download)
exit;
?>