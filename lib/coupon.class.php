<?php
class Coupon{
  
  function CheckCouponCode($coupon_code)
  {
    /*==== mengengecek apakah coupon code sudah ada atau tidak ====*/
    global $db;
    
    $query  = "select coupon_id from discount_coupon where coupon_code = '".mysql_escape_string($coupon_code)."'";
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    if($data['coupon_id'] =="")
      return false;
    else
      return true;
  }
  
  function Add($coupon_code,$coupon_value,$start_date,$expire_date,$expire_usage)
  {
    /*==== Menambah data coupon ====*/
    global $db;
    
    $record["coupon_code"] = $coupon_code;
    $record["coupon_value"] = $coupon_value; 
    $record["start_date"] = $start_date;
    $record["expire_date"] = $expire_date; 
    $record["expire_usage"] = $expire_usage;
    $record["usage_count"] = 0;
    
    $db->AutoExecute('discount_coupon',$record,'INSERT');  
    
    $query  = "select coupon_id from discount_coupon where coupon_code = '".mysql_escape_string($coupon_code)."'";
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    return $data['coupon_id'];
  }
  
  function Delete($coupon_id)
  {
    global $db;
    
    $query = "delete from discount_coupon where coupon_id = ".intval($coupon_id);
    $db->Execute($query);
    
    $query = "delete from discount where coupon_id = ".intval($coupon_id);
    $db->Execute($query);
    
    
  }
  
  function GetAllCoupon($start,$limit)
  {
    global $db;
    
    $query  = "select * from discount_coupon order by coupon_id desc limit ".intval($start).", ".intval($limit);
    $result = $db->Execute($query);
    $data   = $result->GetRows();
    
    return $data;
  }

  function GetCouponDetail($coupon_id)
  {
    global $db;
    
    $query  = "select * from discount_coupon where coupon_id = ".intval($coupon_id);
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    return $data;
  }

  function GetCouponTotal()
  {
    global $db;
    
    $query  = "select count(*) as total_coupon from discount_coupon";
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    return $data['total_coupon'];
  }

  function GetCouponProduct($coupon_id)
  {
    global $db;
    
    $query  = "select * from discount_coupon dc, discount d, product p where dc.coupon_id = d.coupon_id and d.product_id = p.product_id and dc.coupon_id = ".intval($coupon_id)." order by d.product_id asc";
    $result = $db->Execute($query);
    $data   = $result->GetRows();
    
    return $data;
  } 

  function GetDiscountProduct($coupon_id)
  {
    global $db;
    
    $query  = "select * from discount_coupon dc, discount d, product p where dc.coupon_id = d.coupon_id and d.product_id = p.product_id and dc.coupon_id = ".intval($coupon_id)." order by d.product_id asc";
    $result = $db->Execute($query);
    $data   = $result->GetRows();
    
    $i=0;
    foreach($data as $value)
    {
      if($i == 0)
        $list_product_id = "'".$value['product_id']."'";
      else
        $list_product_id .= ",'".$value['product_id']."'";
      $i++;
    }
    
    if(!$data)
    {
      $list_product_id="''";
    }
    $query  = "select * from product where product_id not in($list_product_id) and price <> 0 order by product_id asc";
    $result = $db->Execute($query);
    $data   = $result->GetRows();
    
    return $data;
  }
  
  function AddProductDiscount($coupon_id, $product_id)
  {
    /*==== Menambah product diskon ====*/
    global $db;
    
    $query  = "select * from discount where coupon_id = ".intval($coupon_id)." and product_id = ".intval($product_id);
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    if(!$data)
    {
      $record["coupon_id"] = $coupon_id;
      $record["product_id"] = $product_id;
      
      $db->AutoExecute('discount',$record,'INSERT');  
    }

  }

  function DeleteProductDiscount($coupon_id, $product_id)
  {
    /*==== Menghapus product diskon ====*/
    global $db;
    
    $query = "delete from discount where coupon_id = ".intval($coupon_id)." and product_id = ".intval($product_id);
    $db->Execute($query);

  }
  
  function Edit($coupon_id,$coupon_code,$coupon_value,$start_date,$expire_date,$expire_usage)
  {
    /*==== edit data coupon ====*/
    global $db;
    
    $record["coupon_code"] = $coupon_code;
    $record["coupon_value"] = $coupon_value; 
    $record["start_date"] = $start_date;
    $record["expire_date"] = $expire_date; 
    $record["expire_usage"] = $expire_usage;
    
    $db->AutoExecute('discount_coupon', $record, 'UPDATE', "coupon_id = ".intval($coupon_id)); 
  }

  function CheckNetPrice($discount_price,$product_id)
  {
    /*==== Mengecek jangan apakah hasil diskon valid atau tidak tidal valid bila nilainya minus ====*/
    global $db;
    $discount_price = floatval($discount_price);
    $query  = "select name, (price - $discount_price) as net_price  from product where product_id = ".intval($product_id);
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    return $data;
  }
  
  function CouponValueIsValid($coupon_id,$coupon_value)
  {
    /*==== Menghapus product diskon ====*/
    global $db;
    
    $query = "select * from discount where coupon_id = ".intval($coupon_id);
    $result = $db->Execute($query);
    $data   = $result->GetRows();
    
    $valid = true;
    foreach($data as $value)
    {
      $product_id = $value['product_id'];
      $query      = "select price  from product where product_id = ".intval($product_id);
      $result     = $db->Execute($query);
      $price       = $result->FetchRow();
      
      $percentage = strrpos($coupon_value, "%");
      if($percentage)
      {
        $percent    = str_replace("%", "", $coupon_value);
        $net_price  = $price['price'] - ($price['price'] * ($percent/100));
      }
      else
      {
        $net_price = $price['price'] - $coupon_value;
      }
      
      if($net_price<0)
      {
        $valid = false;
      }
    }
    return $valid;
  }
  
  function GetProductSearchResult($search_for,$search_in)
  {
    global $db;
    $search_for = mysql_escape_string($search_for);
    $query = "select * from discount_coupon where `$search_in` like('%{$search_for}%') order by coupon_id desc";
    $result = $db->Execute($query);
    return $result->GetRows();
  }
  
  function CheckProductDiscount($coupon_code, $product_id)
  {
    /*==== Menambah product diskon ====*/
    global $db;
    
    $query  = "select * from discount_coupon dc, discount d, product p where dc.coupon_id = d.coupon_id and d.product_id = p.product_id and dc.coupon_code = ".intval($coupon_code)." and d.product_id = ".intval($product_id);
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    $date_now = time();
    
    if(!$data)
    {
      return false;
    }
    else
    {
      if($data['usage_count'] >= $data['expire_usage'])
      {
        return false; 
      }
      elseif($date_now >= $data['expire_date'] || $date_now <= $data['start_date'])
      {
        return false;
      }
      else
      {
        return $data;
      }
    }
  }
  
  function AddUsageCount($coupon_code)
  {
    global $db;
    
    $query  = "select * from discount_coupon where coupon_code = '".mysql_escape_string($coupon_code)."'";
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    $record["usage_count"] = $data['usage_count'] + 1;
    
    $db->AutoExecute('discount_coupon', $record, 'UPDATE', "coupon_code ='".mysql_escape_string($coupon_code)."'");  
  }
}
?>
