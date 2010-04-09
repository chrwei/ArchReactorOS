<?php
include '../init.php';

/*###########################################################
Section : Main
###########################################################*/
$user->AuthenticationAdmin();
switch($_REQUEST['pf']){
	default:
	case 'currency':
		GetAllPaymentCurrency();
		ShowPaymentCurrency();
	break;
	case 'change_currency':
		ChangePaymentCurrency();
	break;
	case 'gateway':
		GetAllPaymentGateway();
		ShowPaymentGateway();
	break;
	case 'gateway_setting':
		GetGatewayDetail();
		ShowGatewayDetail();
	break;
}

/** 
 * Functions 
 * */


function ShowPaymentCurrency() {
  global $tpl, $currs, $currency_name, $currency_code;
  
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('currency_name',$currency_name);
  $tpl->assign('currency_code',$currency_code);
  $tpl->assign('currs',$currs);
  $tpl->display('admin/payment.html');
}

function GetAllPaymentCurrency() {
  global $pay_class, $currs, $currency_name, $currency_code;
  
  
  $current_currency = $pay_class->GetPaymentCurrency();
  $currency_name    = $current_currency['currency_name'];
  $currency_code    = $current_currency['currency_code'];
  $currency_data    = $pay_class->GetAllCurrency();
  
  $i = 0;
  foreach($currency_data as $value)
  {
    $currs[$i]['no']   = $i+1;
    $currs[$i]['currency_id']     = $value['currency_id'];
    $currs[$i]['currency_code']   = $value['currency_code'];
    $currs[$i]['currency_name']   = $value['currency_name'];
    $currs[$i]['currency_usage']  = $value['currency_usage'];
    
    if($i % 2 != 0)
    {
      $currs[$i]['color'] = '#f7f7f7';
    }
    else
    {
      $currs[$i]['color'] = '#ffffff';
    }
    
    if($currs[$i]['currency_usage'] == 1)
    {
      $currs[$i]['color'] = '#99cc20';
    } 
    
    $i++;
  }
}

function ChangePaymentCurrency()
{
  global $pay_class;
  
  $process = $_REQUEST['process'];
  $currency_id = $_REQUEST['currency_id'];
  if($process == "change")
  {
    $pay_class->ChangeUseCurrency($currency_id);
    header("Location: payment.php?pf=currency");
  }
}

function GetAllPaymentGateway()
{
  global $pay_class, $paypal_payments_status, $paypal_subscribe_status, $co_status, $co_subscribe_status, $alertpay_status, $alertpay_subscribe_status, $moneybookers_status, $cash_payments_status;
  
  $payment_gateway_data = $pay_class->GetAllPaymentGateway();
  foreach($payment_gateway_data as $value)
  {
    if($value['payment_gateway_name'] == "cash_payments")
    {
      $cash_payments_status  = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "paypal_payments")
    {
      $paypal_payments_status  = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "paypal_subscribe")
    {
      $paypal_subscribe_status  = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "2co")
    {
      $co_status        = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "2co_subscribe")
    {
      $co_subscribe_status        = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "alertpay")
    {
      $alertpay_status  = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "alertpay_subscribe")
    {
      $alertpay_subscribe_status  = $value['payment_gateway_status'];
    }
    elseif($value['payment_gateway_name'] == "moneybookers")
    {
      $moneybookers_status  = $value['payment_gateway_status'];
    }
  }
}

function ShowPaymentGateway()
{
  global $tpl, $paypal_payments_status, $paypal_subscribe_status, $co_status, $co_subscribe_status, $alertpay_status, $alertpay_subscribe_status, $moneybookers_status, $cash_payments_status;

  $tpl->assign('cash_payments_status',$cash_payments_status);
  $tpl->assign('paypal_payments_status',$paypal_payments_status);
  $tpl->assign('paypal_subscribe_status',$paypal_subscribe_status);
  $tpl->assign('co_status',$co_status);
  $tpl->assign('co_subscribe_status',$co_subscribe_status);
  $tpl->assign('alertpay_status',$alertpay_status);
  $tpl->assign('alertpay_subscribe_status',$alertpay_subscribe_status);
  $tpl->assign('moneybookers_status',$moneybookers_status);
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->display('admin/payment.html');
}

function GetGatewayDetail()
{
  global $pay_class, $paypal_payments_email, $paypal_subscribe_email, $co_sid, $co_secret, $co_subscribe_sid, $co_subscribe_secret, $alertpay_email, $alertpay_security_code, $alertpay_subscribe_email, $alertpay_subscribe_security_code, $moneybookers_email, $paypal_payments_status, $paypal_subscribe_status, $co_status, $co_subscribe_status, $alertpay_status, $alertpay_subscribe_status, $moneybookers_status, $cash_payments_status, $success, $error_list, $product, $products;
  
  $gateway_name = $_REQUEST['gateway_name'];
  $process      = $_REQUEST['process'];
  
  if($gateway_name == "cash_payments")
  {
    if($process == "edit")
    {
      $cash_payments_status  = $_REQUEST['cash_payments_status'];
      $i = 0;
      if(!is_array($error_list))
      {
        $pay_class->EditPaymentGateway($gateway_name,'',$cash_payments_status);
        $success = true;
      }
    }
    else
    {
      $gateway_data           = $pay_class->GetPaymentGatewayDetail($gateway_name); 
      $cash_payments_status  = $gateway_data['payment_gateway_status'];
    }
  }
  elseif($gateway_name == "paypal_payments")
  {
    if($process == "edit")
    {
      $paypal_payments_email   = $_REQUEST['paypal_payments_email'];
      $paypal_payments_status  = $_REQUEST['paypal_payments_status'];
      $i = 0;
      if($paypal_payments_email =="" && $paypal_payments_status == 1)
      {
        $error_list[$i] = "Paypal email is required if status is enable";
        $i++;
      }
      if(!is_array($error_list))
      {
        $pay_class->EditPaymentGateway($gateway_name,$paypal_payments_email,$paypal_payments_status);
        $success = true;
      }
    }
    else
    {
      $gateway_data           = $pay_class->GetPaymentGatewayDetail($gateway_name); 
      $paypal_payments_email   = $gateway_data['payment_gateway_account'];
      $paypal_payments_status  = $gateway_data['payment_gateway_status'];
    }
  }
  elseif($gateway_name == "paypal_subscribe")
  {
    if($process == "edit")
    {
      $paypal_subscribe_email   = $_REQUEST['paypal_subscribe_email'];
      $paypal_subscribe_status  = $_REQUEST['paypal_subscribe_status'];
      $i = 0;
      if($paypal_subscribe_email =="" && $paypal_subscribe_status == 1)
      {
        $error_list[$i] = "Paypal email is required if status is enable";
        $i++;
      }
      if(!is_array($error_list))
      {
        $pay_class->EditPaymentGateway($gateway_name,$paypal_subscribe_email,$paypal_subscribe_status);
        $success = true;
      }
    }
    else
    {
      $gateway_data             = $pay_class->GetPaymentGatewayDetail($gateway_name); 
      $paypal_subscribe_email   = $gateway_data['payment_gateway_account'];
      $paypal_subscribe_status  = $gateway_data['payment_gateway_status'];
    }
  }
  elseif($gateway_name == "co")
  {
    if($process == "edit")
    {
      $co_sid      = $_REQUEST['co_sid'];
      $co_secret   = $_REQUEST['co_secret'];
      $co_status   = $_REQUEST['co_status'];
      $i = 0;
      if($co_sid == "" && $co_status == 1)
      {
        $error_list[$i] = "Sid is required if status is enable";
        $i++;
      }
      if($co_secret == "" && $co_status == 1)
      {
        $error_list[$i] = "Secret is required if status is enable";
        $i++;
      }
      if(!is_array($error_list))
      {
        $pay_class->EditPaymentGateway("2".$gateway_name,$co_sid."&".$co_secret,$co_status);
        $success = true;
      }
    }
    else
    {
      $gateway_data         = $pay_class->GetPaymentGatewayDetail("2".$gateway_name); 
      $co_account           = $gateway_data['payment_gateway_account'];
      $list_co_account      = explode("&", $co_account);
      $co_sid               = $list_co_account[0];
      $co_secret            = $list_co_account[1];
      $co_status            = $gateway_data['payment_gateway_status'];
    }
  }
  elseif($gateway_name == "co_subscribe")
  {
    
    $products_data = $product->GetProductNotFree();
    $i = 0;
    foreach ($products_data as $value) 
    {
      $products[$i]['no']         = $i+1;
      $products[$i]['product_id'] = $value['product_id'];
      $products[$i]['name']       = $value['name'];
      if(strlen($value['description']) > 38)
      {
        $products[$i]['description'] = substr($value['description'],0,38)."...";
      }
      else
      {
        $products[$i]['description'] = $value['description'];
      }
      $products[$i]['price']          = $value['price'];
      $products[$i]['duration']       = $value['duration'];
      $products[$i]['duration_unit']  = $value['duration_unit'];
      $products[$i]['path']           = $value['path'];
      $products[$i]['url']            =  $value['url'];
      if($i % 2 != 0)
      {
        $products[$i]['color'] = '#f7f7f7';
      }
      else
      {
        $products[$i]['color'] = '#ffffff';
      }
      $i++;
    }
    
    if($process == "edit")
    {
      $co_subscribe_sid      = $_REQUEST['co_subscribe_sid'];
      $co_subscribe_secret   = $_REQUEST['co_subscribe_secret'];
      $co_subscribe_status   = $_REQUEST['co_subscribe_status'];
      $i = 0;
      if($co_subscribe_sid == "" && $co_subscribe_status == 1)
      {
        $error_list[$i] = "Sid is required if status is enable";
        $i++;
      }
      if($co_subscribe_secret == "" && $co_subscribe_status == 1)
      {
        $error_list[$i] = "Secret is required if status is enable";
        $i++;
      }
      if(!is_array($error_list))
      {
        $pay_class->EditPaymentGateway("2".$gateway_name,$co_subscribe_sid."&".$co_subscribe_secret,$co_subscribe_status);
        $success = true;
      }
    }
    else
    {
      $gateway_data                   = $pay_class->GetPaymentGatewayDetail("2".$gateway_name); 
      $co_subscribe_account           = $gateway_data['payment_gateway_account'];
      $list_co_subscribe_account      = explode("&", $co_subscribe_account);
      $co_subscribe_sid               = $list_co_subscribe_account[0];
      $co_subscribe_secret            = $list_co_subscribe_account[1];
      $co_subscribe_status            = $gateway_data['payment_gateway_status'];
    }
  }
  elseif($gateway_name == "alertpay")
  {
    if($process == "edit")
    {
      $alertpay_email           = $_REQUEST['alertpay_email'];
      $alertpay_security_code   = $_REQUEST['alertpay_security_code'];
      $alertpay_status          = $_REQUEST['alertpay_status'];
      $i = 0;
      if($alertpay_email =="" && $alertpay_status == 1)
      {
        $error_list[$i] = "Alertpay email is required if status is enable";
        $i++;
      }
      if($alertpay_security_code =="" && $alertpay_status == 1)
      {
        $error_list[$i] = "Alertpay security code is required if status is enable";
        $i++;
      }
      if(!is_array($error_list))
      {
        $pay_class->EditPaymentGateway($gateway_name,$alertpay_email."&".$alertpay_security_code,$alertpay_status);
        $success = true;
      }
    }
    else
    {
      $gateway_data           = $pay_class->GetPaymentGatewayDetail($gateway_name); 
      $alertpay_account       = $gateway_data['payment_gateway_account'];
      $list_alertpay_account  = explode("&", $alertpay_account);
      $alertpay_email         = $list_alertpay_account[0];
      $alertpay_security_code = $list_alertpay_account[1];
      $alertpay_status        = $gateway_data['payment_gateway_status'];
    }
  }
  elseif($gateway_name == "alertpay_subscribe")
  {
    if($process == "edit")
    {
      $alertpay_subscribe_email           = $_REQUEST['alertpay_subscribe_email'];
      $alertpay_subscribe_security_code   = $_REQUEST['alertpay_subscribe_security_code'];
      $alertpay_subscribe_status          = $_REQUEST['alertpay_subscribe_status'];
      $i = 0;
      if($alertpay_subscribe_email =="" && $alertpay_subscribe_status == 1)
      {
        $error_list[$i] = "Alertpay email is required if status is enable";
        $i++;
      }
      if($alertpay_subscribe_security_code =="" && $alertpay_subscribe_status == 1)
      {
        $error_list[$i] = "Alertpay security code is required if status is enable";
        $i++;
      }
      if(!is_array($error_list))
      {
        $pay_class->EditPaymentGateway($gateway_name,$alertpay_subscribe_email."&".$alertpay_subscribe_security_code,$alertpay_subscribe_status);
        $success = true;
      }
    }
    else
    {
      $gateway_data                     = $pay_class->GetPaymentGatewayDetail($gateway_name); 
      $alertpay_subscribe_account       = $gateway_data['payment_gateway_account'];
      $list_alertpay_subscribe_account  = explode("&", $alertpay_subscribe_account);
      $alertpay_subscribe_email         = $list_alertpay_subscribe_account[0];
      $alertpay_subscribe_security_code = $list_alertpay_subscribe_account[1];
      $alertpay_subscribe_status        = $gateway_data['payment_gateway_status'];
    }
  }
  elseif($gateway_name == "moneybookers")
  {
    if($process == "edit")
    {
      $moneybookers_email   = $_REQUEST['moneybookers_email'];
      $moneybookers_status  = $_REQUEST['moneybookers_status'];
      $i = 0;
      if($moneybookers_email =="" && $moneybookers_status == 1)
      {
        $error_list[$i] = "Moneybookers email is required if status is enable";
        $i++;
      }
      if(!is_array($error_list))
      {
        $pay_class->EditPaymentGateway($gateway_name,$moneybookers_email,$moneybookers_status);
        $success = true;
      }
    }
    else
    {
      $gateway_data         = $pay_class->GetPaymentGatewayDetail($gateway_name); 
      $moneybookers_email   = $gateway_data['payment_gateway_account'];
      $moneybookers_status  = $gateway_data['payment_gateway_status'];
    }
  }
}

function ShowGatewayDetail()
{
  global $tpl, $paypal_payments_email, $paypal_subscribe_email, $co_sid, $co_secret, $co_subscribe_sid;
  global $co_subscribe_secret, $alertpay_email, $alertpay_security_code, $alertpay_subscribe_email;
  global $alertpay_subscribe_security_code, $moneybookers_email, $paypal_payments_status;
  global $paypal_subscribe_status, $co_status, $co_subscribe_status, $alertpay_status;
  global $alertpay_subscribe_status, $moneybookers_status, $success, $error_list, $products;
  global $cash_payments_status;
  
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('gateway_name',$_REQUEST['gateway_name']);
  
  $tpl->assign('cash_payments_status',$cash_payments_status);
  
  $tpl->assign('paypal_payments_email',$paypal_payments_email);
  $tpl->assign('paypal_payments_status',$paypal_payments_status);
  
  $tpl->assign('paypal_subscribe_email',$paypal_subscribe_email);
  $tpl->assign('paypal_subscribe_status',$paypal_subscribe_status);

  $tpl->assign('moneybookers_email',$moneybookers_email);
  $tpl->assign('moneybookers_status',$moneybookers_status);

  
  $tpl->assign('co_sid',$co_sid);
  $tpl->assign('co_secret',$co_secret);
  $tpl->assign('co_status',$co_status);
  
  $tpl->assign('co_subscribe_sid',$co_subscribe_sid);
  $tpl->assign('co_subscribe_secret',$co_subscribe_secret);
  $tpl->assign('co_subscribe_status',$co_subscribe_status);
  
  $tpl->assign('alertpay_email',$alertpay_email);
  $tpl->assign('alertpay_security_code',$alertpay_security_code);
  $tpl->assign('alertpay_status',$alertpay_status);
  $tpl->assign('alertpay_ipn_url',CFG_SITE_URL.'/payment/alertpay.ipn.php');
  
  $tpl->assign('alertpay_subscribe_email',$alertpay_subscribe_email);
  $tpl->assign('alertpay_subscribe_security_code',$alertpay_subscribe_security_code);
  $tpl->assign('alertpay_subscribe_status',$alertpay_subscribe_status);
  
  
  $tpl->assign('success',$success);
  $tpl->assign('error',$error_list);
  $tpl->assign('products',$products);
  $tpl->display('admin/payment.html');
}

?>
