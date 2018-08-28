<?php


ini_set("display_errors",1);
error_reporting(E_ALL);

if (isset($_GET['err'])) {

  echo "<pre>";
  print_r($_REQUEST);
  exit;
}

if(isset($_REQUEST['wid']) && !empty($_REQUEST['wid']) && is_numeric($_REQUEST['wid'])) {

    require_once("class.member.php");

    $objMember = new Member();
    $member = $objMember->isValid();
    $member = 1;
    
    if ($member == 1) {

      function scrrefcustomerror($errornumber, $errormessage, $errorfile, $errorrow, $objMember)
      {
        echo '<meta http-equiv="refresh" content="0;url='.$objMember->return_url.'?err=Error code '.$errornumber.' - '.$errormessage.'">';
      }
      set_error_handler("scrrefcustomerror");

?>
      <html>
        <head>
          <meta http-equiv="Content-Language" content="en-us">
          <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
          <?php
            $ticketurl = $objMember->prepareTicketURL();
        //    echo $ticketurl;

            if($fp = fopen($ticketurl, 'r'))
            {
              $content = fread($fp, 1000000);
              echo '<meta http-equiv="refresh" content="0;url='.$content.'">';
              fclose($fp);
            }   
            
          ?>
        </head>
      </html>
 <?php }
    else { 
      
      header('Location: custom-error.php');
      exit;      
    } 
} else { 
  
  header('Location: custom-error.php');
  exit;     
} 

?>