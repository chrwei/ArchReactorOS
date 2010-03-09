<?php
include dirname(__FILE__).'/../init.php';

function SendMail() {
  global $order,$mail;
  
  $orders = $order->GetExpireOrders();
  
  foreach ($orders as $value) {
    
    $date_expire          = $value['date_expire'];
    $date_reminder        = $value['date_expire'] - (CFG_NOTIFY_EXPIRE*24*60*60);
    $date_now             = time();
    $date_last_mail_sent  = $value['last_email_sent'];
    $username             = $value['username'];
    $firstname            = $value['firstname'];
    $lastname             = $value['lastname'];
    $email                = $value['email'];
    $from_email           = CFG_NOTIFY_EMAIL;
    $from_name            = CFG_NOTIFY_FROM;
    $product_name         = $value['name'];
    $product_desc         = $value['description'];
    $product_price        = $value['price'];
    $product_expire       = date("F j, Y",$value['date_expire']);
    
    if($date_reminder < $date_now  && $date_last_mail_sent < $date_reminder)
    {
      $mail->ExpireNotificationEmail(CFG_SITE_NAME,$username,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$product_expire,$email);
      $order->UpdateLastEmailSent($value['order_id'],time());
    }
    elseif($date_expire < $date_now && $date_last_mail_sent < $date_expire)
    {
      $mail->ExpireAccountEmail(CFG_SITE_NAME,CFG_SITE_URL,$username,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$product_expire,$email);
      $order->UpdateLastEmailSent($value['order_id'],time());
    }
  }
}
/************************************************************************************************
Section : Main Cron
************************************************************************************************/
SendMail();

?>
