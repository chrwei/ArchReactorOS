<?php
include 'init.php';
$user->AuthenticationUser();

$user_data = Array();

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
	$GLOBALS['tpl']->display('user.html');
}

function ShowDetailUser() {
	$curr_user = $GLOBALS['user']->CheckUserActive($_SESSION['SESSION_USERNAME']);
	$process = $_REQUEST['process'];
	$GLOBALS['user_data'] = $GLOBALS['user']->GetUser($curr_user['user_id']);

	if ($process == 'edit') {
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
		} elseif($repassword != $password){
			$GLOBALS['error_list'][] = _("password doesnt match");
		}
		if(!is_array($GLOBALS['error_list'])) {
			$GLOBALS['user']->Update($curr_user['user_id'], $username, $password, $firstname, $lastname, $email, $address1, $address2, $city, $state, $zip, $phone);
			$GLOBALS['success'] = true;
			//refresh from DB
			$GLOBALS['user_data'] = $GLOBALS['user']->GetUser($curr_user['user_id']);
		} else {
			$GLOBALS['user_data']['firstname'] = $firstname;
			$GLOBALS['user_data']['lastname'] = $lastname;
			$GLOBALS['user_data']['email'] = $email;
			$GLOBALS['user_data']['address1'] = $address1;
			$GLOBALS['user_data']['address2'] = $address2;
			$GLOBALS['user_data']['city'] = $city;
			$GLOBALS['user_data']['state'] = $state;
			$GLOBALS['user_data']['zip'] = $zip;
			$GLOBALS['user_data']['phone'] = $phone;
		}
	}
}
/*===================================================
	main
===================================================*/
ShowDetailUser();
ShowFormDetailUser();
?>
