<?php
//easy way to do it
//DPEH
require_once 'vendor/autoload.php';

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter_LOCAL;
use League\Flysystem\Adapter\Ftp as Adapter_FTP;

$local_path = __DIR__ . '/testdir/';
$filesystem_local = new Filesystem(new Adapter_LOCAL($local_path));
echo "LOCAL PATH = ". $local_path . "</br>";

$remote_path = "./";
$filesystem_remote = new Filesystem(new Adapter_FTP([
    'host' => 'images.ftpstream.com',
    'username' => 'images',
    'password' => 'images',

    /** optional config settings **/
    'port' => 20,
    'root' => $remote_path,
    'passive' => false,
    'ssl' => false,
    'timeout' => 3,
]));

$manager = new League\Flysystem\MountManager();
$manager->mountFilesystem('local', $filesystem_local);
$manager->mountFilesystem('ftp', $filesystem_remote);


//$contents = $manager->listContents('local://testdir', true);
//var_dump($contents);

$ftp_files = $manager->listContents('ftp://'. $remote_path, true);

if(!array_filter($ftp_files)) {
  echo "Cant Find Find Any Files on Remote FTP Server";
}
else
{
  //var_dump($ftp_files);

  $max_ftp_files = 5;
  $i = 0;

  foreach ($ftp_files as $ftpfile) {
    echo $ftpfile['basename'].' is located at '.$ftpfile['path'].' and is a ' .$ftpfile['type'] . '</br>';

    $localfile = 'local://'.$ftpfile['path'];
    $remotefile = 'ftp://'.$ftpfile['path'];
    
    //echo $localfile .' ||||||| '. $remotefile . "</br>";

    $file_exists_r = $manager->has($remotefile);
    $file_exists_l = $manager->has($localfile);
    
    if ($file_exists_l == FALSE){
      //copy file from ftp
      $status_copy = $manager->copy($remotefile, $localfile);
      echo "File Transfer Status = (". $status_copy . ")</br>";
    }
    elseif ($manager->getTimestamp($remotefile) > $manager->getTimestamp($localfile))
    {
      //copy file from ftp
      $status_copy = $manager->copy($remotefile, $localfile);
      echo "File Transfer Status = (". $status_copy . ")</br>";      
    }
    
    echo "Remote Exists? ". $file_exists_r . "</br>";
    echo "Local Exists? ". $file_exists_l . "</br>";
    
    if($i==$max_ftp_files){break;};
    $i++;
    $status_copy = NULL;     

    echo "------------------------------------------------------------------------------------------------</br>";
  }
}




echo "DONE";
?>