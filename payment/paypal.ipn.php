<?php
include "../init.php";
include "paypal.inc.php";
/*===================================================
Your paypal email address
===================================================*/
ignore_user_abort(true);
set_time_limit(300);

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) 
{
  $value = urlencode(stripslashes($value));
  $req  .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

if($enable_paypal_sandbox) 
{
  $fp = fsockopen ('www.sandbox.paypal.com', 80, $errno, $errstr, 30);
}
else 
{
  $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
}

if ($fp) 
{
  fputs ($fp, $header . $req);
  while (!feof($fp)) 
  {
    $res = fgets ($fp, 1024);
    if (strcmp ($res, "VERIFIED") == 0) 
    {
      // get the custom value
      $raw              = explode('&', $_POST['custom']);
      $val_product_id   = $raw[0];
      $val_email        = $raw[1];
      $val_username     = $raw[2];
      $val_password     = $raw[3];
      $val_firstname    = $raw[4];
      $val_lastname	    = $raw[5];
      $val_coupon_code  = $raw[6];
      $val_date_order	= $raw[7];
      $log              = implode("\n",$_POST);
      $payment_gateway  = "Paypal Payment";
      $gateway_data     = $pay_class->GetPaymentGatewayDetail("paypal_payments");
      $paypal_email     = $gateway_data['payment_gateway_account'];
      $payment_date     = time();
      ProcessIPN($paypal_email,$_POST['business'],$_POST['mc_gross'],$val_product_id,$val_username,$val_password,$val_firstname,$val_lastname,$val_email,$log,$payment_date,$payment_gateway,$_POST['invoice'],$val_coupon_code,$val_date_order);
      
    }
    else if (strcmp ($res, "INVALID") == 0) 
    {
      // log for manual investigation
    }
  }
  fclose ($fp);
}
?>
