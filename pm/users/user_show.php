<?php
/* $Id: */

//security check
if (! defined('UID')) {
  die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

$content = '';
$no_access_project = array();
$no_access_group   = array();
$user_gid = array();

//check for valid request
if (!@safe_integer($_GET['user_id'])) {
  error('User show', 'No user_id was given');
}

$user_id = $_GET['user_id'];

//select
$q = db_query('SELECT id, login_name, fullname, email, security_level FROM users WHERE id='.$user_id);

//get info
if (!($row = @db_fetch_array($q, 0))) {
  error('User error', 'User information is not available');
}

if ($row['security_level'] == '0') {
  $content .= "<b><div style=\"text-align:center\"><span class=\"red\">Inactive User</span></div></b><br />";
}
$content .= "<table>".
              "<tr><td>Login Name:</td><td>".$row['login_name']."</td></tr>\n".
              "<tr><td>Full Name:</td><td>".$row['fullname']."</td></tr>\n".
              "<tr><td>Email Address:</td><td><a href=\"mailto:".$row['email']."\">".$row['email']."</a></td></tr>\n";

switch($row['security_level']) {
  case '1':
    $content .= "<tr><td>Guest:</td><td>Yes</td></tr>\n";
  case '2':
    $content .= "<tr><td>Vendor:</td><td>Yes</td></tr>\n";
  case '3':
    $content .= "<tr><td>Agent:</td><td>Yes</td></tr>\n";
  case '99':
    $content .= "<tr><td>Administrator:</td><td>Yes</td></tr>\n";
}

//get the last login time of a user
$row = @db_result(db_query('SELECT lastaccess FROM logins WHERE user_id='.$user_id), 0, 0);
$content .=   "<tr><td>Last Time Logged In:</td><td>".nicetime($row)."</td></tr>\n";

//Get the number of projects owned
$projects_owned = db_result(db_query('SELECT COUNT(*) FROM projects WHERE owner='.$user_id), 0, 0);
$content .=   "<tr><td>Projects Owned:</td><td>".$projects_owned."</td></tr>\n";

//Get the number of tasks owned
$tasks_owned = db_result(db_query('SELECT COUNT(*) FROM tasks WHERE owner='.$user_id), 0, 0);
$content .=   "<tr><td>Tasks Owned:</td><td>".$tasks_owned."</td></tr>\n";

//Get the number of tasks completed that are owned
$row = db_result(db_query('SELECT COUNT(*) FROM tasks WHERE owner='.$user_id.' AND status=\'done\''), 0, 0);
$content .=   "<tr><td>Number of Tasks Completed</td><td>".$row."</td></tr>\n";

//Get the number of messages posts
$row = db_result(db_query('SELECT COUNT(*) FROM messages WHERE user_id='.$user_id), 0, 0);
$content .=   "<tr><td>Messages Added</td><td>".$row."</td></tr>\n";

//Get the number of files uploaded and the size
$q   = db_query('SELECT COUNT(size), SUM(size) FROM files WHERE uploaded_by='.$user_id);
$row = db_fetch_num($q, 0);
$content .=   "<tr><td>Files Added</td><td>".$row[0]."</td></tr>\n";
$size = $row[1];

if ($size == '') {
  $size = 0;
}
$content .=   "<tr><td>Total size of owned files:</td><td>".$size." bytes</td></tr>\n".
            "</table>";

new_box('User Information', $content);


//-------------------------------------------------
//shows quick links to the tasks that the user owns
//-------------------------------------------------
if ($tasks_owned + $projects_owned > 0) {
  $content = "<ul>";

  //********************************
  // TODO - Add Projects/Tasks query - do I show both projects and tasks?????  hmm
  //********************************

  //Get the number of tasks
  $q = db_query('SELECT id, name, parent, status, finished_time AS finished_time, projectid FROM tasks WHERE owner='.$user_id.' AND archived=0');

  //show them
  for ($i=0; $row = @db_fetch_array($q, $i); ++$i) {

    $status_content = '';

    //status
    switch($row['status']) {
      case 'done':
        $status_content="<span class=\"green\">(".$task_state['done']."&nbsp;".nicedate($row['finished_time']).")</span>";
        break;

      case 'active':
        $status_content="<span class=\"orange\">(".$task_state['active'].")</span>";
        break;

      case 'notactive':
        $status_content="<span class=\"green\">(".$task_state['planned'].")</span>";
        break;

      case 'cantcomplete':
        $status_content="<span class=\"blue\">(".$task_state['cantcomplete']."&nbsp;".nicedate($row['finished_time']).")</span>";
        break;
    }

    if ($row['parent'] == 0) {
      //project
      $status_content ="(Projects)";
    }

    //show the task
    $content .= "<li><a href=\"tasks.php?x=".$x."&amp;action=show&amp;taskid=".$row['id']."\">".$row['name']."</a> ".$status_content."</li>\n";
  }
  $content .= "</ul>";
  new_box('Owned Projects/Tasks', $content);
}

?>
