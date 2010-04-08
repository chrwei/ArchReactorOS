<?php
include 'init.php';
$user->AuthenticationUser();
$pf = $_REQUEST['pf'];
$process = $_REQUEST['process'];

function ShowFormAddOrder() {
	global $tpl, $product, $user, $error_list, $cash_payments_status, $paypal_payments_status, $paypal_subscribe_status, $co_status, $co_subscribe_status, $alertpay_status, $alertpay_subscribe_status, $moneybookers_status, $currency_code;
	
	$tpl->assign('cash_payments_status',$cash_payments_status);
	$tpl->assign('paypal_payments_status',$paypal_payments_status);
	$tpl->assign('paypal_subscribe_status',$paypal_subscribe_status);
	$tpl->assign('co_status',$co_status);
	$tpl->assign('co_subscribe_status',$co_subscribe_status);
	$tpl->assign('alertpay_status',$alertpay_status);
	$tpl->assign('alertpay_subscribe_status',$alertpay_subscribe_status);
	$tpl->assign('moneybookers_status',$moneybookers_status);
	
	$users = $user->CheckUserActive($_SESSION['SESSION_USERNAME']);
	$user_id = $users['user_id'];
	$username = $users['username'];
	$firstname = $users['firstname'];
	$lastname = $users['lastname'];
	$email = $users['email'];

	$products = $product->GetAllProducts(($users['path'] == 'F'));
	$i = 0;
	foreach ($products as $value) { 
		$product_data[$i]['product_id'] = $value['product_id'];
		$product_data[$i]['name'] = $value['name'];
		$product_data[$i]['description'] = $value['description'];
		$product_data[$i]['price'] = $value['price'];	
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
	$tpl->assign('error',$error_list);	
	$tpl->assign('product_id',$_REQUEST['product_id']);
	$tpl->assign('payment_gateway',$_REQUEST['payment_gateway']);
	$tpl->assign('user_id',$user_id);
	$tpl->assign('coupon_code',$_REQUEST['coupon_code']);
	$tpl->assign('currency_code',$currency_code);
	$tpl->assign('username',$username);
	$tpl->assign('firstname',$firstname);
	$tpl->assign('lastname',$lastname);	 
	$tpl->assign('email',$email);		 
	$tpl->assign('product_data',$product_data);	
	$tpl->assign('date_order', ($_REQUEST['date_order'] ? $_REQUEST['date_order'] : date('m'))); 
	$tpl->assign('pf',$_REQUEST['pf']);
	$tpl->display("order.html");
}

function ProcessAddOrder() {
	global $tpl, $product, $user, $order, $error_list, $mail, $coupon, $pay_class, $currency_code, $currency_unit, $dispatcher;
	
	$users = $user->CheckUserActive($_SESSION['SESSION_USERNAME']);
	$user_id = $users['user_id'];
	$username = $users['username'];
	$firstname = $users['firstname'];
	$lastname = $users['lastname'];
	$email = $users['email'];
	$password = $users['password'];

	$payment_gateway = $_REQUEST['payment_gateway'];
	$product_id = $_REQUEST['product_id'];
	$payment_gateway = $_REQUEST['payment_gateway'];
	$products = $product->GetProduct($product_id);
	$coupon_code = $_REQUEST['coupon_code'];
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
	if (!$order->CheckActiveOrder($product_id,$user_id,$date_order)) {
		$error_list[$i] = "Order already active for the selected month";
		$i++;			
	}
	else if($product_id =="")
	{
		$error_list[$i] = "Please choose membership type";
		$i++;	
	}
	elseif($payment_gateway == "" && $products['price'] > 0)
	{
		$error_list[$i] = "Please select payment gateway";
		$i++;
	}

	if(!is_array($error_list)) {
		$price = $products['price'];
		$name = $products['name'];
		$description = $products['description'];
		$item_name = $name." ( ".$description." )"; 
		$invoice_id = getInvoiceId();
		
		// let's trigger a hook
		$dispatcher->trigger("newInvoice",$invoice_id);
		
		//**** for coupon ****//
		if($coupon_code != "")
		{
			$discount_data = $coupon->CheckProductDiscount($coupon_code, $product_id);
			if(!$discount_data)
			{
				$error_list[$i] = "Discount not found";
				$i++;
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
				
		if($price == 0 || $payment_gateway == "cash_payments")
		{
			$user_exist = $user->CheckUserActive($username);
			if($user_exist['user_id']=="")
			{
			  $user_id = $user->Add($username,$password,$password,$firstname,$lastname,$email);
			}
			else
			{
			  $user_id = $user_exist['user_id'];
			}
			$order_id = $order->AddOrder($user_id,$product_id,$date_order);
			
			$order_data = $order->GetOrder($order_id);
			$product_name = $order_data['name'];
			$product_desc = $order_data['description'];
			$product_price = $order_data['price'];
			$product_expire = date("Y-m-d",$order_data['date_expire']);
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
			$currency_code = $currency_code;//$currency_unit ===== GLOBAL VARIABLE
			$return_url = CFG_SITE_URL;
			$cancel_url = CFG_SITE_URL;
			$total = $price;
			$custom = "{$product_id}&{$email}&{$username}&{$password}&{$firstname}&{$lastname}&{$coupon_code}&{$date_order}";
			if($payment_gateway == "co" || $payment_gateway == "co_subscribe")
				$gateway_data = $pay_class->GetPaymentGatewayDetail("2".$payment_gateway); 
			else
				$gateway_data = $pay_class->GetPaymentGatewayDetail($payment_gateway); 
			
			switch($payment_gateway){
				case 'paypal_payments':
					$notify_url = CFG_SITE_URL.'/payment/paypal.ipn.php';
					$paypal_payments_email = $gateway_data['payment_gateway_account'];
					$paypal_email = $paypal_payments_email;
					include 'payment/paypal.php';
				break;
				case ' paypal_subscribe':
					$notify_url = CFG_SITE_URL.'/payment/paypal-subscribe.ipn.php';
					$paypal_subscribe_email = $gateway_data['payment_gateway_account'];
					$listing_period = $products['duration'];
					$listing_period_code = strtoupper($products['duration_unit']);
					$paypal_email = $paypal_subscribe_email;
					include 'payment/paypal-subscribe.php';
				break;
				case 'co':
					$notify_url = CFG_SITE_URL.'/payment/2co.ipn.php';
					$co_account = $gateway_data['payment_gateway_account'];
					$list_co_account = explode("&", $co_account);
					$co_sid = $list_co_account[0];
					$co_secret		  = $list_co_account[1];
					$co_recurring = 0; //set subscribe
					include 'payment/2co.php';
				break;
				case 'co_subscribe':
					$notify_url = CFG_SITE_URL.'/payment/2co-subscribe.ipn.php';
					$co_account = $gateway_data['payment_gateway_account'];
					$list_co_account = explode("&", $co_account);
					$co_sid = $list_co_account[0];
					$co_secret		  = $list_co_account[1];
					$co_recurring = 1; //set subscribe
					$co_prod_id = $product_id;
					include 'payment/2co-subscribe.php';
				break;
				case 'alertpay':
					$notify_url = CFG_SITE_URL.'/payment/alertpay.ipn.php';
					$alertpay_account = $gateway_data['payment_gateway_account'];
					$list_alertpay_account = explode("&", $alertpay_account);
					$payalert_email = $list_alertpay_account[0];
					$payalert_security_code = $list_alertpay_account[1];
					$ap_currency = $currency_code;
					$ap_purchasetype = "service"; //lainnya subscription & service
					include 'payment/alertpay.php';
				break;
				case 'alertpay_subscribe':
					$notify_url = CFG_SITE_URL.'/payment/alertpay-subscribe.ipn.php';
					$alertpay_subscribe_account = $gateway_data['payment_gateway_account'];
					$list_alertpay_subscribe_account = explode("&", $alertpay_subscribe_account);
					$payalert_email = $list_alertpay_subscribe_account[0];
					$payalert_security_code = $list_alertpay_subscribe_account[1];
					$ap_currency = $currency_code;
					$ap_purchasetype = "subscription"; //lainnya subscription & service
					if(strtolower($products['duration_unit']) == "d")
						$ap_timeunit = "Day";
					elseif(strtolower($products['duration_unit']) == "m")
						$ap_timeunit = "Month";
					elseif(strtolower($products['duration_unit']) == "y")
						$ap_timeunit = "Year";

					$ap_periodlength = $products['duration'];
					include 'payment/alertpay-subscribe.php';
				break;
				case 'moneybookers':
					$notify_url = CFG_SITE_URL.'/payment/moneybookers.ipn.php';
					$moneybookers_email = $gateway_data['payment_gateway_account'];
					include 'payment/moneybookers.php';
				break;
			}
		}
	}
	else {
		ShowFormAddOrder();	
	}
}

function ShowFormRenewOrder() {
	global $tpl, $product, $user, $error_list, $paypal_payments_status, $paypal_subscribe_status, $co_status, $co_subscribe_status, $alertpay_status, $alertpay_subscribe_status, $moneybookers_status, $currency_code;
	
	$product_id = $_REQUEST['product_id'];
	$product_data = $product->GetProduct($product_id);
	$name = $product_data['name'];
	$description = $product_data['description'];
	$price = $product_data['price'];
	$duration = $product_data['duration'];
	switch($product_data['duration_unit'])
	{
		case 'd':
			$duration_unit  = 'Day(s)';
			break;
		case 'm':
			$duration_unit  = 'Month(s)';
			break;
		case 'y':
			$duration_unit  = 'Year(s)';
			break;
	}

	$users = $user->CheckUserActive($_SESSION['SESSION_USERNAME']);
	$user_id = $users['user_id'];
	$username = $users['username'];
	$firstname = $users['firstname'];
	$lastname = $users['lastname'];
	$email = $users['email'];
	
	$tpl->assign('paypal_payments_status',$paypal_payments_status);
	$tpl->assign('paypal_subscribe_status',$paypal_subscribe_status);
	$tpl->assign('co_status',$co_status);
	$tpl->assign('co_subscribe_status',$co_subscribe_status);
	$tpl->assign('alertpay_status',$alertpay_status);
	$tpl->assign('alertpay_subscribe_status',$alertpay_subscribe_status);
	$tpl->assign('moneybookers_status',$moneybookers_status);

	$tpl->assign('product_id',$_REQUEST['product_id']);
	$tpl->assign('payment_gateway',$_REQUEST['payment_gateway']);
	$tpl->assign('name',$name);
	$tpl->assign('description',$description);
	$tpl->assign('price',$price);
	$tpl->assign('coupon_code',$_REQUEST['coupon_code']);
	$tpl->assign('error',$error_list);	
	$tpl->assign('currency_code',$currency_code);
	$tpl->assign('duration',$duration);
	$tpl->assign('duration_unit',$duration_unit);
	
	$tpl->assign('user_id',$user_id);
	$tpl->assign('username',$username);
	$tpl->assign('firstname',$firstname);
	$tpl->assign('lastname',$lastname);	 
	$tpl->assign('email',$email); 
	$tpl->assign('curr_month', date('m')); 

	$tpl->assign('pf',$_REQUEST['pf']);
	$tpl->display("order.html");
}

function ProcessRenewOrder() {
	global $tpl, $product, $user, $order, $error_list, $mail, $coupon, $pay_class, $currency_code, $currency_unit;
	
	$users = $user->CheckUserActive($_SESSION['SESSION_USERNAME']);
	$user_id = $users['user_id'];
	$username = $users['username'];
	$firstname = $users['firstname'];
	$lastname = $users['lastname'];
	$email = $users['email'];
	$product_id = $_REQUEST['product_id'];
	$products = $product->GetProduct($product_id);
	$payment_gateway = $_REQUEST['payment_gateway'];
	$coupon_code = $_REQUEST['coupon_code'];
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
	if (!$order->CheckActiveOrder($product_id,$user_id,$date_order)) {
		$error_list[$i] = "Order is already active for the selected month";
		$i++;			
	}
	else if($product_id =="")
	{
		$error_list[$i] = "Please choose membership type";
		$i++;	
	}	
	elseif($payment_gateway == "" && $products['price'] > 0)
	{
		$error_list[$i] = "Please select payment gateway";
		$i++;
	}
	
	if(!is_array($error_list)) 
	{
		$price = $products['price'];
		$name = $products['name'];
		$description = $products['description'];
		$item_name = $name." ( ".$description." )"; 
		$invoice_id = getInvoiceId();
		//**** for coupon ****//
		
		if($coupon_code != "")
		{
			$discount_data = $coupon->CheckProductDiscount($coupon_code, $product_id);
			if(!$discount_data)
			{
				$error_list[$i] = "Discount not found";
				$i++;
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
				
		if($price == 0)
		{
			$user_id = $user->Add($username,$password,$password,$firstname,$lastname,$email);
			$order_id = $order->AddOrder($user_id,$product_id,$date_order);
			
			$order_data = $order->GetOrder($order_id);
			$product_name = $order_data['name'];
			$product_desc = $order_data['description'];
			$product_price = $order_data['price'];
			$product_expire = date("Y-m-d",$order_data['date_expire']);
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
			$currency_code = $currency_code;//$currency_unit ===== GLOBAL VARIABLE
			$return_url = CFG_SITE_URL;
			$cancel_url = CFG_SITE_URL;
			$total = $price;
			$custom = "{$product_id}&{$date_order}&{$email}&{$username}&{$password}&{$firstname}&{$lastname}&{$coupon_code}&{$date_order}";
			if($payment_gateway == "co" || $payment_gateway == "co_subscribe")
				$gateway_data = $pay_class->GetPaymentGatewayDetail("2".$payment_gateway); 
			else
				$gateway_data = $pay_class->GetPaymentGatewayDetail($payment_gateway); 
			
			
				
			if($payment_gateway == "paypal_payments")
			{
				$notify_url = CFG_SITE_URL.'/payment/paypal.ipn.php';
				$paypal_payments_email = $gateway_data['payment_gateway_account'];
				$paypal_email = $paypal_payments_email;
				include 'payment/paypal.php';
			}
			elseif($payment_gateway == "paypal_subscribe")
			{
				$notify_url = CFG_SITE_URL.'/payment/paypal-subscribe.ipn.php';
				$paypal_subscribe_email = $gateway_data['payment_gateway_account'];
				$listing_period = $products['duration'];
				$listing_period_code = strtoupper($products['duration_unit']);
				$paypal_email = $paypal_subscribe_email;
				include 'payment/paypal-subscribe.php';
			}
			elseif($payment_gateway == "co")
			{
				$notify_url = CFG_SITE_URL.'/payment/2co.ipn.php';
				$co_account = $gateway_data['payment_gateway_account'];
				$list_co_account = explode("&", $co_account);
				$co_sid = $list_co_account[0];
				$co_secret		  = $list_co_account[1];
				$co_recurring = 0; //set subscribe
				include 'payment/2co.php';
			}
			elseif($payment_gateway == "co_subscribe")
			{
				$notify_url = CFG_SITE_URL.'/payment/2co-subscribe.ipn.php';
				$co_account = $gateway_data['payment_gateway_account'];
				$list_co_account = explode("&", $co_account);
				$co_sid = $list_co_account[0];
				$co_secret		  = $list_co_account[1];
				$co_recurring = 1; //set subscribe
				$co_prod_id = $product_id;
				include 'payment/2co-subscribe.php';
			}
			elseif($payment_gateway == "alertpay")
			{
				$notify_url = CFG_SITE_URL.'/payment/alertpay.ipn.php';
				$alertpay_account = $gateway_data['payment_gateway_account'];
				$list_alertpay_account = explode("&", $alertpay_account);
				$payalert_email = $list_alertpay_account[0];
				$payalert_security_code = $list_alertpay_account[1];
				$ap_currency = $currency_code;
				$ap_purchasetype = "service"; //lainnya subscription & service
				include 'payment/alertpay.php';
			}
			elseif($payment_gateway == "alertpay_subscribe")
			{
				$notify_url = CFG_SITE_URL.'/payment/alertpay.ipn.php';
				$alertpay_subscribe_account = $gateway_data['payment_gateway_account'];
				$list_alertpay_subscribe_account = explode("&", $alertpay_subscribe_account);
				$payalert_email = $list_alertpay_subscribe_account[0];
				$payalert_security_code = $list_alertpay_subscribe_account[1];
				$ap_currency = $currency_code;
				$ap_purchasetype = "Subscription"; //lainnya subscription & service
				if(strtolower($products['duration_unit']) == "d")
					$ap_timeunit = "Day";
				elseif(strtolower($products['duration_unit']) == "m")
					$ap_timeunit = "Month";
				elseif(strtolower($products['duration_unit']) == "y")
					$ap_timeunit = "Year";

				$ap_periodlength = $products['duration'];
				include 'payment/alertpay-subscribe.php';
			}
			elseif($payment_gateway == "moneybookers")
			{
				$notify_url = CFG_SITE_URL.'/payment/moneybookers.ipn.php';
				$moneybookers_email = $gateway_data['payment_gateway_account'];
				include 'payment/moneybookers.php';
			}
		}
	}
	else 
	{
		ShowFormRenewOrder();	
	}
}

function GetOrderHistory(){
	global $orders, $user, $order, $expire_in;
	
	$users = $user->GetActiveUserData($_SESSION['SESSION_USERNAME']);
	$user_id = $users['user_id'];
	$order_data = $order->GetUserOrderHistoryData($user_id);
	
	$i = 0;
	foreach ($order_data as $value) {
		$orders[$i]['no'] = $i+1;
		$orders[$i]['order_id'] = $value['order_id'];
		$orders[$i]['user_id'] = $value['user_id'];
		$orders[$i]['name'] = $value['name'];
		$orders[$i]['username'] = $value['username'];
		$orders[$i]['firstname'] = $value['firstname'];
		$orders[$i]['lastname'] = $value['lastname'];
		$orders[$i]['email'] = $value['email'];
		$orders[$i]['password'] = $value['password'];
		$orders[$i]['product_id'] = $value['product_id'];
		$orders[$i]['price'] = $value['price'];
		$orders[$i]['description'] = $value['description'];
		$orders[$i]['date_order'] = date("Y-m-d",$value['date_order']);
		$orders[$i]['date_expire'] = date("Y-m-d",$value['date_expire']);
		$orders[$i]['url'] = CFG_PROTECT_URL.'/'.$value['url'];
		$expire_in = $value['date_expire']-time();

		if($expire_in <= 0)
		{
			$product_id = $value['product_id'];
			$orders[$i]['expire_in'] = "<font color ='#FF0000'>Expired </font>";
		}
		elseif($expire_in <= CFG_NOTIFY_EXPIRE*24*60*60)
		{
			$expire_days = round($expire_in/60/60/24);
			if($expire_days == 0)
				$expire_days = $expire_days+1;
			$orders[$i]['expire_in'] = "<font color ='#F87217'>Expire in ".$expire_days. " Days</font>";
		}
		else
		{
			$orders[$i]['expire_in'] = "<font color ='green'>Active</font>";
		}

		$invoice_id = $order->GetPaymentInvoice($value['order_id']);
		if($invoice_id)
		{
			$orders[$i]['invoice'] = "<a href ='invoice_payment.php?invoice_id =$invoice_id' target ='_blank'>Show</a>";
		}
		else
		{
			$orders[$i]['invoice'] = "Not";
			
		}	
		
		if($i % 2 != 0)
		{
			$orders[$i]['color'] = '#f7f7f7';
		}
		else
		{
			$orders[$i]['color'] = '#ffffff';
		}
		$i++;
	} 
}

function ShowOrderHistory() {
	global $tpl, $orders;
	
	$tpl->assign('pf',$_REQUEST['pf']);
	$tpl->assign('expire_in',$expire_in);
	$tpl->assign('orders',$orders);
	$tpl->assign('error',$error_list);	
	$tpl->display('order.html');		
} 

/* ===================================================
	main
 ===================================================*/
if ($pf == 'add_order') {
	GetPaymentGateway();
	GetPaymentCurrency();
	if ($process == 'add_order') {
		ProcessAddOrder();
	}	 
	else {
		ShowFormAddOrder();
	}	
}
elseif ($pf =='order_history') {
	GetOrderHistory();	
	ShowOrderHistory();
}
elseif ($pf =='renewal') {
	GetPaymentGateway();
	GetPaymentCurrency();
	if ($process == 'renew_order') {
		ProcessRenewOrder();
	}	 
	else {
		ShowFormRenewOrder();
	}	
}
?>
