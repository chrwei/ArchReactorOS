<?
include "../init.php";
include "2co.inc.php";

if (!defined('2CO_RETURN')) 
{
  exit;
}

// get the custom value
$raw               = explode('&', urldecode($_POST['custom']));
$val_product_id    = $raw[0];
$val_email    	   = $raw[1];
$val_username      = $raw[2];
$val_password      = $raw[3];
$val_firstname     = $raw[4];
$val_lastname	     = $raw[5];
$val_coupon_code   = $raw[6];
$val_date_order	= $raw[7];
$payment_gateway   = "2CO";
$log               = "payment ".$_POST['item_name']." at 2CO";
$gateway_data      = $pay_class->GetPaymentGatewayDetail("2co");
$co_account        = $gateway_data['payment_gateway_account'];
$list_co_account   = explode("&", $co_account);
$co_sid  				   = $list_co_account[0];
$co_secret     		 = $list_co_account[1];      
$payment_date      = time();
ProcessIPN($co_sid,$_POST['sid'],$_POST['total'],$val_product_id,$val_username,$val_password,$val_firstname,$val_lastname,$val_email,$log,$payment_date,$payment_gateway,$_POST['invoice_id'],$val_coupon_code,$val_date_order);
  
?>
