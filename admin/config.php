<?php
include '../init.php';
$pf = $_REQUEST['pf'];

function ShowFormConfig() {
  global $tpl, $pf,$error_list,$site_name, $site_url, $site_mail, $protect_path, $protect_url,$notify_email,
         $notify_from,$notify_expire, $success;
  
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('success',$success);
  $tpl->assign('error',$error_list);
  $tpl->assign('site_name',$site_name);
  $tpl->assign('site_path', $site_path);
  $tpl->assign('site_url', $site_url);
  $tpl->assign('site_mail', $site_mail);
  $tpl->assign('protect_path', $protect_path);
  $tpl->assign('protect_url', $protect_url);
  $tpl->assign('notify_email', $notify_email);
  $tpl->assign('notify_from', $notify_from);
  $tpl->assign('notify_expire', $notify_expire);      
  $tpl->display('admin/config.html');  
}

function ShowConfig() {
  global $tpl,$pf, $error_list, $site_name, $site_url, $site_mail, $protect_path, $protect_url,$notify_email,
         $notify_from,$notify_expire,$success;
         
  $pf = $_REQUEST['pf'];
  $process = $_REQUEST['process'];
  if ($process == 'edit') 
  {  
    $site_name      = stripslashes($_REQUEST['site_name']);
    $site_mail      = stripslashes($_REQUEST['site_mail']);
    $protect_path   = stripslashes($_REQUEST['protect_path']);
    $protect_url    = stripslashes($_REQUEST['protect_url']);
    $notify_email   = stripslashes($_REQUEST['notify_email']);
    $notify_from    = stripslashes($_REQUEST['notify_from']);
    $notify_expire  = stripslashes($_REQUEST['notify_expire']);
      
    $i = 0;
    if($site_name == "" || $site_mail == "" || $protect_path == "" || $protect_url == "" || $notify_email == "" || $notify_from == "")
    {
      if($site_name == ""){
        $error_list[$i] = "Site name is required";
        $i++;
      }
      if($site_mail == ""){
        $error_list[$i] = "Site email is required";
        $i++;
      }
      if($protect_path == ""){
        $error_list[$i] = "Protected path is required";
        $i++;
      }
      if($protect_url == ""){
        $error_list[$i] = "Protected url is required";
        $i++;
      }
      if($notify_from == ""){
        $error_list[$i] = "Notify from is required";
        $i++;
      }
      if($notify_email == ""){
        $error_list[$i] = "Notify email is required";
        $i++;
      }
    }
    elseif(!IsDigit($notify_expire)){
      $error_list[$i] = "Notify expire must be digit";
      $i++;    
    }
    elseif(!IsEmailAddress($site_mail)){
      $error_list[$i] = "Site email is not valid format";
      $i++;
    }
    elseif(!IsEmailAddress($notify_email)){
      $error_list[$i] = "Notify email is not valid format";
      $i++;
    }
    if(!is_array($error_list)) 
    { 
      UpdateConfig($site_name,"site_name");
      UpdateConfig($site_mail,"site_mail");
      UpdateConfig($protect_path,"protect_path");
      UpdateConfig($protect_url,"protect_url");
      UpdateConfig($notify_email,"notify_email");
      UpdateConfig($notify_from,"notify_from");
      UpdateConfig($notify_expire,"notify_expire");
      $success = true;
    }
  }
  else
  {
    $site_name = CFG_SITE_NAME;
    $site_mail = CFG_SITE_MAIL;
    $protect_path = CFG_PROTECT_PATH;
    $protect_url = CFG_PROTECT_URL;
    $notify_email = CFG_NOTIFY_EMAIL;
    $notify_from = CFG_NOTIFY_FROM;
    $notify_expire = CFG_NOTIFY_EXPIRE; 
  }           
}
/*###########################################################
Section : Main
###########################################################*/
$user->AuthenticationAdmin();
if (empty($pf)) 
{
  ShowConfig();
  ShowFormConfig();
}
elseif ($pf == 'edit') 
{
  ShowConfig();
  ShowFormConfig();
}
?>
