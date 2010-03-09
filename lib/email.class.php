<?php

class Email extends PHPMailer {
  
  function Add($email_id, $name, $subject,$body) {
    global $db;
    
    $record["name"]     = $name;
    $record["subject"]  = $subject;
    $record["body"]     = $body; 
    $db->AutoExecute('email_templates', $record, 'INSERT');    
        
  }
  
  function EmailConfirmation($from,$fromName,$subject,$body,$to) {
    
    $mailer = new PHPMailer();
    $mailer->From       = $from;
    $mailer->FromName   = $fromName;    
    $mailer->AddAddress($to);  
    $mailer->Subject    = $subject;
    $mailer->Body       = $body;    
    
    return $mailer->Send();
  }  
  
  function GetEmailTemplates($tpl_name) {
    global $db;
    
    $query  = "select * from email_templates where name ='".mysql_escape_string($tpl_name)."'";
    $result = $db->Execute($query);
    return $result->FetchRow();
  }
  
  function GetAllEmailTemplates() {
    global $db;
    
    $query  = "select * from email_templates order by email_id";
    $result = $db->Execute($query);
    $rows   = $result->GetRows();
    return $rows;
  }
  
  function Update($name, $subject, $body) {
    global $db;
    $record["name"]     = $name;
    $record["subject"]  = $subject;
    $record["body"]     = $body; 
    $db->AutoExecute('email_templates', $record, 'UPDATE', "name ='".mysql_escape_string($name)."'");    
    
  }
  
  function GetEmailTpl($email_id) {
    global $db;
    
    $query = "select * from email_templates where email_id= ".intval($email_id);
    $result = $db->Execute($query);
    return $result->FetchRow();
  }
  
  function Delete($email_id) {
    global $db;
    $query = "delete from email_templates where email_id= ".intval($email_id);
    $rows   =$db->Execute($query);
  }
  
  function RequestPasswordEmail($site_name,$username,$password,$firstname,$from_email,$from_name,$to_email)
  {
    $mail_tpl   = $this->GetEmailTemplates('request_password');
    $subject    = $mail_tpl['subject'];
    $body       = $mail_tpl['body'];
    $body       = str_replace('%site_name%',$site_name,$body);
    $body       = str_replace('%firstname%',$firstname,$body);
    $body       = str_replace('%username%',$username,$body);
    $body       = str_replace('%password%',$password,$body);
    $body       = str_replace('%from_name%',$from_name,$body);
    $this->SendMail($from_email,$from_name,$subject,$body,$to_email);
  }
  
  function ConfirmAccountEmail($site_name,$username,$firstname,$from_email,$from_name,$to_email)
  {
    $mail_tpl   = $this->GetEmailTemplates('account_confirm');
    $subject    = $mail_tpl['subject'];
    $body       = $mail_tpl['body'];
    $body       = str_replace('%site_name%',$site_name,$body);
    $body       = str_replace('%firstname%',$firstname,$body);
    $body       = str_replace('%username%',$username,$body);
    $body       = str_replace('%from_name%',$from_name,$body);
    $this->SendMail($from_email,$from_name,$subject,$body,$to_email);
  }
  
  function ConfirmOrderEmail($site_name,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$product_expire,$to_email)
  {
    $mail_tpl   = $this->GetEmailTemplates('confirm_order');   
    $subject    = $mail_tpl['subject'];
    $body       = $mail_tpl['body'];
    $body       = str_replace('%site_name%',$site_name,$body);
    $body       = str_replace('%firstname%',$firstname,$body);
    $body       = str_replace('%lastname%',$lastname,$body);
    $body       = str_replace('%product_name%',$product_name,$body);
    $body       = str_replace('%product_desc%',$product_desc,$body);
    $body       = str_replace('%product_price%',$product_price,$body);
    $body       = str_replace('%product_expire%',$product_expire,$body);
    $body       = str_replace('%from_name%',$from_name,$body);
    
    $this->SendMail($from_email,$from_name,$subject,$body,$to_email);
  }
  
  function ReceivedOrderEmail($site_name,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$date_order,$product_expire,$to_email)
  {
    $mail_tpl   = $this->GetEmailTemplates('received_new_order');   
    $subject    = $mail_tpl['subject'];
    $body       = $mail_tpl['body'];
    $date_order = date("Y-m-d",$date_order);
    $body       = str_replace('%site_name%',$site_name,$body);
    $body       = str_replace('%date_order%',$date_order,$body);
    $body       = str_replace('%firstname%',$firstname,$body);
    $body       = str_replace('%lastname%',$lastname,$body);
    $body       = str_replace('%product_name%',$product_name,$body);
    $body       = str_replace('%product_desc%',$product_desc,$body);
    $body       = str_replace('%product_price%',$product_price,$body);
    $body       = str_replace('%product_expire%',$product_expire,$body);
    $body       = str_replace('%date_expire%',$product_expire,$body);
    $body       = str_replace('%from_name%',$from_name,$body);
    $this->SendMail($from_email,$from_name,$subject,$body,$to_email);
  }
  
  function ExpireNotificationEmail($site_name,$username,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$product_expire,$url,$to_email)
  {
    $mail_tpl   = $this->GetEmailTemplates('expire_notification');   
    $subject    = $mail_tpl['subject'];
    $body       = $mail_tpl['body'];
    $body       = str_replace('%site_name%',$site_name,$body);
    $body       = str_replace('%username%',$username,$body);
    $body       = str_replace('%firstname%',$firstname,$body);
    $body       = str_replace('%lastname%',$lastname,$body);
    $body       = str_replace('%product_name%',$product_name,$body);
    $body       = str_replace('%product_desc%',$product_desc,$body);
    $body       = str_replace('%product_price%',$product_price,$body);
    $body       = str_replace('%product_expire%',$product_expire,$body);
    $body       = str_replace('%date_expire%',$product_expire,$body);
    $body       = str_replace('%url%',$url,$body);
    $body       = str_replace('%from_name%',$from_name,$body);
    $this->SendMail($from_email,$from_name,$subject,$body,$to_email);
  }

  function ExpireAccountEmail($site_name,$site_url,$username,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$product_expire,$url,$to_email)
  {
    $mail_tpl   = $this->GetEmailTemplates('account_expire');   
    $subject    = $mail_tpl['subject'];
    $body       = $mail_tpl['body'];
    $body       = str_replace('%site_name%',$site_name,$body);
    $body       = str_replace('%site_url%',$site_url,$body);
    $body       = str_replace('%username%',$username,$body);
    $body       = str_replace('%firstname%',$firstname,$body);
    $body       = str_replace('%lastname%',$lastname,$body);
    $body       = str_replace('%product_name%',$product_name,$body);
    $body       = str_replace('%product_desc%',$product_desc,$body);
    $body       = str_replace('%product_price%',$product_price,$body);
    $body       = str_replace('%product_expire%',$product_expire,$body);
    $body       = str_replace('%date_expire%',$product_expire,$body);
    $body       = str_replace('%url%',$url,$body);
    $body       = str_replace('%from_name%',$from_name,$body);
    $this->SendMail($from_email,$from_name,$subject,$body,$to_email);
  }
  
  function SendMail($from_email,$from_name,$subject,$body,$to_email)
  {
    $mailer = new PHPMailer();
    $mailer->From       = $from_email;
    $mailer->FromName   = $from_name;    
    $mailer->AddAddress($to_email);  
    $mailer->Subject    = $subject;
    $mailer->Body       = $body;    
    $mailer->Send();    
  }
  
  function CreateMailingList()
  {
    global $db, $user, $order;
    
    $user_data =  $user->GetAllUsers();
    $i = 0;
    foreach($user_data as $val_user)
    {
      $user_order_data = $order->CheckUserOrder($val_user['user_id']);
      $status = "inactive";
      foreach($user_order_data as $val_order)
      {
        if($val_order['date_expire'] >= time())
        {
          $status = "active";
        }
      }
      $mailing_data[$i]['email']      = $val_user['email'];
      $mailing_data[$i]['firstname']  = $val_user['firstname'];
      $mailing_data[$i]['lastname']   = $val_user['lastname'];
      $mailing_data[$i]['status']     = $status;
      $i++;
    }
    
    return $mailing_data;
  }
}
?>
