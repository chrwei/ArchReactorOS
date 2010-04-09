<?php
include '../init.php';

/*###########################################################
Section : Main
###########################################################*/
$user->AuthenticationAdmin();

switch($_REQUEST['pf']){
	default:	//fall-through
	case 'browse':
		ShowAllUsers();
		ShowFormAllUsers();
	break;
	case 'detail':
		ShowDetailUser();
		ShowFormDetailUser();
	break;
	case 'delete':
		DeleteUser($_REQUEST['delete']);
	break;
	case 'add':
		if ($_REQUEST['process'] == 'add')
			AddUser();
		else ShowFormAddUser();
	break;
	case 'add_order':
		GetPaymentCurrency();
		if ($_REQUEST['process'] == 'add_order') 
			ProcessAddOrder();
		else ShowFormAddOrder();
	break;
	case 'search':
		ShowSearchResult() ;
		ShowFormSearchResult();
	break;
}

/**
 * Functions
 * */

function ShowFormAllUsers() {
  global $tpl, $users, $success, $paging;
  
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('success',$_REQUEST['success']);
  $tpl->assign('delete_admin',$_REQUEST['delete_admin']);
  $tpl->assign('users',$users);
  $tpl->assign('paging',$paging);
  $tpl->display('admin/user.html');
}

function ShowAllUsers() {
  global $user, $users, $paging;
  
  $total_data         = $user->GetUserTotal();
  $total_data_in_page = LIMIT_USER_PAGE;
  $paging             = paging($total_data,$total_data_in_page);
  $page               = $_REQUEST['page']-1;
  if($page<=0)
  {
    $users_data = $user->BrowseAllUsers(0,$total_data_in_page);
  }
  else
  {
    $users_data = $user->BrowseAllUsers($page*$total_data_in_page,$total_data_in_page);
  } 
  $i = 0;
  foreach ($users_data as $value) 
  {
    $users[$i]['user_id']   = $value['user_id'];
    $users[$i]['username']  = $value['username'];
    $users[$i]['firstname'] = $value['firstname'];
    $users[$i]['lastname']  = $value['lastname'];
	$users[$i]['paid']      = $value['paid'];
    if($i % 2 != 0)
    {
      $users[$i]['color'] = '#f7f7f7';
    }
    else
    {
      $users[$i]['color'] = '#ffffff';
    }       
    $i++;
  }
}

function ShowFormSearchResult() {
  global $tpl, $users, $error_list;
  
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('search_for',$_REQUEST['search_for']);
  $tpl->assign('search_in',$_REQUEST['search_in']);
  $tpl->assign('error',$error_list);
  $tpl->assign('users',$users);
  $tpl->display('admin/user.html');
}

function ShowSearchResult() {
  global $user,$users,$error_list;
  
  $search_for = $_REQUEST['search_for'];
  $search_in  = $_REQUEST['search_in'];
  $i = 0;
  if($search_for == "")
  {
    $error_list[$i] = "Search for is required";
    $i++;
  }
  
  if(!is_array($error_list))
  {
    $users_data = $user->GetUsersSearchResult($search_for,$search_in);
    $i = 0;
    foreach ($users_data as $value) 
    {
      $users[$i]['no'] = $i+1;
      $users[$i]['user_id'] = $value['user_id'];
      $users[$i]['username'] = $value['username'];
      //$users[$i]['password'] = $value['password'];
      $users[$i]['firstname'] = $value['firstname'];
      $users[$i]['lastname'] = $value['lastname'];
      $users[$i]['email'] = $value['email'];
      $users[$i]['date'] = date("F j, Y",$value['date']);
      if($i % 2 != 0)
      {
        $users[$i]['color'] = '#f7f7f7';
      }
      else
      {
        $users[$i]['color'] = '#ffffff';
      }       
      $i++;
    }
  }
}

function ShowFormDetailUser() {
	$GLOBALS['tpl']->assign('pf',$_REQUEST['pf']);
	$GLOBALS['tpl']->assign('error',$GLOBALS['error_list']);
	$GLOBALS['tpl']->assign('user_id',$_REQUEST['user_id']);
	$GLOBALS['tpl']->assign('username', $GLOBALS['user_data']['username']);
	$GLOBALS['tpl']->assign('firstname',$GLOBALS['user_data']['firstname']);
	$GLOBALS['tpl']->assign('lastname', $GLOBALS['user_data']['lastname']);
	$GLOBALS['tpl']->assign('email',$GLOBALS['user_data']['email']);
	$GLOBALS['tpl']->assign('address1',$GLOBALS['user_data']['address1']);
	$GLOBALS['tpl']->assign('address2',$GLOBALS['user_data']['address2']);
	$GLOBALS['tpl']->assign('city',$GLOBALS['user_data']['city']);
	$GLOBALS['tpl']->assign('state',$GLOBALS['user_data']['state']);
	$GLOBALS['tpl']->assign('zip',$GLOBALS['user_data']['zip']);
	$GLOBALS['tpl']->assign('phone',$GLOBALS['user_data']['phone']);
	$GLOBALS['tpl']->assign('success',$GLOBALS['success']);
	$GLOBALS['tpl']->display('admin/user.html');
}

function ShowDetailUser() 
{
	$user_id    = $_REQUEST['user_id'];
	$process    = $_REQUEST['process'];
	$GLOBALS['user_data']  = $GLOBALS['user']->GetUser($user_id);

	if($process == 'edit' && $_REQUEST['subsave'] == 'Save') 
	{
		$username = stripslashes($_REQUEST['username']);
		$firstname = stripslashes($_REQUEST['firstname']);
		$lastname = stripslashes($_REQUEST['lastname']);
		$email = stripslashes($_REQUEST['email']);
		$address1 = stripslashes($_REQUEST['address1']);
		$address2 = stripslashes($_REQUEST['address2']);
		$city = stripslashes($_REQUEST['city']);
		$state = stripslashes($_REQUEST['state']);
		$zip = stripslashes($_REQUEST['zip']);
		$phone = stripslashes($_REQUEST['phone']);

		if($username == ""){
			$GLOBALS['error_list'][] = _("Username is required");
		} elseif($GLOBALS['user_data']['username']!=$username) {
			if($GLOBALS['user']->CheckUserLogin($username)) {
				$GLOBALS['error_list'][] = _("Username already exist");
			}				
		}

		if($firstname == ""){
			$GLOBALS['error_list'][] = _("Firstname is required");
		}
		if($lastname == ""){
			$GLOBALS['error_list'][] = _("Lastname is required");
		}
		if($address1 == ""){
			$GLOBALS['error_list'][] = _("Address is required");
		}
		if($city == ""){
			$GLOBALS['error_list'][] = _("City is required");
		}
		if($state == ""){
			$GLOBALS['error_list'][] = _("State is required");
		}
		if($zip == ""){
			$GLOBALS['error_list'][] = _("Zip is required");
		}
		if($email == ""){
			$GLOBALS['error_list'][] = _("Email is required");
		} elseif($GLOBALS['user_data']['email']!=$email) {
			if($GLOBALS['user']->CheckEmailExist($email)){
				$GLOBALS['error_list'][] = _("Email already exist");
			}
		} elseif(!IsEmailAddress($email)){
			$GLOBALS['error_list'][] = _("email is not valid");
		} 
		if(!is_array($GLOBALS['error_list'])) {
			$GLOBALS['user']->Update($user_id, $username, '', $firstname, $lastname, $email, $address1, $address2, $city, $state, $zip, $phone);
			$GLOBALS['success'] = true;
			$GLOBALS['user_data'] = $GLOBALS['user']->GetUser($user_id);
		}
	}
	elseif ($process == 'edit' && $_REQUEST['subconfirm'] == 'Send Confirmation') 
	{
		$username = stripslashes($_REQUEST['username']);
		$firstname = stripslashes($_REQUEST['firstname']);
		$email = stripslashes($_REQUEST['email']);
		$from_email  = CFG_NOTIFY_EMAIL;
		$from_name   = CFG_NOTIFY_FROM;
		$GLOBALS['mail']->ConfirmAccountEmail(CFG_SITE_NAME,$username,$firstname,$from_email,$from_name,$email);
		$GLOBALS['success'] = true;
	}
}

function DeleteUser($user_id_list) {
  global $tpl, $user, $success;
  
	$delete_admin = false;
	for($i=0;$i<=count($user_id_list);$i++) {
		if($user_id_list[$i] != 1)
			$user->Delete($user_id_list[$i]);
		else $delete_admin = true;
	}
	$file_htgroup   = CFG_DATA_PATH.'.htgroup';
	$file_htpasswd  = CFG_DATA_PATH.'.htpasswd';
  
	if($delete_admin) header("Location: user.php?pf=browse&delete_admin=true");
	else header("Location: user.php?pf=browse&success=true");
}

function ShowFormAddUser() {
	global $tpl, $error_list;

	$tpl->assign('error', $error_list);
	$tpl->assign('pf', $_REQUEST['pf']);
	$tpl->assign('username', $_REQUEST['username']);
	$tpl->assign('firstname', $_REQUEST['firstname']);
	$tpl->assign('lastname', $_REQUEST['lastname']);
	$tpl->assign('email', $_REQUEST['email']);
	$tpl->assign('address1', $_REQUEST['address1']);
	$tpl->assign('address2', $_REQUEST['address2']);
	$tpl->assign('city', $_REQUEST['city']);
	$tpl->assign('state', $_REQUEST['state']);
	$tpl->assign('zip', $_REQUEST['zip']);
	$tpl->assign('phone', $_REQUEST['phone']);
	$tpl->display('admin/user.html');
}

function AddUser() {
  
	global $tpl, $user, $error_list;

	$username    = stripslashes($_REQUEST['username']);
	$firstname   = stripslashes($_REQUEST['firstname']);
	$lastname    = stripslashes($_REQUEST['lastname']);
	$email       = stripslashes($_REQUEST['email']);
	$address1 = stripslashes($_REQUEST['address1']);
	$address2 = stripslashes($_REQUEST['address2']);
	$city = stripslashes($_REQUEST['city']);
	$state = stripslashes($_REQUEST['state']);
	$zip = stripslashes($_REQUEST['zip']);
	$phone = stripslashes($_REQUEST['phone']);
  
	$i = 0;
  
	if($username == "")
	{
		$error_list[$i] = "Username is required";
		$i++;
	}
	if($firstname == "")
	{
		$error_list[$i] = "Firstname is required";
		$i++;
	}
	if($lastname == "")
	{
		$error_list[$i] = "Lastname is required";
		$i++;
	}
	if($email == "")
	{
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
  
	if($user->CheckUser($username,$email))
	{
		$error_list[$i] = "Username or email is already exist";
		$i++;    
	}
	elseif($repassword != $password)
	{
		$error_list[$i] = "Password does not match";
		$i++;
	}
	elseif(!IsEmailAddress($email))
	{
		$error_list[$i] = "Invalid format email";
		$i++;
	}

	if(!is_array($error_list)) 
	{
		$_REQUEST['user_id'] = $user->Add($username,'',$firstname,$lastname,$email, $address1, $address2, $city, $state, $zip, $phone);
		$_REQUEST['pf'] = 'detail';
		$GLOBALS['user_data'] = $GLOBALS['user']->GetUser($_REQUEST['user_id']);
		ShowFormDetailUser();
	}
	else 
	{
		ShowFormAddUser();  
	}
  
}

function ShowFormAddOrder() {
	global $tpl, $product, $user, $error_list, $currency_code, $currency_unit;

	$products = $product->GetAllProducts(true);

	$i = 0;
	foreach ($products as $value) 
	{
		$product_data[$i]['product_id'] = $value['product_id'];
		$product_data[$i]['name']       = $value['name'];
		$product_data[$i]['description'] = $value['description'];
		$product_data[$i]['price']      = $value['price'];
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

	$user_data  = $user->GetUser($_REQUEST['user_id']);
	$user_id    = $user_data['user_id'];
	$username   = $user_data['username'];
	$firstname  = $user_data['firstname'];
	$lastname   = $user_data['lastname'];
	$email      = $user_data['email'];

	$tpl->assign('error',$error_list);  
	$tpl->assign('currency_code',$currency_code);
	$tpl->assign('user_id',$user_id);
	$tpl->assign('username',$username);
	$tpl->assign('firstname',$firstname);
	$tpl->assign('lastname',$lastname);   
	$tpl->assign('email',$email);     
	$tpl->assign('product_data',$product_data); 
	$tpl->assign('date_order', date('m')); 
	$tpl->assign('pf',$_REQUEST['pf']);
	$tpl->display("admin/user.html");
}


function ProcessAddOrder() {
  global $tpl, $product, $user, $order, $error_list, $mail, $currency_code, $currency_unit;
  
  $user_id        = $_REQUEST['user_id'];
  $product_id     = $_REQUEST['product_id'];
  $confirm_user   = $_REQUEST['confirm_user'];
  $email          = $_REQUEST['email'];
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
  if (!$order->CheckActiveOrder($product_id,$user_id, $date_order))
  {
    $error_list[$i] = "Order is already active for the selected month";
    $i++;
  }
  else if($product_id=="")
  {
    $error_list[$i] = "Please choose membership type";
    $i++;  
  }
  if(!is_array($error_list))
  {
    $order_id = $order->AddOrder($user_id,$product_id,$date_order);

    $order_data       = $order->GetOrder($order_id);
    $product_name     = $order_data['name'];
    $product_desc     = $order_data['description'];
    $product_price    = $currency_code.". ".$order_data['price'];
    $product_expire   = date("Y-m-d",$order_data['date_expire']);
    
    $data_user = $user->CheckEmailExist($email);
    $username  = $data_user['username'];
    $firstname = $data_user['firstname'];
    $lastname  = $data_user['lastname'];
    
    if ($confirm_user) {         
      $from_email  = CFG_NOTIFY_EMAIL;
      $from_name   = CFG_NOTIFY_FROM;
      
      $mail->ConfirmOrderEmail(CFG_SITE_NAME,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$product_expire,$email);
      
      $mail->ReceivedOrderEmail(CFG_SITE_NAME,$firstname,$lastname,$from_email,$from_name,$product_name,$product_desc,$product_price,$date_order,$product_expire,CFG_SITE_MAIL);

      $order->UpdateLastEmailSent($order_id,time());
    }
    $message = "Adding order to user success.<br />";
    $message .= "<input type='button' value='back' onclick=\"javascript:window.location.href='order.php?pf=browse'\">";
    $tpl->assign('message',$message);
    $tpl->display('admin/generic.html'); 
  }
  else 
  {
    ShowFormAddOrder();  
  }
}

function paging($total_data,$total_data_in_page)
{
  if($total_data <=$total_data_in_page)
  {
    return "";
  }
  else
  {
    if($total_data % $total_data_in_page == 0)
    {
      $page_total = integer_divide($total_data, $total_data_in_page);
    }
    else
    {
      $page_total = integer_divide($total_data, $total_data_in_page) + 1;
    }
    $paging = "Page : ";
    for($page=0;$page<=$page_total-1;$page++)
    {
      $page_ = $page+1;
      $paging = $paging."<a href='user.php?pf=browse&page=".$page_."'>$page_</a>&nbsp;&nbsp;";
    }
    return $paging;
  }
}

?>
