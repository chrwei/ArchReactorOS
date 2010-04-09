<?php
  include "../init.php";
  include "moneybookers.inc.php";
  
  
	ignore_user_abort(true);
	set_time_limit(300);
	


	$referers = array("www.moneybookers.com", "www.moneybookers.com", "moneybookers.com");
	$valid_referer = FALSE;
	if ($_SERVER['HTTP_REFERER']) {
	while (list(, $host) = @each($referers)) {
	  if (eregi($host, $_SERVER['HTTP_REFERER'])) {
		$valid_referer = TRUE;
	  }
	}
	}
	else {
	$valid_referer = TRUE;
	}
	
	if (!$valid_referer) {
	exit;
	}
		
		
//	if (md5($_POST['merchant_id'].$_POST['transaction_id'].strtoupper(md5('abcde')))) 
//	{

		if ($_POST['status']==2)
		{
		  $raw                = explode('&', urldecode($_POST['custom']));
      $val_product_id     = $raw[0];
      $val_email          = $raw[1];
      $val_username       = $raw[2];
      $val_password       = $raw[3];
      $val_firstname      = $raw[4];
      $val_lastname	      = $raw[5];
      $val_coupon_code    = $raw[6];
      $val_date_order	= $raw[7];
	  $log                = implode("\n",$_POST);
      $payment_gateway    = "Moneybookers";
      $gateway_data       = $pay_class->GetPaymentGatewayDetail("moneybookers");
      $moneybookers_email = $gateway_data['payment_gateway_account'];
      $payment_date       = time();
      
      ProcessIPN($moneybookers_email,$_POST['pay_to_email'],$_POST['amount'],$val_product_id,$val_username,$val_password,$val_firstname,$val_lastname,$val_email,$log,$payment_date,$payment_gateway,$_POST['transaction_id'],$val_coupon_code,$val_date_order);

	}
 //}
	

?>
