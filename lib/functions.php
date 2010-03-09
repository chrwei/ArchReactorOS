<?php

function GetConfig() {
  global $db;

  $query   = "select * from config";
  $result  = $db->Execute($query);
  if(!$result) return false;
  $config  = $result->GetRows();
  foreach ($config as $k => $v ) {
    define('CFG_'.strtoupper($v['name']),$v['value']);
  }
  return $config;
}

function UpdateConfig($value,$name) {
  global $db;
  
  $record["value"]  = $value;
  $db->AutoExecute('config', $record, 'UPDATE', "name ='".mysql_escape_string($name)."'");
}


function cryptPasswd($passwd) {
  $passwd = crypt(trim($passwd),base64_encode(CRYPT_STD_DES));
  return $passwd;
}
    
function getInvoiceId() {
  $pattern  = time(); 
  $invoice_id = substr($pattern,2,8 );
  return $invoice_id;
}

function ProcessIPN($account,$business,$amount,$product_id,$username,$password,$firstname,$lastname,$email,$log,$payment_date,$payment_gateway,$invoice_id,$coupon_code = '',$date_order)
{
  global $user, $order, $product, $mail,$coupon, $currency_code, $inv_class;
  
  $product_data       = $product->GetProduct($product_id);
  $coupon_code_valid  = false;
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
        $percent                  = str_replace("%", "", $discount_data['coupon_value']);
        $coupon_value_type        = "percentage";
        $percentage_coupon_value  = $percent;
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
      $coupon_code_valid = true;
    }
  }
  else
  {
    $price = $product_data['price'];
  }

  if($account==$business)
  {
    if(round($price,3) == round($amount,3))
    {
        /*add user*/
        GetPaymentCurrency();
        $user_exist = $user->CheckUserActive($username);
        if($user_exist['user_id']=="")
        {
          $user_id = $user->Add($username,$password,$password,$firstname,$lastname,$email);
        }
        else
        {
          $user_id = $user_exist['user_id'];
        }
        /*add order*/
        $order_id = $order->AddOrder($user_id,$product_id,$date_order);

        $order_data       = $order->GetOrder($order_id);
        
        $invoice_date     = time();
        $due_date         = time();
        $invoice_to       = $firstname;
        $service          = $order_data['name'];
        $description      = $order_data['description'];
        $discount_price   = $order_data['price'] - $amount;
        $total_price      = $amount;
        $comment          = "Payment is success";
        $paid             = $amount;
        $paid_date        = time();
        $product_name     = $order_data['name'];
        $product_desc     = $order_data['description'];
        $product_price    = $currency_code.". ".$price;
        $product_expire   = date("Y-m-d",$order_data['date_expire']);
        $from_email       = CFG_NOTIFY_EMAIL;
        $from_name        = CFG_NOTIFY_FROM; 

        $order->AddPayment($order_id,$amount,$currency_code,$payment_date,$payment_gateway,$log,$invoice_id);
        
        $inv_class->AddInvoice($invoice_id,$invoice_date,$due_date,$invoice_to,$service,$description,$order_data['price'],$discount_price,$total_price,$currency_code,$comment,$paid,$paid_date,$payment_gateway,$email);
        
        if($coupon_code_valid)
        {
          $coupon->AddUsageCount($coupon_code);
        }
        
        $mail->ConfirmOrderEmail(CFG_SITE_NAME,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$product_expire,$email);
        
        $mail->ReceivedOrderEmail(CFG_SITE_NAME,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$date_order,$product_expire,CFG_SITE_MAIL);
    }
  }
}

function AddHtaccess($path) {
	return;
}

function UpdateHtaccess($new_path,$old_path) {
	return;
}

function GenerateHtgroup($file_htgroup) {
	return;
}

function GenerateHtpasswd($file_htpasswd) {
	return;
}

function GenerateHtaccess($path) {
	return;
}

function DeleteHtaccess($path) {
	return;
}

function RemoveFolder($dir){
  
  if(!is_dir($dir))
    return false;
  for($s = '/', $stack = array($dir), $emptyDirs = array($dir); $dir = array_pop($stack);) {
    if(!($handle = @dir($dir)))
      continue;
    while(false !== $item = $handle->read())
  			$item != '.' && $item != '..' && (is_dir($path = $handle->path . $s . $item) ?
  			array_push($stack, $path) && array_push($emptyDirs, $path) : unlink($path));
  		$handle->close();
  	}
  	for($i = count($emptyDirs); $i--; rmdir($emptyDirs[$i]));
  }    

function integer_divide($x, $y){
    //Returns the integer division of $x/$y.
    $t = 1;
    if($y == 0 || $x == 0)
        return 0;
    if($x < 0 XOR $y < 0) //Mistaken the XOR in the last instance...
        $t = -1;
    $x = abs($x);
    $y = abs($y);
    $ret = 0;
    while(($ret+1)*$y <= $x)
        $ret++;
    return $t*$ret;
}

function GetPaymentCurrency()
{
  global $pay_class, $currency_code, $currency_name, $currency_unit;
  
  $currency_data = $pay_class->GetPaymentCurrency();
  $currency_code = $currency_data['currency_code'];
  $currency_name = $currency_data['currency_name'];
  $currency_unit = $currency_data['currency_pay_unit'];
}

function GetPaymentGateway()
{
  global $pay_class, $cash_payments_status, $paypal_payments_status, $paypal_subscribe_status, $co_status, $co_subscribe_status, $alertpay_status, $alertpay_subscribe_status, $moneybookers_status;
  
  $payment_gateway_data = $pay_class->GetAllPaymentGateway();
  foreach($payment_gateway_data as $value)
  {
    if($value['payment_gateway_name'] == "cash_payments")
    {
      $cash_payments_status = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "paypal_payments")
    {
      $paypal_payments_status = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "paypal_subscribe")
    {
      $paypal_subscribe_status = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "2co")
    {
      $co_status = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "2co_subscribe")
    {
      $co_subscribe_status = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "alertpay")
    {
      $alertpay_status = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "alertpay_subscribe")
    {
      $alertpay_subscribe_status = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "moneybookers")
    {
      $moneybookers_status = $value['payment_gateway_status'];
    }
  }
}

function createRandomPassword($length) {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";

    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;

    while ($i < $length) {
        $num = rand() % (strlen($chars)-1); //zero based
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}
?>
