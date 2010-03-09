<?php
include '../init.php';
$pf = $_REQUEST['pf'];

function ProcessExportMailingList()
{
  global $mail, $error_list;
  
  
  $mailing_list_data  = $mail->CreateMailingList();
  
  $option            = $_REQUEST['option'];
  $i =0;
  if($option == "")
  {
    $error_list[$i] = "Please choose export options";
    $i++;
  }
  
  if(!is_array($error_list))
  {
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-type: text/plain");
    $output = "";
    if($option == "all")
    {
      foreach($mailing_list_data as $value)
      {
        header("Content-Disposition: attachment; filename=\"all_mailing_list.csv\";" );
        $output .= $value['firstname']."\t".$value['lastname']."\t".$value['email']."\n";
      }
    }
    elseif($option == "active")
    {
      foreach($mailing_list_data as $value)
      {
        header("Content-Disposition: attachment; filename=\"active_mailing_list.csv\";" );
        if($value['status'] == "active")
          $output .= $value['firstname']."\t".$value['lastname']."\t".$value['email']."\n";
      }
    }    
    elseif($option == "inactive")
    {
      foreach($mailing_list_data as $value)
      {  
        header("Content-Disposition: attachment; filename=\"inactive_mailing_list.csv\";" );
        if($value['status'] == "inactive")
          $output .= $value['firstname']."\t".$value['lastname']."\t".$value['email']."\n";
      }
    }
    print $output;
  }
  else
  {
    ShowFormExportMailingList();
  }
}

function ShowFormExportMailingList()
{
  global $tpl, $error_list;
  
  $tpl->assign('error',$error_list);
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->display("admin/mailing.html");
}
/*###########################################################
Section : Main
###########################################################*/

$user->AuthenticationAdmin();
if($pf == "export" && $process == "")
{
  ShowFormExportMailingList();

}
elseif($pf == "export" && $process == "export")
{
  ProcessExportMailingList();
}
?>
