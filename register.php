<?php
include 'init.php';

$pf = $_REQUEST['pf'];

function ShowFormRegister() {
	global $tpl, $error_list, $product, $products, $paypal_payments_status, $paypal_subscribe_status, $co_status, $co_subscribe_status, $alertpay_status, $alertpay_subscribe_status, $moneybookers_status, $currency_code;
		
	$products = $product->GetAllProducts();
	$i				= 0;
	foreach ($products as $value) {
		
		$product_data[$i]['product_id'] = $value['product_id'];
		$product_data[$i]['name']			 = $value['name'];
		$product_data[$i]['description']= $value['description'];
		$product_data[$i]['price']			= $value['price'];	
		$product_data[$i]['duration']      = $value['duration'];
		switch($value['duration_unit'])
		{
			case 'd':
				$product_data[$i]['unit']  = 'Day(s)';
				break;
			case 'm':
				$product_data[$i]['unit']  = 'Month(s)';
				break;
			case 'y':
				$product_data[$i]['unit']  = 'Year(s)';
				break;
		}
		$i++;
	}
	
	$tpl->assign('paypal_payments_status',$paypal_payments_status);
	$tpl->assign('paypal_subscribe_status',$paypal_subscribe_status);
	$tpl->assign('co_status',$co_status);
	$tpl->assign('co_subscribe_status',$co_subscribe_status);
	$tpl->assign('alertpay_status',$alertpay_status);
	$tpl->assign('alertpay_subscribe_status',$alertpay_subscribe_status);
	$tpl->assign('moneybookers_status',$moneybookers_status);
	
	$tpl->assign('product_data',$product_data);
	$tpl->assign('date_order', (trim($_REQUEST['date_order'])=='' ? date('m') : $_REQUEST['date_order'])); 
	$tpl->assign('error',$error_list);
	$tpl->assign('product_id', $_REQUEST['product_id']);
	$tpl->assign('coupon_code', $_REQUEST['coupon_code']);
	$tpl->assign('username', $_REQUEST['username']);
	$tpl->assign('firstname', $_REQUEST['firstname']);
	$tpl->assign('password', $_REQUEST['password']);
	$tpl->assign('repassword', $_REQUEST['repassword']);
	$tpl->assign('lastname', $_REQUEST['lastname']);
	$tpl->assign('payment_gateway', $_REQUEST['payment_gateway']);
	$tpl->assign('currency_code', $currency_code);
	$tpl->assign('email', $_REQUEST['email']);
	$tpl->assign('address1',$_REQUEST['address1']);
	$tpl->assign('address2',$_REQUEST['address2']);
	$tpl->assign('city',$_REQUEST['city']);
	$tpl->assign('state',$_REQUEST['state']);
	$tpl->assign('zip',$_REQUEST['zip']);
	$tpl->assign('phone',$_REQUEST['phone']);
	$tpl->display('register.html');
}

function ProcessFormRegister() {
	global $tpl, $user, $error_list, $order, $mail, $product, $coupon, $pay_class, $currency_code, $currency_unit;
	
	$product_id			 = $_REQUEST['product_id'];
	$username = stripslashes($_REQUEST['username']);
	$password = stripslashes($_REQUEST['password']);
	$repassword = stripslashes($_REQUEST['repassword']);
	$firstname = stripslashes($_REQUEST['firstname']);
	$lastname = stripslashes($_REQUEST['lastname']);
	$email = stripslashes($_REQUEST['email']);
	$address1 = stripslashes($_REQUEST['address1']);
	$address2 = stripslashes($_REQUEST['address2']);
	$city = stripslashes($_REQUEST['city']);
	$state = stripslashes($_REQUEST['state']);
	$zip = stripslashes($_REQUEST['zip']);
	$phone = stripslashes($_REQUEST['phone']);
	
	$payment_gateway	= $_REQUEST['payment_gateway'];
	$products				 = $product->GetProduct($product_id);
	$date_order_mo  = $_REQUEST['date_order'];
	
	//figure out what the timestamp for the month should be
	if ($date_order_mo < date('m'))
	{ //year+1
		$date_order = strtotime((date('Y')+1).'/'.$date_order_mo.'/1');
	}
	else
	{
		$date_order = strtotime(date('Y').'/'.$date_order_mo.'/1');
	}
		
	$i = 0;
	
	if($product_id == ""){
		$error_list[$i] = "Please select membership type";
		$i++;
	}
	if($username == ""){
		$error_list[$i] = "Username is required";
		$i++;
	}
	if($password == ""){
		$error_list[$i] = "Password is required";
		$i++;
	}
	if($repassword == ""){
		$error_list[$i] = "Retype password is required";
		$i++;
	}
	if($firstname == ""){
		$error_list[$i] = "Firstname is required";
		$i++;
	}
	if($lastname == ""){
		$error_list[$i] = "Lastname is required";
		$i++;
	}
	if($email == ""){
		$error_list[$i] = "Email is required";
		$i++;
	}
	if($address1 == ""){
		$error_list[$i] = _("Address is required");
		$i++;
	}
	if($city == ""){
		$error_list[$i] = _("City is required");
		$i++;
	}
	if($state == ""){
		$error_list[$i] = _("State is required");
		$i++;
	}
	if($zip == ""){
		$error_list[$i] = _("Zip is required");
		$i++;
	}
	if($payment_gateway == "" && $products['price'] > 0){
		$error_list[$i] = "Please select payment gateway";
		$i++;
	}
	if($user->CheckUser($username,$email)){
		$error_list[$i] = "Username or email already exist";
		$i++;		
	}
	if($repassword != $password){
		$error_list[$i] = "password doesnt match";
		$i++;
	}
	if(!IsEmailAddress($email)){
		$error_list[$i] = "email is not valid";
		$i++;
	}
	
	if(!is_array($error_list)) {
		$product_id	 = $_REQUEST['product_id'];
		$price				= $products['price'];
		$name				 = $products['name'];
		$description	= $products['description'];
		$item_name		= $name." ( ".$description." )"; 
		$invoice_id	 = getInvoiceId();
		//**** for coupon ****//
		
		if($coupon_code != "")
		{
			$discount_data = $coupon->CheckProductDiscount($coupon_code, $product_id);
			if(!$discount_data)
			{
				
			}
			else
			{
				$percentage = strrpos($discount_data['coupon_value'], "%");
				if($percentage)
				{
					$percent = str_replace("%", "", $discount_data['coupon_value']);
					$coupon_value_type = "percentage";
					$percentage_coupon_value = $percent;
					$net_price = $discount_data['price'] - ($discount_data['price']*($percent/100));
				}
				else
				{
					$coupon_value_type = "price";
					$price_coupon_value = $discount_data['coupon_value'];
					$net_price = $discount_data['price']-$discount_data['coupon_value'];
				}
				$price = $net_price;
				if($price < 0)
				{
					$price = 0;
				}
			}
		}
				
		$user_id = $user->Add($username, $password, $firstname, $lastname,$email, $address1, $address2, $city, $state, $zip, $phone);
		if($price == 0)
		{
			$order_id = $order->AddOrder($user_id,$product_id,$date_order);
			
			$order_data			 = $order->GetOrder($order_id);
			$product_name		 = $order_data['name'];
			$product_desc		 = $order_data['description'];
			$product_price		= $order_data['price'];
			$product_expire	 = date("Y-m-d",$order_data['date_expire']);
			$from_email = CFG_NOTIFY_EMAIL;
			$from_name = CFG_NOTIFY_FROM;						
			
			$mail->ConfirmOrderEmail(CFG_SITE_NAME,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$product_expire,$email);
			$mail->ReceivedOrderEmail(CFG_SITE_NAME,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$date_order,$product_expire,CFG_SITE_MAIL);
			
			$order->UpdateLastEmailSent($order_id,time());
			$login = $user->Login($username, $password, $expire);
			header("Location: index.php");
		}
		else
		{
			$currency_code	= $currency_code;//$currency_unit ===== GLOBAL VARIABLE
			$return_url		 = CFG_SITE_URL;
			$cancel_url		 = CFG_SITE_URL;
			$total					= $price;
			$custom				 = "{$product_id}&{$email}&{$username}&{$password}&{$firstname}&{$lastname}&{$coupon_code}&{$date_order}";
			if($payment_gateway == "co" || $payment_gateway == "co_subscribe")
				$gateway_data	 = $pay_class->GetPaymentGatewayDetail("2".$payment_gateway); 
			else
				$gateway_data	 = $pay_class->GetPaymentGatewayDetail($payment_gateway); 
			
			
				
			if($payment_gateway == "paypal_payments")
			{
				$notify_url						 = CFG_SITE_URL.'/payment/paypal.ipn.php';
				$paypal_payments_email	= $gateway_data['payment_gateway_account'];
				$paypal_email					 = $paypal_payments_email;
				include 'payment/paypal.php';
			}
			elseif($payment_gateway == "paypal_subscribe")
			{
				$notify_url							 = CFG_SITE_URL.'/payment/paypal-subscribe.ipn.php';
				$paypal_subscribe_email	 = $gateway_data['payment_gateway_account'];
				$listing_period					 = $products['duration'];
				$listing_period_code			= strtoupper($products['duration_unit']);
				$paypal_email						 = $paypal_subscribe_email;
				include 'payment/paypal-subscribe.php';
			}
			elseif($payment_gateway == "co")
			{
				$notify_url							 = CFG_SITE_URL.'/payment/2co.ipn.php';
				$co_account							 = $gateway_data['payment_gateway_account'];
				$list_co_account					= explode("&", $co_account);
				$co_sid										= $list_co_account[0];
				$co_secret		 						= $list_co_account[1];
				$co_recurring							= 0; //set subscribe
				include 'payment/2co.php';
			}
			elseif($payment_gateway == "co_subscribe")
			{
				$notify_url							 = CFG_SITE_URL.'/payment/2co-subscribe.ipn.php';
				$co_account							 = $gateway_data['payment_gateway_account'];
				$list_co_account					= explode("&", $co_account);
				$co_sid										= $list_co_account[0];
				$co_secret		 						= $list_co_account[1];
				$co_recurring							= 1; //set subscribe
				$co_prod_id							 = $product_id;
				include 'payment/2co-subscribe.php';
			}
			elseif($payment_gateway == "alertpay")
			{
				$notify_url							 = CFG_SITE_URL.'/payment/alertpay.ipn.php';
				$alertpay_account				 = $gateway_data['payment_gateway_account'];
				$list_alertpay_account		= explode("&", $alertpay_account);
				$payalert_email					 = $list_alertpay_account[0];
				$payalert_security_code	 = $list_alertpay_account[1];
				$ap_currency							= $currency_code;
				$ap_purchasetype					= "service"; //lainnya subscription & service
				include 'payment/alertpay.php';
			}
			elseif($payment_gateway == "alertpay_subscribe")
			{
				$notify_url											 = CFG_SITE_URL.'/payment/alertpay.ipn.php';
				$alertpay_subscribe_account			 = $gateway_data['payment_gateway_account'];
				$list_alertpay_subscribe_account	= explode("&", $alertpay_subscribe_account);
				$payalert_email									 = $list_alertpay_subscribe_account[0];
				$payalert_security_code					 = $list_alertpay_subscribe_account[1];
				$ap_currency											= $currency_code;
				$ap_purchasetype									= "Subscription"; //lainnya subscription & service
				if(strtolower($products['duration_unit']) == "d")
					$ap_timeunit							= "Day";
				elseif(strtolower($products['duration_unit']) == "m")
					$ap_timeunit							= "Month";
				elseif(strtolower($products['duration_unit']) == "y")
					$ap_timeunit							= "Year";

				$ap_periodlength					= $products['duration'];
				include 'payment/alertpay-subscribe.php';
			}
			elseif($payment_gateway == "moneybookers")
			{
				$notify_url						 = CFG_SITE_URL.'/payment/moneybookers.ipn.php';
				$moneybookers_email		 = $gateway_data['payment_gateway_account'];
				include 'payment/moneybookers.php';
			}
		}
	}
	else 
	{
		ShowFormRegister();	
	}
}
/***********************************************************************************************
Main
***********************************************************************************************/

if($banned->BanIp() || $banned->BanCountry())
{
	header("Location: ban_confirm.php");
}

if (empty($pf)) {
	GetPaymentCurrency();
	GetPaymentGateway();
	ShowFormRegister();
}
elseif ($pf == 'reg') {
	GetPaymentCurrency();
	GetPaymentGateway();
	ProcessFormRegister();
}

?>
