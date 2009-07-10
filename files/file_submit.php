<?php
/* $Id$ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');

//update or insert ?
if (empty($_REQUEST['action'])) {
	error('Document Submit', 'No action given');
}

//if user aborts, let the script carry onto the end
ignore_user_abort(TRUE);

//$def_folder = db_result(db_query('SELECT description FROM data_table WHERE type="FOLDERS" AND id='.FOLDER_DEFAULT),0,0);
$def_folder = "A";

switch($_REQUEST['action']) {
	/*
	 //handle a file update
	 case 'submit_update':
		//if all values are filled in correctly we can submit the messages-item
		$input_array = array('file_id', 'project_id');
		foreach ($input_array as $var) {
		if (!@safe_integer($_POST[$var])) {
		error('Message submit', "Variable $var is not set");
		}
		${$var} = $_POST[$var];
		}

		if ($_POST['project_doc'] == "on") {
		$project_doc = 1;
		} else {
		$project_doc = 0;
		}
		$doctype = safe_data($_POST['doctype']);
		$summary = safe_data($_POST['summary']);
		$content = $_POST['content'];
		$description = safe_data_long($_POST['description']);

		//make email adresses and web links clickable
		$description = html_links($description, 1);

		//do data consistency check on project_id
		if ($project_id != 0) {
		if (db_result(db_query('SELECT COUNT(*) FROM files WHERE project_id='.$project_id), 0, 0) == 0) {
		error('File submit', 'Data consistency error - file is not attached to a project');
		}
		}

		if (isset($_POST['task_id'])) {
		$task_id = $_POST['task_id'];
		} else {
		$task_id = 0;
		}

		db_begin();
		db_query('UPDATE files SET
		summary=\''.$summary.'\',
		project_doc='.$project_doc.',
		doc_type=\''.$doctype.'\',
		content=\''.$content.'\',
		description=\''.$description.'\'
		WHERE id ='.$file_id.' AND project_id='.$project_id);

		//set time of last messages post to this project
		//db_query('UPDATE projects SET lastfileupload=now() WHERE id='.$project_id);

		//insert data into perms
		// first delete
		$mod_id = db_result(db_query('SELECT id FROM data_table WHERE type="MODULES" AND description="Documents"'),0,0);
		@db_query('DELETE FROM perms WHERE module_id='.$mod_id.' AND rec_id='.$file_id);
		// next add perms through loop
		foreach ($_POST as $key => $value) {
		if (@is_array($value)) {
		if (substr($key,0,2) == "CB") {
		$side = substr($key,3,1);
		$id = substr($key,5,10);
		$val = implode("",$value);
		db_query('INSERT INTO perms(module_id, rec_id, side, sec_lvl_id, permissions) VALUES('.$mod_id.', '.$file_id.', \''.$side.'\', '.$id.', \''.$val.'\')');
		}
		}
		}

		db_commit();

		break;

		//handle create content
		case 'submit_create':
		//if all values are filled in correctly we can submit the messages-item
		$input_array = array('project_id');
		foreach ($input_array as $var) {
		if (!@safe_integer($_POST[$var])) {
		error('Message submit', "Variable $var is not set");
		}
		${$var} = $_POST[$var];
		}

		if ($_POST['project_doc'] == "on") {
		$project_doc = 1;
		} else {
		$project_doc = 0;
		}
		$doctype = safe_data($_POST['doctype']);
		$summary = safe_data($_POST['summary']);
		$content = $_POST['content'];

		//make email adresses and web links clickable
		//      $description = html_links($description, 1);

		//do data consistency check on project_id
		if ($project_id != 0) {
		if (db_result(db_query('SELECT COUNT(*) FROM projects WHERE id='.$project_id), 0, 0) == 0) {
		error('File submit', 'Data consistency error - file is not attached to a project');
		}
		}

		if (isset($_POST['task_id'])) {
		$task_id = $_POST['task_id'];
		} else {
		$task_id = 0;
		}

		db_begin();

		//alter file database administration
		$q = db_query("INSERT INTO files (summary,project_doc,doc_type,uploaded_date,uploaded_by,project_id,task_id,content)
		VALUES ('$summary',$project_doc,'".$doctype."',now(),".UID.",$project_id,$task_id,'".$content."' )");

		//get last insert id
		$file_id = db_lastoid('files_id_seq');

		//insert data into perms
		// first delete
		$mod_id = db_result(db_query('SELECT id FROM data_table WHERE type="MODULES" AND description="Documents"'),0,0);
		@db_query('DELETE FROM perms WHERE module_id='.$mod_id.' AND rec_id='.$file_id);
		// next add perms through loop
		foreach ($_POST as $key => $value) {
		if (@is_array($value)) {
		if (substr($key,0,2) == "CB") {
		$side = substr($key,3,1);
		$id = substr($key,5,10);
		$val = implode("",$value);
		db_query('INSERT INTO perms(module_id, rec_id, side, sec_lvl_id, permissions) VALUES('.$mod_id.', '.$file_id.', \''.$side.'\', '.$id.', \''.$val.'\')');
		}
		}
		}

		db_commit();
		break;
		*/
	//handle a file upload
	case 'submit_upload':

		//check if there was an upload
		if (!is_uploaded_file($_FILES['userfile']['tmp_name'])) {
			//no file upload occurred
			switch($_FILES['userfile']['error']) {
				case 1:
					error('Document Submit', "The uploaded file exceeds the upload_max_filesize directive in php.ini");
					break;

				case 2:
					error('Document Submit', "The uploaded file exceeds maximum allowed file size");
					break;

				case 3:
					error('Document Submit', "The uploaded file was only partially uploaded");
					break;

				case 4:
					error('Document Submit', 'No file was uploaded. Please go back and try again');
					break;

				case 6:
					error('Document Submit', "Missing a temporary folder");
					break;

				default:
					error('Document Submit', "Unknown file upload error with error code ".$_FILES['userfile']['tmp_name']);
					break;
			}
		}

		if (!@safe_integer($_POST['project_id'])) {
			//delete any upload before invoking the error function
			if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
				unlink($_FILES['userfile']['tmp_name']);
			}
			error('Document submit', 'Not a valid project_id');
		}
		$project_id = $_POST['project_id'];

		if (isset($_POST['task_id'])) {
			$task_id = $_POST['task_id'];
		} else {
			$task_id = 0;
		}

		//check the destination directory is writeable by the webserver
		if (!is_writable(FILE_BASE.'/'.$def_folder.'/')) {
			unlink($_FILES['userfile']['tmp_name']);
			error('Configuration error', 'The upload directory does not have write permissions set properly, or the directory does not exist.  Document upload has not been accepted.');
		}

		//check for zero length files
		if ($_FILES['userfile']['size'] == 0) {
			unlink($_FILES['userfile']['tmp_name']);
			error('Document Submit', 'No Document was uploaded. Please go back and try again.');
		}

		//check for ridiculous uploads
		if ($_FILES['userfile']['size'] > FILE_MAXSIZE) {
			unlink($_FILES['userfile']['tmp_name']);
			error('Document Submit', sprintf('The maximum upload size is %s bytes. Your Document was too big and has been deleted.', FILE_MAXSIZE));
		}

		//check for dangerous file uploads
		if (preg_match('/\.(php|php3|php4|php5|js|asp)$/', $_FILES['userfile']['name'])) {
			unlink($_FILES['userfile']['tmp_name']);
			error('Document submit', 'The Document types .php, .php3, .php4, .php5, .js and .asp are not acceptable for security reasons. You must either rename or compress the file.');
		}

		if (empty($_FILES['userfile']['type']) || $_FILES['userfile']['type'] == '') {
			$mime = "application/octet-stream";
		} else {
			$mime = $_FILES['userfile']['type'];
		}

		//okay accept file
		db_begin();

		//validate characters in filename
		$filename = validate($_FILES['userfile']['name']);

		//limit file name to 200 characters - should be enough for any sensible(!) file name :-)
		$filename = substr($filename, 0, 200);
		//strip illegal characters
		$filename = preg_replace('/[\x00-\x2a\x2f\x3a-\x3c\x3e-\x3f\x5c\x5e\x60\x7b-\x7e]|[\.]{2}/', '_', $filename);

		//escape for database
		$db_filename = db_escape_string($filename);

		//get file token
		$token = get_file_token();

		//alter file database administration
		$q = db_query("INSERT INTO files (filename,folder_id,token,size,uploaded_date,uploaded_by,project_id,task_id,mime)
			VALUES ('$db_filename',".FOLDER_DEFAULT.",'$token',".$_FILES['userfile']['size'].",now(),".$_SESSION['UID'].",$project_id,$task_id,'".$mime."' )");

		//get last insert id
		$file_id = db_lastoid('files_id_seq');

		//copy it
		if (!move_uploaded_file($_FILES['userfile']['tmp_name'], FILE_BASE.'/'.$def_folder.'/'.$token)) {
			db_query('DELETE FROM files WHERE id='.$file_id);
			unlink($_FILES['userfile']['tmp_name']);
			db_rollback();
			error('Document submit', 'Internal error: The Document cannot be moved to the master document directory, deleting upload');
		}

		//alter projects lastfileupload
		//db_query('UPDATE projects SET lastfileupload=now() WHERE id='.$project_id);

		//make the file non-executable for security
		//chmod(FILE_BASE.'/'.$def_folder.'/'.$token, 0644);

		//success!
		db_commit();

		foreach($_FILES as $file) {
            $n = $file['name'];
            $s = $file['size'];
            if (!$n) continue;
            echo "File: $n ($s bytes)";
        }

		break;
/*
				case 'submit_del':

					if (!@safe_integer($_GET['project_id'])) {
						error('Document submit', 'Not a valid project_id');
					}

					$project_id = $_GET['project_id'];

					if (isset($_POST['task_id'])) {
						$task_id = $_POST['task_id'];
					} else {
						$task_id = 0;
					}

					if (!@safe_integer($_GET['file_id'])) {
						error('Document submit', 'Not a valid file_id');
					}

					$file_id = $_GET['file_id'];

					//get the files from this task
					$q = db_query('SELECT files.uploaded_by AS uploaded_by,
                          files.filename AS filename,files.folder_id AS folder_id,
                          files.token AS token,projects.owner AS owner FROM files
                          LEFT JOIN projects ON (files.project_id=projects.id) WHERE files.id='.$file_id);

					if (db_numrows($q) != 0) {

						//show it
						$row = @db_fetch_array($q, 0);
						//owners of the file and admins can delete files
						if ((UID == $row['owner']) || (UID == $row['uploaded_by'])) {

							$loc_folder = db_result(db_query('SELECT description FROM data_table WHERE type="FOLDERS" AND id='.$row['folder_id']),0,0);

							// get filename - backward compatible
							if ($row['token'] == "") {
								$os_file = $file_id . "__" . $row['filename'];
							} else {
								$os_file = $row['token'];
							}

							//delete file from disk
							if (file_exists(FILE_BASE.'/'.$loc_folder.'/'.$os_file)) {
								unlink(FILE_BASE.'/'.$loc_folder.'/'.$os_file);
							}
							//delete record of file
							db_query('DELETE FROM files WHERE id='.$file_id);
						}
					}
					break;
*/
				default:
					error('Document submit', 'Invalid request given');
					break;
}

?>