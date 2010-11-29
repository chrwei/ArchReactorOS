<?php

include '../init.php';
$pf = $_REQUEST['pf'];

function ShowFormBanned() {
  global $banned, $ban_country, $ban_ip ,$tpl;
  
  $tpl->assign('ban_country',$ban_country);
  $tpl->assign('ban_ip',$ban_ip);
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('success_country',$_REQUEST['success_country']);
  $tpl->assign('success_ip',$_REQUEST['success_ip']);
  $tpl->display('admin/banned.html');
}

function ShowBanned() {
  global $banned, $ban_country, $ban_ip;

  $ban_country_data = $banned->GetBanCountry();
  $i=0;
  foreach ($ban_country_data as $value) {
    $ban_country[$i]['no']            = $i+1;
    $ban_country[$i]['banned_id']     = $value['banned_country_id']; 
    $ban_country[$i]['country_name']  = $value['country_name'];
    $ban_country[$i]['country_code']  = $value['country_code'];
    if($i % 2 != 0)
    {
      $ban_country[$i]['color'] = '#f7f7f7';
    }
    else
    {
      $ban_country[$i]['color'] = '#ffffff';
    }       
    $i++;
  }
  
  $ban_ip_data = $banned->GetBanIp();
  $i=0;
  foreach ($ban_ip_data as $value) {
    $ban_ip[$i]['no']                = $i+1;
    $ban_ip[$i]['banned_id']         = $value['banned_ip_id']; 
    $ban_ip[$i]['ip_address_start']  = $value['ip_address_start'];
    $ban_ip[$i]['ip_address_end']    = $value['ip_address_end'];
    if($i % 2 != 0)
    {
      $ban_ip[$i]['color'] = '#f7f7f7';
    }
    else
    {
      $ban_ip[$i]['color'] = '#ffffff';
    }       
    $i++;
  }  
}

function ShowFormAddBannedCountry()
{
  global $tpl, $country, $error;
  
  $tpl->assign('list_country',$list_country);
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('country',$country);
  $tpl->assign('error',$error);
  $tpl->display('admin/banned.html'); 
}

function ShowAddBannedCountry()
{
  global $banned, $country, $error;
  
  $process = $_REQUEST['process'];
  
  $country_data = $banned->GetCountryCode();
  $i=0;
  foreach ($country_data as $value)
  {
    $country[$i]['country_name'] = $value['name']; 
    $country[$i]['country_code'] = $value['code']; 
    $i++;
  }
  
  if($process == "add")
  {
    $list_country = $_REQUEST['country_code'];
    if(!is_array($list_country))
    {
      $error[0] = "Please choose";
    }
    else
    {
      foreach($list_country as $value)
      {
        list($code,$name) = explode(":", $value);
        if(!$banned->CheckBannedCountry($name))
        {
          $banned->AddBannedCountry($code,$name);
        }
      }
      header("Location: banned.php?pf=country");
    }
  }
}

function ShowFormAddBannedIp()
{
  global $tpl, $error_list_banned_single_ip, $error_list_banned_range_ip, $ip_a, $ip_b, $ip_c, $ip_d, $ip_start_a, $ip_start_b, $ip_start_c, $ip_start_d, $ip_end_a, $ip_end_b, $ip_end_c, $ip_end_d;
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('ip_a',$ip_a);
  $tpl->assign('ip_b',$ip_b);
  $tpl->assign('ip_c',$ip_c);
  $tpl->assign('ip_d',$ip_d);
  $tpl->assign('ip_start_a',$ip_start_a);
  $tpl->assign('ip_start_b',$ip_start_b);
  $tpl->assign('ip_start_c',$ip_start_c);
  $tpl->assign('ip_start_d',$ip_start_d);
  $tpl->assign('ip_end_a',$ip_end_a);
  $tpl->assign('ip_end_b',$ip_end_b);
  $tpl->assign('ip_end_c',$ip_end_c);
  $tpl->assign('ip_end_d',$ip_end_d);
  $tpl->assign('error_list_banned_single_ip',$error_list_banned_single_ip);
  $tpl->assign('error_list_banned_range_ip',$error_list_banned_range_ip);
  $tpl->display('admin/banned.html'); 
}

function ShowAddBannedIp()
{
  global $banned, $error_list_banned_single_ip, $error_list_banned_range_ip, $ip_a, $ip_b, $ip_c, $ip_d, $ip_start_a, $ip_start_b, $ip_start_c, $ip_start_d, $ip_end_a, $ip_end_b, $ip_end_c, $ip_end_d;
  
  $process = $_REQUEST['process'];
  
  if($process == "add_banned_single_ip")
  {
    $i    =0;
    $ip_a = $_REQUEST['ip_a'];
    $ip_b = $_REQUEST['ip_b'];
    $ip_c = $_REQUEST['ip_c'];
    $ip_d = $_REQUEST['ip_d'];

    if(!IsDigit($ip_a) || !IsDigit($ip_b) || !IsDigit($ip_c) || !IsDigit($ip_d)){
      $error_list_banned_single_ip[$i] = "IP must be a digit";
      $i++;    
    }
    elseif($ip_a >=256 || $ip_b >=256 || $ip_c >=256 || $ip_d >=256){
      $error_list_banned_single_ip[$i] = "IP is not valid";
      $i++;    
    }
    elseif($banned->CheckBannedIp($ip_a.".".$ip_b.".".$ip_c.".".$ip_d,$ip_a.".".$ip_b.".".$ip_c.".".$ip_d))
    {
      $error_list_banned_single_ip[$i] = "This Banned IP already exists";
      $i++; 
    }
    
    if(!is_array($error_list_banned_single_ip))
    {
      $ip_address_start = $ip_a.".".$ip_b.".".$ip_c.".".$ip_d;
      $ip_address_end   = $ip_a.".".$ip_b.".".$ip_c.".".$ip_d;
      $ip_number_start  = $banned->GetIpNumber($ip_address_start);
      $ip_number_end    = $banned->GetIpNumber($ip_address_end);
      $banned->AddBannedIp($ip_address_start,$ip_address_end,$ip_number_start,$ip_number_end);
      header("Location: banned.php?pf=ip");;
    }
  }
  elseif($process == "add_banned_range_ip")
  {
    $i          =0;
    $ip_start_a = $_REQUEST['ip_start_a'];
    $ip_start_b = $_REQUEST['ip_start_b'];
    $ip_start_c = $_REQUEST['ip_start_c'];
    $ip_start_d = $_REQUEST['ip_start_d'];
    $ip_end_a   = $_REQUEST['ip_end_a'];
    $ip_end_b   = $_REQUEST['ip_end_b'];
    $ip_end_c   = $_REQUEST['ip_end_c'];
    $ip_end_d   = $_REQUEST['ip_end_d'];

    if(!IsDigit($ip_start_a) || !IsDigit($ip_start_b) || !IsDigit($ip_start_c) || !IsDigit($ip_start_d) || !IsDigit($ip_end_a) || !IsDigit($ip_end_b) || !IsDigit($ip_end_c) || !IsDigit($ip_end_d)){
      $error_list_banned_range_ip[$i] = "IP must be a digit";
      $i++;    
    }
    elseif($ip_start_a >=256 || $ip_start_b >=256 || $ip_start_c >=256 || $ip_start_d >=256 || $ip_end_a >=256 || $ip_end_b >=256 || $ip_end_c >=256 || $ip_end_d >=256){
      $error_list_banned_range_ip[$i] = "IP is not valid";
      $i++;    
    }
    elseif($banned->GetIpNumber($ip_start_a.".".$ip_start_b.".".$ip_start_c.".".$ip_start_d) >  $banned->GetIpNumber($ip_end_a.".".$ip_end_b.".".$ip_end_c.".".$ip_end_d)){
      $error_list_banned_range_ip[$i] = "IP address range start must be larger than its end address";
      $i++; 
    }
    elseif($banned->CheckBannedIp($ip_start_a.".".$ip_start_b.".".$ip_start_c.".".$ip_start_d,$ip_end_a.".".$ip_end_b.".".$ip_end_c.".".$ip_end_d)){
      $error_list_banned_range_ip[$i] = "This Banned IP already exists";
      $i++; 
    }
    
    if(!is_array($error_list_banned_range_ip))
    {
      $ip_address_start = $ip_start_a.".".$ip_start_b.".".$ip_start_c.".".$ip_start_d;
      $ip_address_end   = $ip_end_a.".".$ip_end_b.".".$ip_end_c.".".$ip_end_d;
      $ip_number_start  = $banned->GetIpNumber($ip_address_start);
      $ip_number_end    = $banned->GetIpNumber($ip_address_end);
      $banned->AddBannedIp($ip_address_start,$ip_address_end,$ip_number_start,$ip_number_end);
      header("Location: banned.php?pf=ip");
    }
  }
}

function DeleteBannedCountry($delete_list)
{
  global $banned;
  
  foreach ($delete_list as $value)
  {
    $banned->DeleteBannedCountry($value);
  }
  header("Location: banned.php?pf=country&success_country=1");
}

function DeleteBannedIp($delete_list)
{
  global $banned;

  foreach ($delete_list as $value)
  {
    $banned->DeleteBannedIp($value);
  }
  header("Location: banned.php?pf=ip&success_ip=1");
}
/*###########################################################
Section : Main
###########################################################*/
$user->AuthenticationAdmin();
if (empty($pf)) {
  ShowBanned();
  ShowFormBanned();
}
elseif ($pf == 'country') {
  ShowBanned();
  ShowFormBanned();
}
elseif ($pf == 'ip') {
  ShowBanned();
  ShowFormBanned();
}
elseif ($pf == 'add_banned_country') {
  ShowAddBannedCountry();
  ShowFormAddBannedCountry();
}
elseif ($pf == 'add_banned_ip') {
  ShowAddBannedIp();
  ShowFormAddBannedIp();
}
elseif ($pf == 'delete_banned_country') {
  DeleteBannedCountry($_REQUEST['delete']);
}
elseif ($pf == 'delete_banned_ip') {
  DeleteBannedIp($_REQUEST['delete']);
}
?>
