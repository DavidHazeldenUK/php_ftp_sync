<?php
// your settings
$ftp_server    = 'images.ftpstream.com';
$user          = 'images';
$password      = 'images';
$max_images = 5000;

$ftp_remote_document_root = './';
$local_target_path   = './wp-content/uploads/vip-computers/';


//create directory if not exist, or ignore if it does...function calls itself
$mkdir_result = createpath($local_target_path);
echotxt($mkdir_result);
echotxt('Save Files Target = '. $local_target_path);
chdir($local_target_path);
//moved to target, now lets FTP contents here...

$ftp_server_connection = ftp_connect($ftp_server);
if ($ftp_server_connection)
{
	echotxt('FTP Connection Okay');
	$ftp_authentication = ftp_login($ftp_server_connection, $user, $password);
	if ($ftp_authentication)
  {
    echotxt('FTP Auth Okay');
    //change directory to ftp_remote_directory
		$result_002 = ftp_sync($ftp_server_connection, $ftp_remote_document_root, $local_target_path);
		echotxt('Running FTP_SYNC ('.$ftp_remote_document_root.') = '. $result_002);
    //close connection
		ftp_close($ftp_server_connection);
	}
  else
  {
		echotxt('FTP Auth Failed');
	}
}
else
{
	echotxt('FTP Connection Failed - Check Address or Route');
}

echotxt('done.');

function ftp_sync($ftp_server_connection, $ftp_remote_path, $target_path)
{
	echotxt("Running Function ftp_sync");
	echotxt("ftp_remote_path = ". $ftp_remote_path);
	echotxt("target_path = ". $target_path);
	if ($ftp_remote_path !== '.')
	{
		echotxt("Target Path isn't a fullstop/dot");
		if (ftp_chdir($ftp_server_connection, $ftp_remote_path) === FALSE)
		{
			echotxt ('Change dir failed: ' . $ftp_remote_path);
			return;
		}
		else
		{
			echotxt ('Changing Directory was a Success');
		}
		if (!(is_dir($ftp_remote_path)))
		{
			echotxt('Making Path = '. $ftp_remote_path);
			mkdir($target_path . $ftp_remote_path);
		}
		chdir($ftp_remote_path);
	}
	else
	{
		echo "Target Path = DOT";
	}
  $i = 0;
	echotxt("Listing Contents for this path = ". $ftp_remote_path);
	$contents = ftp_nlist($ftp_server_connection, '.');
	var_dump($contents);
	foreach ($contents as $file)
	{
		echotxt($file);
    //loop through the files
		if ($file == '.' || $file == '..')
    {
      //ignore linux directories
			continue;
		}
    
		if (@ftp_chdir($ftp_server_connection, $file))
		{
			ftp_chdir($ftp_server_connection, "..");
			ftp_sync($file, $ftp_server_connection, $target_path);
			echotxt($file);
		}
		else
		{
      //get file from remote server
      //connection, local_file, remote_file
      $save_to = $target_path . $file;
      
      if (file_exists($save_to))
      {
        echotxt('The file '. $save_to .' exists, skipping download');
      }
      else
      {
        echotxt('The file'. $save_to .' does not exist, saving file!');
        ftp_get($ftp_server_connection, $save_to, $file, FTP_BINARY);
      }
		}

    //debug
    $i++;
    if ($i >= $max_images)
    {
      echo "<p>Max Images of ".$max_images." reached. </p>";
      break;
    }
	}
  //moving to parent folder
	ftp_chdir($ftp_server_connection, '..');
	chdir('..');
}

function echotxt ($msg)
{
  echo '<p>'.$msg.'</p>';
}

function createPath($path)
{
    if (is_dir($path)) return true;
    $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
    $return = createPath($prev_path);
    return ($return && is_writable($prev_path)) ? mkdir($path) : false;
}
?>