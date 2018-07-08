<?php
echo "File Transfers 1";
//DAVID HAZELDEN 2018

$myftp = new FileSyncFtp('images.ftpstreddam.com', 'images', 'images');
var_dump($myftp);

class FileSyncFtp
{
   const display_txt_error = "display";
   private $ftp_server_address = '';
   private $ftp_user = '';
   private $ftp_password = '';
   private $obj_ftp_connection = NULL;
   private $ftp_timeout = 5;
   private $ftp_port = 21;
  
   function __construct($ftpsa, $ftpu, $ftpp)
   {   
    $this->ftp_server_address = $ftpsa;
    $this->ftp_user = $ftpu;
    $this->ftp_password = $ftpp;
    $connected = $this->ftp_Connect();
   }

  function ftp_Connect()
  {
    $this->obj_ftp_connection = ftp_Connect($this->ftp_server_address, $this->ftp_port, $this->ftp_timeout);
    
    
    
    if ($this->obj_ftp_connection)
    {
      echo "ftp yay...";
      $ip = gethostbyname($this->ftp_server_address);
      echo self::printHtmlDebug(__CLASS__.' - Success - Connected to '. $this->ftp_server_address. ' - ('.$ip.')');
    }
    else
    {
      echo self::printHtmlDebug(__CLASS__.' - FTP Connection Failed - Check Address or Route ');
    }



  }
  
  function setupAuth() {
    
  }
  
  function changeDir() {
    
  }
  function listDir() {
    
  }
  function copyFile() {
  
  }
  function printHtmlDebug($txt_error)
  {
    if(self::display_txt_error == "display")
    {
      $txt_error = "<p>". $txt_error ."</p>"; 
    }
    else
    {
      $txt_error = "";
    }
    return $txt_error;
  }
}

echo "<p>End of Processing</p>";

?>