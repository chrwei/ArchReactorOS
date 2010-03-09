<?php
class Banned
{
  
  function BanIp()
  {
    $ip_address = $this->GetIpAddress();
    $ip_number  = $this->GetIpNumber($ip_address);
    $ip_banned  = $this->GetBanIp();
    $banned     = false;
    
    $i=0;
    foreach ($ip_banned as $value) {
      $ban_ip_number_start = $value['ip_number_start'];
      $ban_ip_number_end   = $value['ip_number_end'];
      if($ip_number >= $ban_ip_number_start && $ip_number <= $ban_ip_number_end)
      {
        $banned = true;
      }
    } 
    return $banned;
  }
  
  function BanCountry()
  {
    $ip_address = $this->GetIpAddress();
    $ip_number  = $this->GetIpNumber($ip_address);
    $ip_data    = $this->GetIpCountry($ip_number);
    $ip_banned  = $this->GetBanCountry();
    $banned     = false;
    
    $i=0;
    foreach ($ip_banned as $value) {
      if($ip_data['ip_country_code'] == $value['country_code'])
      {
        $banned = true;
      }
    } 
    return $banned;
  }
  
  function GetIpAddress()
  {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif (isset($_SERVER['HTTP_VIA'])) {
      $ip = $_SERVER['HTTP_VIA'];
    }
    elseif (isset($_SERVER['REMOTE_ADDR'])) {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    else {
      $ip = "Banned";
    }
    
    return $ip;
  }
  
  function GetIpNumber($ip_address)
  {
    if ($ip_address == "") {
        return 0;
    } 
    else {
        $ips = split ("\.", "$ip_address");
        return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
    }
  }
  
  function GetIpCountry($ip_number)
  {
    $filename   = CFG_SITE_PATH . "lib/ip/GeoIPCountryWhois.csv";
    $contents   = file($filename);
    $row_start  = 0;
    $row_end    = count($contents);
    
    list($a, $b, $c, $d, $e, $f, $g) = explode(",", $contents[round($row_end/2)]);
    
    if ($ip_number <= $d)
    {
      $row_start  = $row_start;
      $row_end    = $row_end/2;
    }
    else
    {
      $row_start  = $row_end/2;
      $row_end    = $row_end;  
    }
    
    for ($i = $row_start; $i <= $row_end; $i++)
    {
      list($a, $b, $c, $d, $e, $f, $g) = explode(",", $contents[$i]);
    
      if($ip_number >= $c && $ip_number <= $d)
      {
        $ip_data['ip_address']        = $ip_address;
        $ip_data['ip_number']         = $ip_number;
        $ip_data['ip_country_name']   = $f.$g;
        $ip_data['ip_country_code']   = $e;
        break;
      }
    }
    return $ip_data;
  }
  
  function GetBanCountry()
  {
    global $db;
    
    $query  = "select * from banned_country order by banned_country_id";
    $result = $db->Execute($query);
    return $result->GetRows();
  }

  function GetBanIp()
  {
    global $db;
    
    $query  = "select * from banned_ip order by banned_ip_id";
    $result = $db->Execute($query);
    return $result->GetRows();
  }
    
  
  function GetCountryCode()
  {
    $filename   = CFG_SITE_PATH . "lib/ip/CountryCode.txt";
    $contents   = file($filename);
    $i=0;
    foreach ($contents as $value)
    {
     list($code,$name) = explode(":", $value);
     $country_data[$i]['name'] = $name;
     $country_data[$i]['code'] = $code;
     $i++;
    }
    return $country_data;
  }
  
  function AddBannedCountry($code,$name)
  {
    global $db;
    
    $record['country_name'] = $name;
    $record['country_code'] = $code;
    
    $db->AutoExecute('banned_country',$record,'INSERT'); 
  }
  function DeleteBannedCountry($id)
  {
    global $db;
    
    $query = "delete from banned_country where banned_country_id=".intval($id);
    $db->Execute($query);
  }
  
  function DeleteBannedIp($id)
  {
    global $db;
    
    $query = "delete from banned_ip where banned_ip_id=".intval($id);
    $db->Execute($query);
  }
  
  function CheckBannedCountry($name)
  {
    global $db;
    
    $query  = "select * from banned_country where country_name = '".mysql_escape_string($name)."'";
    $result = $db->Execute($query);
    $found  = $result->FetchRow();
    
    if($found)
      return true;
    else
      return false;
  }
  
  function CheckBannedIp($ip_address_start,$ip_address_end)
  {
    global $db;
    
    $query  = "select * from banned_ip where ip_address_start = '".mysql_escape_string($ip_address_start)."' and ip_address_end = '".mysql_escape_string($ip_address_end)."'";
    $result = $db->Execute($query);
    $found  = $result->FetchRow();
    
    if($found)
      return true;
    else
      return false;
  }
  
  function AddBannedIp($ip_address_start,$ip_address_end,$ip_number_start,$ip_number_end)
  {
    global $db;
    
    $record['ip_address_start'] = $ip_address_start;
    $record['ip_address_end']   = $ip_address_end;
    $record['ip_number_start']  = $ip_number_start;
    $record['ip_number_end']    = $ip_number_end;
    
    
    $db->AutoExecute('banned_ip',$record,'INSERT');     
  }
}
?>
