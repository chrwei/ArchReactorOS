<?php

class Order {
  
    function AddOrder($user_id,$product_id,$date_order) {
    global $db;
  
    $product_data = $this->GetProduct($product_id);
    $duration = $product_data['duration'];
    $duration_unit = $product_data['duration_unit'];
    
    if ($duration_unit== 'd')
    {
      $date_expire = strtotime(date("Y-m-d", $date_order) ." +".$duration." days -1 days");
    }
    elseif ($duration_unit== 'm')
    {
      $date_expire = strtotime(date("Y-m-d", $date_order) ." +".$duration." months -1 days");
    }
    elseif ($duration_unit== 'y')
    {
      $date_expire = strtotime(date("Y-m-d", $date_order) ." +".$duration." years -1 days");
    }
    $record['user_id']      = $user_id;
    $record['product_id']   = $product_id;
    $record['date_order']   = $date_order;
    $record['date_expire']  = $date_expire;
    $record['date_added']   = time();
    
    $db->AutoExecute('orders',$record,'INSERT');  
    
    $query      = "select order_id from orders where user_id=".intval($user_id)." and product_id=".intval($product_id)." and date_order='".mysql_escape_string($date_order)."'";
    $result     = $db->Execute($query);
    $order_data = $result->FetchRow();
    
    return $order_data['order_id']; 
  }
  
    function GetProduct($product_id) {
    global $db;
    
    $query = "select * from product where product_id=".intval($product_id);
    $result = $db->Execute($query);
    return $result->FetchRow();
  }
  
  function GetAllOrders() {
    global $db;
    
    $query = "select * from orders o, product p, user u where o.product_id=p.product_id and  o.user_id=u.user_id order by p.product_id, date_added, date_order";
    $result    = $db->Execute($query);
    return $result->GetRows();
  }
  
  function GetOrdersTotals() {
    global $db;
    
    $query = "
SELECT
	date_order,
	product.name,
	count(*) as total
FROM
	orders JOIN product ON orders.product_id = product.product_id
WHERE
	date_expire >='".date('Y-m-d', strtotime(date("Y-m")."-01 -1 month"))."'
GROUP BY
	date_order,
	product.name
ORDER BY
	product.product_id,
	date_order
";
    $result    = $db->Execute($query);
    return $result->GetRows();
  }
  
  function GetExpireOrders() {
    global $db;
    //get all orders that may be expring, but exclude any users who have orders in for future months.
	$query = "
select 
	* 
from 
	orders o JOIN product p ON o.product_id=p.product_id
		JOIN user u ON o.user_id=u.user_id
where 
	o.date_expire >='".date('Y-m-d', strtotime(date("Y-m")."-01 -1 month"))."' AND
	NOT u.user_id IN(SELECT user_id FROM orders WHERE date_order > now())
order by 
	date_added DESC,
	date_order";
	
    $result    = $db->Execute($query);
    return $result->GetRows();
  }
  
  
  function GetOrderSearchResult($search_for="",$search_in,$max_date_order = "",$min_date_order = "",$max_date_expire = "",$min_date_expire = "")
  {
    global $db;
    
    if($search_for)
    {
      $qr1="and `$search_in` like ('%".mysql_escape_string($search_for)."%')";
    }

    if($max_date_order)
    {
      $qr2="and (date_order >= '".mysql_escape_string($min_date_order)."' and date_order <= '".mysql_escape_string($max_date_order)."')";
    }

    if($max_date_expire)
    {
      $qr3="and (date_expire >= '".mysql_escape_string($min_date_expire)."' and date_expire <= '".mysql_escape_string($max_date_expire)."')";
    }
    
    $query = "select * from orders o, product p, user u where o.product_id=p.product_id and o.user_id=u.user_id ".$qr1." ".$qr2." ".$qr3." order by date_order";
    $result    = $db->Execute($query);
    return $result->GetRows();  
  }
  
  
  
  function GetOrder($order_id) {
    global $db;
    
    $query  = "select * from orders o, product p, user u where o.product_id=p.product_id and o.user_id=u.user_id and o.order_id=".intval($order_id);
    $result = $db->Execute($query);
    return $result->FetchRow();
  }
  
  

  
  function CheckOrder($user_id, $product_id) {
    global $db;
    
    $query = "select order_id from orders where user_id = ".intval($user_id)." and product_id = ".intval($product_id);
    $result = $db->Execute($query);
    $rows = $result->FetchRow();
    if ($rows) 
      return true;
    else  
      return false;
  }
  
  function ReCheckOrder($product_id) {
    global $db;
    
    $query  = "select * from orders o, product p where o.product_id = p.product_id and o.product_id=".intval($product_id);
    $result = $db->Execute($query);
    $rows   = $result->FetchRow();
    return $rows;
  }

  function Delete($order_id) {
    global $db;
    $query = "delete from orders where order_id=".intval($order_id)."";
    $db->Execute($query);
  }  
  
  function GetGroupOrder($product_id) {
    global $db;
    
    $query = "select * from orders o, product p, user u where o.product_id=p.product_id and u.user_id=o.user_id and p.product_id=".intval($product_id);
    $result = $db->Execute($query);
    $rows = $result->GetRows();
    return $rows; 
  } 
  
 
  function CheckUserOrder($user_id) {
    global $db;
    
    $query = "select * from orders o, product p, user u where o.product_id=p.product_id and u.user_id=o.user_id and  u.user_id=".intval($user_id);
    $result = $db->Execute($query);
    $rows = $result->GetRows();
    return $rows; 
  }  

  function AddPayment($order_id,$amount,$currency_code,$payment_date,$payment_gateway,$payment_log,$invoice_id) {
  global $db;
  
  $record['order_id']       = $order_id;
  $record['date_payment']   = $payment_date;
  $record['amount']         = $amount;
  $record['currency_code']  = $currency_code;
  $record['payment_gateway']= $payment_gateway;
  $record['log']            = $payment_log;
  $record['invoice_id']     = $invoice_id;
  $db->AutoExecute('payment',$record,'INSERT');
  }  
  
  function BrowseAllOrders($start,$limit) {
    global $db;
    
    $query = "select * from orders o, product p, user u where o.date_expire >='".date('Y-m-d', strtotime(date("Y-m")."-01 -1 month"))."' AND o.product_id=p.product_id and  o.user_id=u.user_id order by p.product_id, date_added DESC, date_order limit ".intval($start).",".intval($limit)."";
    $result    = $db->Execute($query);
    return $result->GetRows();
  }

  function GetTotalOrders() 
  {
    global $db;
    
    $query = "select count(*) as total from orders o, product p, user u where o.date_expire >='".date('Y-m-d', strtotime(date("Y-m")."-01 -1 month"))."' AND o.product_id=p.product_id and  o.user_id=u.user_id order by date_added DESC, date_order";
    $result = $db->Execute($query);
    $total = $result->FetchRow();
    return $total['total'];
  }

  function UpdateLastEmailSent($order_id,$date_now)
  {
    global $db;
    
    $record['last_email_sent']= $date_now;
    $db->AutoExecute('orders', $record, 'UPDATE', "order_id = ".intval($order_id));
  }

//*********///
  
  function GetUserOrderData($user_id){
    /*==== mengambil data order user yang terakhir (order yang masih aktive dan order yang expire tapi belum di renew) ====*/
    global $db;
    
    $query  = "select * from orders o, product p, user u where o.product_id = p.product_id and u.user_id = o.user_id and  u.user_id = ".intval($user_id)." order by date_added DESC, date_order desc";
    $result = $db->Execute($query);
    $rows   = $result->GetRows();
    return $rows;
  }

  function GetUserOrderHistoryData($user_id){
    /*==== mengambil semua data order user ====*/
    global $db;
    
    $query  = "select * from orders o, product p, user u where o.product_id = p.product_id and u.user_id = o.user_id and u.user_id = ".intval($user_id)." order by date_added DESC, date_order desc";
    $result = $db->Execute($query);
    $rows   = $result->GetRows();
    return $rows;
  }

  function GetPaymentInvoice($order_id)
  {
    global $db;
    
    $query  = "select invoice_id from payment where order_id = ".intval($order_id);
    $result = $db->Execute($query);
    $rows   = $result->FetchRow();
    return $rows['invoice_id'];
  }

/*Backup
  function GetUserOrderHistoryData($user_id){
    
    global $db;
    
    $query  = "select * from orders o, product p, user u , payment py where o.product_id = p.product_id and u.user_id = o.user_id and py.order_id = o.order_id and u.user_id = ".intval($user_id)." order by date_order desc";
    $result = $db->Execute($query);
    $rows   = $result->GetRows();
    return $rows;
  }
*/

  function CheckActiveOrder($product_id,$user_id,$date_order)
  {
    global $db;
    
    $query  = "select max(date_expire) as max_date_expire from orders where product_id = ".intval($product_id)." and user_id = ".intval($user_id);
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    if($data['max_date_expire'] <= $date_order)
      return true;
    else
      return false;
  }
}
?>
