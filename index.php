<?php
include 'init.php';
$user->AuthenticationUser();

$pf = $_REQUEST['pf'];

function RenewOrder() {
	$pf             = $_REQUEST['pf'];
	$product_id     = $_REQUEST['product_id'];
	$users          = $GLOBALS['user']->CheckUserActive($_SESSION['SESSION_USERNAME']);
	$user_id        = $users['user_id'];
	$username       = $users['username'];
	$firstname      = $users['firstname'];
	$lastname       = $users['lastname'];
	$email          = $users['email'];
	$password       = $users['password'];
	$name           = $users['name'];   
	$date_order = strtotime(date('Y').'/'.date('m').'/1');

	if($GLOBALS['order']->CheckActiveOrder($product_id,$user_id,$date_order))
	{
		$products       = $GLOBALS['product']->GetProduct($product_id);
		$price          = $products['price'];
		$name           = $products['name'];
		$description    = $products['description'];
		//**** for coupon ****//
		if($price == 0) {
		  $order_id         = $GLOBALS['order']->AddOrder($user_id,$product_id,$date_order);
		  
		  $order_data       = $GLOBALS['order']->GetOrder($order_id);
		  $product_name     = $order_data['name'];
		  $product_desc     = $order_data['description'];
		  $product_price    = $order_data['price'];
		  $product_expire   = date("m-d-y",$order_data['date_expire']);
		  $from_email       = CFG_NOTIFY_EMAIL;
		  $from_name        = CFG_NOTIFY_FROM;            
		  
		  $GLOBALS['mail']->ConfirmOrderEmail(CFG_SITE_NAME,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$product_expire,$email);
		  $GLOBALS['mail']->ReceivedOrderEmail(CFG_SITE_NAME,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$date_order,$product_expire,CFG_SITE_MAIL);
		  
		  $GLOBALS['order']->UpdateLastEmailSent($order_id,time());
		  $login = $GLOBALS['user']->Login($username, $password, $expire);
		  header("Location: index.php"); 
		} else {
			header("Location: order.php?pf=renewal&product_id=$product_id");
		}
	}
}

function GetOrder(){
  global $orders, $user, $order, $expire_in;
  
  $users    = $user->GetActiveUserData($_SESSION['SESSION_USERNAME']);
  $user_id  = $users['user_id'];
  $order_data   = $order->GetUserOrderData($user_id);
  
  $i = 0;
  $j = 0;
  foreach ($order_data as $value) {
    
    $temp_product_id[$j] = $value['product_id'];
    $show_order_data = true;
    
    for ($loop=0;$loop<=$j-1;$loop++)
    {
      if($temp_product_id[$loop] == $value['product_id'])
      {
        $show_order_data = false;
      }
    }
    
    if($show_order_data)
    {
      $orders[$i]['no']          = $i+1;
      $orders[$i]['order_id']    = $value['order_id'];
      $orders[$i]['user_id']     = $value['user_id'];
      $orders[$i]['name']        = $value['name'];
      $orders[$i]['username']    = $value['username'];
      $orders[$i]['firstname']   = $value['firstname'];
      $orders[$i]['lastname']    = $value['lastname'];
      $orders[$i]['email']       = $value['email'];
      //$orders[$i]['password']    = $value['password'];
      $orders[$i]['product_id']  = $value['product_id'];
      $orders[$i]['price']       = $value['price'];
      $orders[$i]['description'] = $value['description'];
      $orders[$i]['date_order']  = date("m-d-y",$value['date_order']);
      $orders[$i]['date_expire'] = date("m-d-y",$value['date_expire']);
      $orders[$i]['url']         = CFG_PROTECT_URL.'/'.$value['url'];
      $expire_in                 = $value['date_expire']-time();
  
      if($expire_in <= 0)
      {
        $product_id               = $value['product_id'];
        $orders[$i]['name']       = $value['name']. " <a href=\"index.php?pf=renewal&product_id=$product_id\" style=\"color:red;\">[Renew]</a>";
        $orders[$i]['expire_in']  = "<font color='#FF0000'>Expired </font>";
      }
      elseif($expire_in <= CFG_NOTIFY_EXPIRE*24*60*60)
      {
        $expire_days = round($expire_in/60/60/24);
        if($expire_days == 0)
          $expire_days = $expire_days+1;
        $orders[$i]['expire_in'] = "<font color='#F87217'>Expire in ".$expire_days. " Days</font>";
      }
      else
      {
        $orders[$i]['expire_in'] = "<font color='green'>Active</font>";
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
    $j++;
  } 
}

function ShowOrder() {
  global $tpl, $orders, $expire_in;
  
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('expire_in',$expire_in);
  $tpl->assign('orders',$orders);
  $tpl->assign('error',$error_list);  
  $tpl->display('index.html');    
} 


/*=============================================================================================
  Main Program
=============================================================================================*/
switch($_REQUEST['pf'])
{
	case 'renewal':
		RenewOrder();
		break;
	case '':
	default:
		GetOrder();  
		ShowOrder();
		break;
}
?>
