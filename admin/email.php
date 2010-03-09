<?php
include '../init.php';
$pf = $_REQUEST['pf'];

function ShowFormAllEmailTpl() {
  global $tpl, $emails, $success;
  
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('success',$_REQUEST['success']);
  $tpl->assign('emails',$emails);
  $tpl->display('admin/email.html');
}


function ShowAllEmailTpl() {
  global $db,$mail,$emails;
  
  $email_data = $mail->GetAllEmailTemplates();
  
  //print_r($email_data);
  $i = 0;
  
  foreach ($email_data as $value) {
    $emails[$i]['no']       = $i+1; 
    $emails[$i]['email_id'] = $value['email_id'];
    $emails[$i]['name']     = $value['name'];
    $emails[$i]['subject']  = $value['subject'];
    $emails[$i]['body']     = $value['body'];
    if($i % 2 != 0)
    {
      $emails[$i]['color'] = '#f7f7f7';
    }
    else
    {
      $emails[$i]['color'] = '#ffffff';
    }       
    $i++;    
  } 
  
}

function ShowFormDetailEmailTpl() {
  global $tpl,$email_id,$error_list,$name,$subject,$body, $success;

  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('success',$success);
  $tpl->assign('error',$error_list);
  $tpl->assign('email_id',$email_id);
  $tpl->assign('name', $name);
  $tpl->assign('subject', $subject);
  $tpl->assign('body',$body);
  $tpl->display('admin/email.html');
}


function ShowDetailEmailTpl() {
  global $mail,$email_id,$name,$subject,$body,$error_list, $success;;
  
  $name     = $_REQUEST['name'];
  $process  = $_REQUEST['process'];    
  
  $email_data  = $mail->GetEmailTemplates($name);
  
  if ($process == 'edit') {
    $name       = stripslashes($_REQUEST['name']);
    $subject    = stripslashes($_REQUEST['subject']);
    $body       = stripslashes($_REQUEST['body']);
    $i = 0;
  
    if($name == "" || $subject == "" || $body == "" ){
      if($name == ""){
        $error_list[$i] = _("name is required");
        $i++;
      }
      if($subject == ""){
        $error_list[$i] = _("subject is required");
        $i++;
      }
      if($body == ""){
        $error_list[$i] = _("content is required");
        $i++;
      }
    }
    if(!is_array($error_list)) {
      $mail->Update($name, $subject,$body);
      $success = true;
    }
    else {
      //ShowFormDetailUser();  
    }
    
  }
  else {
    $name       = stripslashes($email_data['name']);
    $subject    = stripslashes($email_data['subject']);
    $body       = stripslashes($email_data['body']);    
  }   
}

function ShowFormAddEmailTpl() {
  global $tpl,$mail,$error_list;
  
  $tpl->assign('pf', $_REQUEST['pf']);
  $tpl->assign('process', $_REQUEST['process']);
  $tpl->assign('error', $error_list);
  $tpl->assign('name', stripslashes($_REQUEST['name']));
  $tpl->assign('subject',stripslashes($_REQUEST['subject']));
  $tpl->assign('body', stripslashes($_REQUEST['body']));
  
  $tpl->display('admin/email.html');
}

function AddEmailTpl() {
  global $mail,$tpl,$error_list;
 
    $name       = stripslashes($_REQUEST['name']);
    $subject    = stripslashes($_REQUEST['subject']);
    $body       = stripslashes($_REQUEST['body']);
    $i = 0;
  
    if($subject == "" || $body == "" ){
      if($subject == ""){
        $error_list[$i] = _("subject is required");
        $i++;
      }
      if($body == ""){
        $error_list[$i] = _("content is required");
        $i++;
      }
    }
    if(!is_array($error_list)) {

      $mail->Add($email_id, $name, $subject,$body);

      header("Location: email.php?pf=browse");
    }
    else {
      ShowFormAddEmailTpl();  
    }  

}

function DeleteEmailTpl($email_id_list) {
  global $mail, $tpl, $success;
  
  for($i=0;$i<=count($email_id_list);$i++)
  {
    $mail->Delete($email_id_list[$i]);
  }
  
  header("Location: email.php?pf=browse&success=true");
}

function ShowFormDeleteEmailTpl() {
  global $tpl;
  
  $tpl->assign('pf', $_REQUEST['pf']);
  $tpl->display('admin/email.html');
}

/*###########################################################
Section : Main
###########################################################*/
$user->AuthenticationAdmin();

if (empty($pf)) {
  ShowAllEmailTpl();
  ShowFormAllEmailTpl();
}
elseif ($pf == 'browse') {
  ShowAllEmailTpl();
  ShowFormAllEmailTpl();
}

elseif ($pf == 'detail') {
  ShowDetailEmailTpl();
  ShowFormDetailEmailTpl();
}
elseif ($pf == 'add') {
   if ($_REQUEST['process'] == 'add') {
    AddEmailTpl();
    //ShowFormAddEmailTpl();
  }
  else
  {
    //AddEmailTpl();
    ShowFormAddEmailTpl();
 }
}

elseif ($pf == 'delete') {
  DeleteEmailTpl($_REQUEST['delete']);
  ShowFormDeleteEmailTpl();
}


?>
