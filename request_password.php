<?php
include 'init.php';

$pf = $_REQUEST['pf'];

function ShowFormRequestPassword() {
  global $tpl, $error_list, $success;
  
  $email      = $_REQUEST['email'];
  $process    = $_REQUEST['process'];
  $success    = $_REQUEST['success'];
  
  $tpl->assign('email',$email);
  $tpl->assign('error',$error_list);
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->display('request_password.html');
 
}

function ProcessRequestPassword() {
  global $tpl, $user, $error_list, $mail, $success;
  
  $success     = $_REQUEST['success'];
  $email       = $_REQUEST['email'];
  $process     = $_REQUEST['process'];
  $check_email = $user->CheckEmailExist($email);
  
  $i = 0;
  if ($process == '1') {
    if ($email == '') {
      $error_list[$i] = _("Please enter your email address.");
      $i++;      
    }
    elseif(!IsEmailAddress($email)) {
      $error_list[$i] = _("Email is not valid.");
      $i++;      
    }
    elseif (!$check_email) {
      $error_list[$i] = _("Email doesnt exist.");
      $i++;            
    }
    else {
      $username   = $check_email['username'];
      $password   = $user->RandomPassword($check_email['user_id']);
      $firstname  = $check_email['firstname'];
      $lastname   = $check_email['lastname'];
      $from_email = CFG_NOTIFY_EMAIL;
      $from_name  = CFG_NOTIFY_FROM;    
      $mail->RequestPasswordEmail(CFG_SITE_NAME,$username,$password,$firstname,$from_email,$from_name,$email);
      header("Location: request_password.php?pf=success");        
    }
  }
}

function ShowFormSuccess() {
  global $tpl, $succces;
  
  $tpl->assign('success',$_REQUEST['success']);
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->display('request_password.html');
}
//show page

if (empty($pf)) {
  ProcessRequestPassword();
  ShowFormRequestPassword();
}
elseif ($pf == 'browse') {
  ProcessRequestPassword();
  ShowFormRequestPassword();
}
elseif ($pf == 'success') {
  ShowFormSuccess();
  ProcessRequestPassword();
}
?>
