<?php
//$to = "mpembert@wfubmc.edu";
//$subject = "Hi!";
//$body = print_r($_FILES["uploadedfile"]);
//$headers = "From: noreply@wfubmc.edu\r\n" . "X-Mailer: php";
//if (mail($to, $subject, $body, $headers)) {
//  echo("<p>Message delivery succeeded...</p>");
// } else {
//  echo("<p>Message delivery failed...</p>");
// }
/* $Id$ */

//security check
if (!isset($_SESSION['UID'])) {
  die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

$target_path = "files/filebase/A/";
$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
    echo "The file ".  basename( $_FILES['uploadedfile']['name']).
    " has been uploaded";
} else{
    echo "There was an error uploading the file, please try again!";
}
?>