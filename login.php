<?php
include 'init.php';

function ShowFormLogin() {
  global $tpl, $error_list;
    
  $tpl->assign('error',$error_list);
  //$tpl->assign('front_text',CFG_FRONT_TEXT);
  $tpl->assign('username', addslashes($_REQUEST['username']));
  $tpl->assign('b', $_REQUEST['b']);
  $tpl->display('login.html');
}

function ProcessFormLogin() {
  global $tpl, $user, $error_list;
  
  $username = stripslashes($_REQUEST['username']);
  $password = stripslashes($_REQUEST['password']);
  $remember = $_REQUEST['remember'];
  $b        = $_REQUEST['b'];
  $i        = 0;

  if($username == "" || $password == "" ){
    if($username == ""){
      $error_list[$i] = _("Username is required");
      $i++;
    }
    if($password == ""){
      $error_list[$i] = _("Password is required");
      $i++;
    }
  }
  elseif(!$user->CheckUserLogin($username)){
    $error_list[$i] = _("User doesnt exist");
    $i++;    
  }
  elseif(!$user->CheckPasswordLogin($username,$password)){
    $error_list[$i] = _("Invalid password");
    $i++;    
  }
  
  if(!is_array($error_list)) {
    
    if ($remember) {
      $expire = time() + (3600 * 24 * 1000);
    } 
    else {
      $expire = 0;
    }
    $login = $user->Login($username, $password, $expire);
    if ($login == 0) {
      if(!$b)
        header("Location: index.php");
      else
        header("Location: ".$b);
    }
    elseif($login == 1){
      if(!$b)
        header("Location: admin/index.php");
      else
        header("Location: ".$b);
    }
    else{
      ShowFormLogin();  
    }
  }
  else {
    ShowFormLogin();  
  }
}

/***********************************************************************************************
Main
***********************************************************************************************/
if (empty($_REQUEST['pf'])) {
  ShowFormLogin();
}
elseif ($_REQUEST['pf'] == 'login') {
  ProcessFormLogin();
}
?>
