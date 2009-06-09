<?php
/*
* generate_url.php
*
* Script for generating URLs that can be accessed one single time.
*
*/

/* Generate a unique token: */
$token = md5(uniqid(rand(),1));

/* This file is used for storing tokens. One token per line. */
$file = "/tmp/urls.txt";
if( !($fd = fopen($file,"a")) )
        die("Could not open $file!");

if( !(flock($fd,LOCK_EX)) )
        die("Could not aquire exclusive lock on $file!");

if( !(fwrite($fd,$token."\n")) )
        die("Could not write to $file!");

if( !(flock($fd,LOCK_UN)) )
        die("Could not release lock on $file!");

if( !(fclose($fd)) )
        die("Could not close file pointer for $file!");

/* Parse out the current working directory for this script. */
$cwd = substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],"/"));

/* Report the one-time URL to the user: */
print "Use this URL to download the secret file:<br><br>\n";
print "<a href='http://".$_SERVER['HTTP_HOST']. 
      "$cwd/get_file.php?q=$token'>\n";
print "http://".$_SERVER['HTTP_HOST']."/get_file.php?q=$token</a>\n";

?>
