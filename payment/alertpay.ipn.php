<?php
include "../init.php";

$enable = "1";

$ap_PurchaseType    = $_POST['ap_purchasetype'];	

if (strtolower($ap_PurchaseType) == "subscription")
{
  $gateway_data    = $pay_class->GetPaymentGatewayDetail("alertpay_subscribe");
  $payment_gateway = "Alertpay Subscribe";
}
else
{
  $gateway_data    = $pay_class->GetPaymentGatewayDetail("alertpay");
  $payment_gateway = "Alertpay";
}

$alertpay_account         = $gateway_data['payment_gateway_account'];
$list_alertpay_account    = explode("&", $alertpay_account);
$payalert_email           = $list_alertpay_account[0];
$payalert_security_code   = $list_alertpay_account[1];

//$ap_SecurityCode;

$ap_ReferenceNumber = $_POST['ap_referencenumber'];
$ap_TrialAmount     = $_POST['ap_trialamount'];
$ap_Status          = $_POST['ap_status'];	

setSubscriptionVariables();

if ($_POST['ap_securitycode'] != $payalert_security_code)
{
  echo "The Data is NOT sent by AlertPay. ";
  echo "Take appropriate action";
}
else
{
  if ($ap_PurchaseType == "Subscription")
  {
    setSubscriptionVariables();
  }
  
  if (strlen($ap_ReferenceNumber) == 0 && $ap_TrialAmount != "0")
	{
  	echo "Invalid reference number. The reference number is invalid because the ap_ReferenceNumber doesn't";
		echo " contain a value and the ap_TrialAmount is not equal to 0.";
	}
	else
	{
		if ($ap_Status == "Success")
		{
			// The is not a free trial and ap_TrialAmount contains some amount and the
			// ap_ReferenceNumber contains a valid transaction reference number.
			$raw             = explode('&', urldecode($_POST['apc_1']));
			$val_product_id  = $raw[0];
			$val_email    	 = $raw[1];
			$val_username    = $raw[2];
			$val_password    = $raw[3];
			$val_firstname	 = $raw[4];
			$val_lastname	   = $raw[5];
			$val_coupon_code = $raw[6];
		    $val_date_order	= $raw[7];
			$log             = implode("\n",$_POST);
			$invoice_id      = getInvoiceId();
      		$payment_date    = time();
      
      ProcessIPN($payalert_email,$_POST['ap_merchant'],$_POST['ap_amount'],$val_product_id,$val_username,$val_password,$val_firstname,$val_lastname,$val_email,$log,$payment_date,$payment_gateway,$invoice_id,$val_coupon_code,$val_date_order);
		}
		else
		{
			echo "Transaction cancelled means seller explicitely cancelled the subscription or AlertPay ";							 	
			echo "cancelled or it was cancelled since buyer didnt have enough money after resheduling after two times.";
			echo "Take Action appropriately";
			if ($ap_PurchaseType == "Subscription")
			{
				setSubscriptionVariables();
			} 
      else
			{
			
			}
		}
	}
}

function setSubscriptionVariables()
{
  $ap_SubscriptionReferenceNumber = $_POST['ap_subscriptionreferencenumber'];
  $ap_TimeUnit                    = $_POST['ap_timeunit'];
  $ap_PeriodLength                = $_POST['ap_periodlength'];
  $ap_PeriodCount                 = $_POST['ap_periodcount'];
  $ap_NextRunDate                 = $_POST['ap_nextrundate'];
  $ap_TrialTimeUnit               = $_POST['ap_trialtimeunit'];
  $ap_TrialPeriodLength           = $_POST['ap_trialperiodlength'];
  $ap_TrialAmount                 = $_POST['ap_trialamount'];
}
?>
