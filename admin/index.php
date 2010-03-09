<?php
include '../init.php';

global $user;

function ShowFormAllOrders() {
  global $tpl, $orders, $order, $user, $product, $paging;
  
  $total_data         = $order->GetTotalOrders();
  $total_data_in_page = LIMIT_ORDER_PAGE;
  $paging             = paging($total_data,$total_data_in_page);
  $page               = $_REQUEST['page']-1;
  if($page<=0)
  {
    $orders_data = $order->BrowseAllOrders(0,$total_data_in_page);
  }
  else
  {
    $orders_data = $order->BrowseAllOrders($page*$total_data_in_page,$total_data_in_page);
  }
  
  $order_active = 0;
  $order_expire = 0;
  $statistic    = $order->GetAllOrders();
  foreach ($statistic as $value) 
  {
    $expire_in = $value['date_expire']-time();
    if($expire_in <= 0)
    {
      $order_expire++;
    }
    else
    {
      $order_active++;
    }
  }
  
  $i = 0;
  foreach ($orders_data as $value) {
    $orders[$i]['no'] = $i+1;
    $orders[$i]['order_id']    = $value['order_id'];
    $orders[$i]['name']        = $value['name'];
    $orders[$i]['user_id']     = $value['user_id'];
    $orders[$i]['username']    = $value['username'];
    $orders[$i]['date_order']  = date("m-d-Y ",$value['date_order']);
    $orders[$i]['date_expire'] = date("m-d-Y ",$value['date_expire']);
    if($i % 2 != 0)
    {
      $orders[$i]['color'] = '#f7f7f7';
    }
    else
    {
      $orders[$i]['color'] = '#ffffff';
    }
    $expire_in = $value['date_expire']-time();
    if($expire_in <= 0)
    {
      $orders[$i]['expire_in'] = "<font color='#ff0000'>Expired on ".$orders[$i]['date_expire']."</font>";
      
    }
    else if($expire_in <= CFG_NOTIFY_EXPIRE*24*60*60)
    {
      $expire_days = round($expire_in/60/60/24);
      if($expire_days == 0)
        $expire_days = $expire_days+1;
      $orders[$i]['expire_in'] = "<font color='#F87217'>Expire in ".$expire_days. " Days</font>";
    }
    else
    {
      $orders[$i]['expire_in']   = "<font color='green'>Active</font>";
    }
    $orders[$i]['description'] = $value['description']; 
    $i++;
  }
  $total_products   = $product->GetProductTotal();
  $total_users      = $user->GetUserTotal();
  $total_orders     = $order->GetTotalOrders();
  $product_active   = $product->GetProductActive();
  $product_expired  = $total_products - $product_active;
  
  $tpl->assign('product_expired',$product_expired);
  $tpl->assign('product_active',$product_active);
  $tpl->assign('order_expire',$order_expire);
  $tpl->assign('order_active',$order_active);  
  $tpl->assign('total_products',$total_products);
  $tpl->assign('total_users',$total_users);
  $tpl->assign('total_orders',$total_orders);
  $tpl->assign('paging',$paging);
  $tpl->assign('orders',$orders);
  $tpl->display('admin/index.html');
}

function paging($total_data,$total_data_in_page)
{
  if($total_data <= $total_data_in_page)
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
      $paging = $paging."<a href='index.php?page=".$page_."'>$page_</a>&nbsp;&nbsp;";
    }
    return $paging;
  }
}
/*###########################################################
Section : Main
###########################################################*/
$user->AuthenticationAdmin();
ShowFormAllOrders();
?>
