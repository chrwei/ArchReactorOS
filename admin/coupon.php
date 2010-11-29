<?php
include '../init.php';
$pf = $_REQUEST['pf'];

function ShowFormAddCoupon() 
{
  global $tpl, $error_list;
  
  $tpl->assign('pf', $_REQUEST['pf']);
  $tpl->assign('error', $error_list);
  $tpl->assign('coupon_code', $_REQUEST['coupon_code']);
  $tpl->assign('coupon_value_type', $_REQUEST['coupon_value_type']);
  $tpl->assign('percentage_coupon_value', $_REQUEST['percentage_coupon_value']);
  $tpl->assign('price_coupon_value', $_REQUEST['price_coupon_value']);
  $tpl->assign('month_start_date', $_REQUEST['month_start_date']);
  $tpl->assign('day_start_date', $_REQUEST['day_start_date']);
  $tpl->assign('year_start_date', $_REQUEST['year_start_date']);
  $tpl->assign('month_expire_date', $_REQUEST['month_expire_date']);
  $tpl->assign('day_expire_date', $_REQUEST['day_expire_date']);
  $tpl->assign('year_expire_date', $_REQUEST['year_expire_date']);
  $tpl->assign('expire_usage', $_REQUEST['expire_usage']);
  $tpl->display('admin/coupon.html');
}

function AddCoupon() {

  global $tpl, $coupon, $error_list;
  
  $coupon_code              = $_REQUEST['coupon_code'];
  $coupon_value_type        = $_REQUEST['coupon_value_type'];
  $percentage_coupon_value  = $_REQUEST['percentage_coupon_value'];
  $price_coupon_value       = $_REQUEST['price_coupon_value'];
  $month_start_date         = $_REQUEST['month_start_date'];
  $year_start_date          = $_REQUEST['year_start_date'];
  $day_start_date           = $_REQUEST['day_start_date'];
  $month_expire_date        = $_REQUEST['month_expire_date'];
  $day_expire_date          = $_REQUEST['day_expire_date'];
  $year_expire_date         = $_REQUEST['year_expire_date'];
  $expire_usage             = $_REQUEST['expire_usage'];
  $start_date               = mktime(23,59,59, $month_start_date,$day_start_date,$year_start_date);
  $expire_date              = mktime(23,59,59, $month_expire_date,$day_expire_date,$year_expire_date);
  
  $i = 0;
  if($coupon_code == "" || $coupon_value_type == "" || $price_coupon_value == "" || $percentage_coupon_value == "" || $price_coupon_value != "" || $percentage_coupon_value != "")
  {
    if($coupon_code == "")
    {
      $error_list[$i] = "Coupon code is required";
      $i++;
    }
    if($coupon_value_type == "")
    {
      $error_list[$i] = "Please select coupon type option";
      $i++;
    }
    elseif($coupon_value_type == "price")
    {
      $coupon_value = $price_coupon_value;
      if($coupon_value == "")
      {
        $error_list[$i] = "Coupon value price is required";
        $i++;
      }
      elseif(!IsDigit($coupon_value))
      {
        $error_list[$i] = "Coupon value price must be digit value";
        $i++;
      }
    }
    elseif($coupon_value_type == "percentage")
    {
      $coupon_value = $percentage_coupon_value;
      if($coupon_value == ""){
        $error_list[$i] = "Coupon value percentage is required";
        $i++;
      } 
      elseif(!IsDigit($coupon_value)){
        $error_list[$i] = "Coupon value percentage must be digit value";
        $i++;
      }
      else
      {
        $coupon_value = $coupon_value."%";
      }
    }       
  }
  if($start_date >= $expire_date)
  {
    $error_list[$i] = "Start date must be less than expire date";
  }
  if($expire_usage!="" && !IsDigit($expire_usage)){
    $error_list[$i] = "Expire usage must be digit";
    $i++;    
  }
  if($coupon->CheckCouponCode($coupon_code)){
    $error_list[$i] = "Coupon code is already exist";
    $i++;    
  }
  if(!is_array($error_list)) 
  {
    if($expire_usage=="")
      $expire_usage = 0;
    $coupon_id = $coupon->Add($coupon_code,$coupon_value,$start_date,$expire_date,$expire_usage);
    $message  = "Adding new coupon successful <br />";
    $message .= "<input type='button' value='Back' onclick=\"javascript:window.location.href='coupon.php'\"> <input type='button' value='Manage Coupon' onclick=\"javascript:window.location.href='coupon.php?pf=detail&id=$coupon_id'\">";$tpl->assign('message',$message);
    $tpl->display('admin/generic.html');    
    header("Location: coupon.php");
  }
  else 
  {
    ShowFormAddCoupon();  
  }
}


function ShowAllCoupon() 
{
  global $tpl, $coupons, $success, $paging;
  
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('success',$_REQUEST['success']);
  $tpl->assign('coupons',$coupons);
  $tpl->assign('paging',$paging);
  $tpl->display('admin/coupon.html');
}

function GetAllCoupon() 
{
  global $coupon,$coupons,$paging;

  $total_data         = $coupon->GetCouponTotal();
  $total_data_in_page = LIMIT_COUPON_PAGE;
  $paging             = paging($total_data,$total_data_in_page);
  $page               = $_REQUEST['page']-1;
  if($page<=0)
    $coupon_data = $coupon->GetAllCoupon(0,$total_data_in_page);
  else
    $coupon_data = $coupon->GetAllCoupon($page*$total_data_in_page,$total_data_in_page);

  $i = 0;
  foreach ($coupon_data as $value) 
  {
    $coupons[$i]['no'] = $i+1;
    $coupons[$i]['coupon_id']     = $value['coupon_id'];
    $coupons[$i]['coupon_code']   = $value['coupon_code'];
    $coupons[$i]['coupon_value']  = $value['coupon_value'];
    $coupons[$i]['period']    = date("F-d-y",$value['start_date'])." to ".date("F-d-y",$value['expire_date']);
    if($value['expire_usage'] == 0)
      $value['expire_usage'] = 'unlimited';
    $coupons[$i]['usage']         = $value['usage_count']." / ".$value['expire_usage'];
    if($i % 2 != 0)
    {
      $coupons[$i]['color'] = '#f7f7f7';
    }
    else
    {
      $coupons[$i]['color'] = '#ffffff';
    }
    if(time()<$value['expire_date']&&time()>$value['start_date']&&($value['expire_usage']== 0 || $value['expire_usage']>$value['usage_count']))
      $coupons[$i]['coupon_active'] = "<font color='green'>Active</font>";
    else
      $coupons[$i]['coupon_active'] = "<font color='red'>Inactive</font>";
    
    $i++;
  }
}


function ShowCouponSearchResult() {
  global $tpl, $coupons,$error_list;
  

  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('search_for',$_REQUEST['search_for']);
  $tpl->assign('search_in',$_REQUEST['search_in']);
  $tpl->assign('error',$error_list);
  $tpl->assign('coupons',$coupons);
  $tpl->display('admin/coupon.html');
}

function GetCouponSearchResult() {
  global $coupon,$coupons,$error_list;
  

  $search_for = $_REQUEST['search_for'];
  $search_in = $_REQUEST['search_in'];
  $i=0;
  if($search_for == ""){
        $error_list[$i] = "Search String is required";
        $i++;
  }
  
  if(!is_array($error_list))
  {
    $coupon_data = $coupon->GetProductSearchResult($search_for,$search_in);
    $i = 0;
    foreach ($coupon_data as $value) {
      $coupons[$i]['no'] = $i+1;
      $coupons[$i]['coupon_id']     = $value['coupon_id'];
      $coupons[$i]['coupon_code']   = $value['coupon_code'];
      $coupons[$i]['coupon_value']  = $value['coupon_value'];
      $coupons[$i]['period']    = date("F-d-y",$value['start_date'])." to ".date("F-d-y",$value['expire_date']);
      if($value['expire_usage'] == 0)
        $value['expire_usage'] = 'unlimited';
      $coupons[$i]['usage']         = $value['usage_count']." / ".$value['expire_usage'];
      if($i % 2 != 0)
      {
        $coupons[$i]['color'] = '#f7f7f7';
      }
      else
      {
        $coupons[$i]['color'] = '#ffffff';
      }
      if(time()<$value['expire_date']&&time()>$value['start_date']&&($value['expire_usage']== 0 || $value['expire_usage']>=$value['usage_count']))
        $coupons[$i]['coupon_active'] = "<font color='green'>Active</font>";
      else
        $coupons[$i]['coupon_active'] = "<font color='red'>Expire</font>";
      
      $i++;
    }
  }
}


function ShowCouponDetail() {
  global $tpl, $coupon, $coupon_id, $coupon_code, $coupon_value, $period, $usage, $coupon_product;

  $tpl->assign('pf',$_REQUEST['pf']);
  
  $tpl->assign('coupon_id',$coupon_id);
  $tpl->assign('coupon_code',$coupon_code);
  $tpl->assign('coupon_value',$coupon_value);
  $tpl->assign('usage',$usage);
  $tpl->assign('period',$period);
  $tpl->assign('coupon_product',$coupon_product);
  $tpl->display('admin/coupon.html');
}

function GetCouponDetail() {
  global $coupon, $coupon_id, $coupon_code, $coupon_value, $period, $usage, $coupon_product;
  
  $coupon_id = $_REQUEST['id'];
  $coupon_detail_data = $coupon->GetCouponDetail($coupon_id);
  
  $coupon_code    = $coupon_detail_data['coupon_code'];
  $coupon_value   = $coupon_detail_data['coupon_value'];
  $period = date("F - d - Y ",$coupon_detail_data['start_date'])." To ".date(" F - d - Y",$coupon_detail_data['expire_date']);
  if($coupon_detail_data['expire_usage'] == 0)
      $coupon_detail_data['expire_usage'] = 'unlimited';
  $usage          = $coupon_detail_data['usage_count']." / ".$coupon_detail_data['expire_usage'];
  
  $coupon_product_data =  $coupon->GetCouponProduct($coupon_id);
  $i = 0;
  foreach ($coupon_product_data as $value) {
    $coupon_product[$i]['no'] = $i+1;
    $coupon_product[$i]['product_id'] = $value['product_id'];
    $coupon_product[$i]['name'] = $value['name'];
    $coupon_product[$i]['price'] = $value['price'];
    $percentage = strrpos($value['coupon_value'], "%");
    if($percentage)
    {
      $percent = str_replace("%", "", $value['coupon_value']);
      $coupon_product[$i]['discount'] = $value['price'] - ($value['price'] * ($percent/100));
    }
    else
    {
      $coupon_product[$i]['discount'] = $value['price'] - $value['coupon_value'];
    }
    if($i % 2 != 0)
    {
      $coupon_product[$i]['color'] = '#f7f7f7';
    }
    else
    {
      $coupon_product[$i]['color'] = '#ffffff';
    }
    $i++;
  }
  
  $process = $_REQUEST['process'];
  $list_product_id = $_REQUEST['delete_discount'];
  
  $i=0;
  if($process == "delete")
  {
    foreach($list_product_id as $value)
    {
      $coupon->DeleteProductDiscount($coupon_id, $value);
      $i++;
    }
    header("Location: coupon.php?pf=detail&id=$coupon_id");
  }
}


function ShowFormEditCoupon() {
  global $tpl, $coupon, $coupon_id, $coupon_code, $coupon_value, $coupon_value_type, $percentage_coupon_value, $price_coupon_value, $month_start_date, $day_start_date, $year_start_date, $month_expire_date, $day_expire_date, $year_expire_date, $expire_usage, $error_list, $success;

  $tpl->assign('pf',$_REQUEST['pf']);
  
  $tpl->assign('coupon_id',$coupon_id);
  $tpl->assign('coupon_code',$coupon_code);
  $tpl->assign('percentage_coupon_value',$percentage_coupon_value);
  $tpl->assign('price_coupon_value',$price_coupon_value);
  $tpl->assign('coupon_value_type',$coupon_value_type);
  $tpl->assign('month_start_date',$month_start_date);
  $tpl->assign('day_start_date',$day_start_date);
  $tpl->assign('year_start_date',$year_start_date);
  $tpl->assign('month_expire_date',$month_expire_date);
  $tpl->assign('day_expire_date',$day_expire_date);
  $tpl->assign('year_expire_date',$year_expire_date);
  $tpl->assign('expire_usage',$expire_usage);
  $tpl->assign('error',$error_list);
  $tpl->assign('success',$success);
  $tpl->display('admin/coupon.html');
}

function EditCoupon() {
  global $coupon, $tpl, $coupon_id, $coupon_code, $coupon_value, $coupon_value_type, $percentage_coupon_value, $price_coupon_value, $month_start_date, $day_start_date, $year_start_date, $month_expire_date, $day_expire_date, $year_expire_date, $expire_usage, $error_list, $success;
  
  $coupon_id = $_REQUEST['id'];
  $coupon_detail_data = $coupon->GetCouponDetail($coupon_id);
  
  $process = $_REQUEST['process'];
  $i=0;
  if($process == "edit")
  {
    $coupon_code              = $_REQUEST['coupon_code'];
    $coupon_value_type        = $_REQUEST['coupon_value_type'];
    $percentage_coupon_value  = $_REQUEST['percentage_coupon_value'];
    $price_coupon_value       = $_REQUEST['price_coupon_value'];
    $month_start_date         = $_REQUEST['month_start_date'];
    $year_start_date          = $_REQUEST['year_start_date'];
    $day_start_date           = $_REQUEST['day_start_date'];
    $month_expire_date        = $_REQUEST['month_expire_date'];
    $day_expire_date          = $_REQUEST['day_expire_date'];
    $year_expire_date         = $_REQUEST['year_expire_date'];
    $expire_usage             = $_REQUEST['expire_usage'];
    $start_date               = mktime(23,59,59, $month_start_date,$day_start_date,$year_start_date);
    $expire_date              = mktime(23,59,59, $month_expire_date,$day_expire_date,$year_expire_date);
    
    $i = 0;
    if($coupon_code == "" || $coupon_value_type == "")
    {
      if($coupon_code == "")
      {
        $error_list[$i] = "Coupon code required";
        $i++;
      }
      if($coupon_value_type == "")
      {
        $error_list[$i] = "Please checked coupon type option";
        $i++;
      }
    }
    elseif($coupon_value_type == "price")
    {
        $coupon_value = $price_coupon_value;
        if($coupon_value == ""){
          $error_list[$i] = "Coupon value Price is required";
          $i++;
        }
        elseif(!IsDigit($coupon_value)){
          $error_list[$i] = "Coupon value Price must be digit value";
          $i++;
        }
    }
    elseif($coupon_value_type == "percentage")
    {
      $coupon_value = $percentage_coupon_value;
      if($coupon_value == "")
      {
        $error_list[$i] = "Coupon value percentage is required";
        $i++;
      }
      elseif($coupon_value>100)
      {
        $error_list[$i] = "Coupon value percentage max 100";
        $i++;
      }
      elseif(!IsDigit($coupon_value))
      {
        $error_list[$i] = "Coupon value percentage must be digit value";
        $i++;
      }
      else
      {
        $coupon_value = $coupon_value."%";
      }    
    }
       
    if($start_date >= $expire_date)
    {
      $error_list[$i] = "Start Date must be less than expire date";
    }
    
    if($expire_usage!="" && !IsDigit($expire_usage)){
      $error_list[$i] = "Expire usage must be digit";
      $i++;    
    }
    elseif($expire_usage < $coupon_detail_data['usage_count'] && ($expire_usage!="" && $expire_usage!= "0"))
    {
        $error_list[$i] = "Expire usage invalid<br> because this coupon was be used  ".$coupon_detail_data['usage_count']." of";
        $i++; 
    }
    if($coupon_code != $coupon_detail_data['coupon_code'])
    {
      if($coupon->CheckCouponCode($coupon_code)){
        $error_list[$i] = "Coupon code already exist";
        $i++;    
      }
    }
    if(!is_array($error_list)) {
      
      if($coupon_detail_data['coupon_value'] != $coupon_value)
      {
        if(!$coupon->CouponValueIsValid($coupon_id,$coupon_value))
        {
          $error_list[$i] = "Price value is outsize. <br> a product using this coupon has net price less than zero";
          $i++;
        }
      }
      
      if(!is_array($error_list))
      {
        if($expire_usage=="")
          $expire_usage_ = 0;
        else
          $expire_usage_ = $expire_usage;
        $coupon->Edit($coupon_id,$coupon_code,$coupon_value,$start_date,$expire_date,$expire_usage_);
        $success = true;
      } 
    }   
  }
  else
  {
    $coupon_code    = $coupon_detail_data['coupon_code'];
    $percentage = strrpos($coupon_detail_data['coupon_value'], "%");
    if($percentage)
    {
      $percent                  = str_replace("%", "", $coupon_detail_data['coupon_value']);
      $coupon_value_type        = "percentage";
      $percentage_coupon_value  = $percent;
    }
    else
    {
      $coupon_value_type = "price";
      $price_coupon_value = $coupon_detail_data['coupon_value'];
    }
    
    $month_start_date = date("n",$coupon_detail_data['start_date']);
    $day_start_date   = date("j",$coupon_detail_data['start_date']);
    $year_start_date  = date("Y",$coupon_detail_data['start_date']);
  
    $month_expire_date = date("n",$coupon_detail_data['expire_date']);
    $day_expire_date   = date("j",$coupon_detail_data['expire_date']);
    $year_expire_date  = date("Y",$coupon_detail_data['expire_date']);
    
    if($coupon_detail_data['expire_usage'] == 0)
      $expire_usage = '';
    else
      $expire_usage = $coupon_detail_data['expire_usage'];    
  }
}



function DeleteCoupon($coupon_id_list) {
  global $coupon,$tpl,$success;
  
  for($i=0;$i<=count($coupon_id_list)-1;$i++)
  {
    $coupon->Delete($coupon_id_list[$i]);
  }
  header("Location: coupon.php?pf=browse&success=true");
 
}


function AddProductDiscount() {
  global $coupon, $coupon_id, $coupon_code, $coupon_value, $period, $usage, $coupon_product, $error_list;
  
  $coupon_id            = $_REQUEST['id'];
  $coupon_detail_data   = $coupon->GetCouponDetail($coupon_id);
  $coupon_code          = $coupon_detail_data['coupon_code'];
  $coupon_value         = $coupon_detail_data['coupon_value'];
  $period               = date("F - d - Y",$coupon_detail_data['start_date'])." To ".date("F - d - Y",$coupon_detail_data['expire_date']);
  
  if($coupon_detail_data['expire_usage'] == 0)
  {
      $coupon_detail_data['expire_usage'] = 'unlimited';
  }
  $usage                = $coupon_detail_data['usage_count']." / ".$coupon_detail_data['expire_usage'];
  $coupon_product_data  =  $coupon->GetDiscountProduct($coupon_id);
  
  $i = 0;
  foreach ($coupon_product_data as $value) {
    $coupon_product[$i]['no'] = $i+1;
    $coupon_product[$i]['product_id'] = $value['product_id'];
    $coupon_product[$i]['name'] = $value['name'];
    $coupon_product[$i]['price'] = $value['price'];
    $percentage = strrpos($value['coupon_value'], "%");
    if($percentage)
    {
      $percent = str_replace("%", "", $value['coupon_value']);
      $coupon_product[$i]['discount'] = $value['price'] - ($value['price'] * ($percent/100));
    }
    else
    {
      $coupon_product[$i]['discount'] = $value['price'] - $value['coupon_value'];
    }
    if($i % 2 != 0)
    {
      $coupon_product[$i]['color'] = '#f7f7f7';
    }
    else
    {
      $coupon_product[$i]['color'] = '#ffffff';
    }
    $i++;
  }
  
  $process = $_REQUEST['process'];
  $list_product_id = $_REQUEST['add_discount'];
  
  if($process == "add")
  {
    $i=0;
    foreach($list_product_id as $value)
    {
      $percentage = strrpos($coupon_detail_data['coupon_value'], "%");
      if(!$percentage)
      {
        $net_price = $coupon->CheckNetPrice($coupon_detail_data['coupon_value'], $value);
        if($net_price['net_price'] < 0)
        {
          $error_list[$i] = "This coupon can't using in ".$net_price['name']." product, becauce price this produtc after discount less than zero.";
          $i++;
        }
      }
    }
    if(!is_array($error_list))
    {
      foreach($list_product_id as $value)
      {
        $coupon->AddProductDiscount($coupon_id, $value);
      }
      header("Location: coupon.php?pf=detail&id=$coupon_id");
    }
  }
}

function ShowFormAddProductDiscount() {
  global $tpl, $coupon, $coupon_id, $coupon_code, $coupon_value, $period, $usage, $coupon_product, $error_list;

  $tpl->assign('pf',$_REQUEST['pf']);
  
  $tpl->assign('error',$error_list);
  $tpl->assign('coupon_id',$coupon_id);
  $tpl->assign('coupon_code',$coupon_code);
  $tpl->assign('coupon_value',$coupon_value);
  $tpl->assign('usage',$usage);
  $tpl->assign('period',$period);
  $tpl->assign('coupon_product',$coupon_product);
  $tpl->display('admin/coupon.html');
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
      $paging = $paging."<a href='coupon.php?browse&page=".$page_."'>$page_</a>&nbsp;&nbsp;";
    }
    return $paging;
  }
}
/*###########################################################
Section : Main
###########################################################*/
$user->AuthenticationAdmin();

if (empty($pf)) {
  GetAllCoupon();
  ShowAllCoupon();
}
elseif ($pf == 'browse') {
  GetAllCoupon();
  ShowAllCoupon();

}
elseif ($pf == 'add') {
   if ($_REQUEST['process'] == 'add') {
    AddCoupon();
  }
  else
  {
    ShowFormAddCoupon();
 }
}
elseif ($pf == 'detail') {
  GetCouponDetail();
  ShowCouponDetail();
}
elseif ($pf == 'add_product_discount') {
  AddProductDiscount();
  ShowFormAddProductDiscount();
}
elseif ($pf == 'delete') {
  DeleteCoupon($_REQUEST['delete']);
}
elseif ($pf == 'edit')
{
  EditCoupon();
  ShowFormEditCoupon();
}
elseif ($pf == 'search')
{
  GetCouponSearchResult();
  ShowCouponSearchResult();
}


