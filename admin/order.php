<?php
include '../init.php';
$pf = $_REQUEST['pf'];

function ShowFormAllOrders() 
{
  global $tpl, $orders, $success, $paging;
  
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('success',$_REQUEST['success']);
  $tpl->assign('orders',$orders);
  $tpl->assign('paging',$paging);
  $tpl->display('admin/order.html');
}

function ShowAllOrders() 
{
  global $tpl, $order, $product, $orders, $success, $paging;
  
  $total_data         = $order->GetTotalOrders();
  $total_data_in_page = LIMIT_ORDER_PAGE;
  $paging             = paging($total_data,$total_data_in_page);
  $page               = $_REQUEST['page']-1;
  if($page<=0)
  {
    $orders_data      = $order->BrowseAllOrders(0,$total_data_in_page);
  }
  else
  {
    $orders_data      = $order->BrowseAllOrders($page*$total_data_in_page,$total_data_in_page);
  }
  
  $i = 0;
  foreach ($orders_data as $value) 
  {
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
      $orders[$i]['expire_in'] = "<font color='#ff0000'>Expire</font>";
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
}
function ShowFormSearchResult() 
{
  global $tpl, $orders, $error_list;
  
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('search_for',$_REQUEST['search_for']);
  $tpl->assign('search_in',$_REQUEST['search_in']);
  $tpl->assign('error',$error_list);
  $tpl->assign('orders',$orders);
  $tpl->assign('to_month_order',$_REQUEST['to_month_order']);
  $tpl->assign('to_day_order',$_REQUEST['to_day_order']);
  $tpl->assign('to_year_order',$_REQUEST['to_year_order']);
  $tpl->assign('month_order',$_REQUEST['month_order']);
  $tpl->assign('day_order',$_REQUEST['day_order']);
  $tpl->assign('year_order',$_REQUEST['year_order']);
  $tpl->assign('to_month_expire',$_REQUEST['to_month_expire']);
  $tpl->assign('to_day_expire',$_REQUEST['to_day_expire']);
  $tpl->assign('to_year_expire',$_REQUEST['to_year_expire']);
  $tpl->assign('month_expire',$_REQUEST['month_expire']);
  $tpl->assign('day_expire',$_REQUEST['day_expire']);
  $tpl->assign('year_expire',$_REQUEST['year_expire']);
  $tpl->display('admin/order.html');
}

function ShowSearchResult() {
  global $tpl, $order, $product, $orders, $error_list;
  
  $search_for         = $_REQUEST['search_for'];
  $search_in          = $_REQUEST['search_in'];
  $filter_date_order  = $_REQUEST['filter_date_order'];
  $filter_date_expire = $_REQUEST['filter_date_expire'];
  
  $i=0;
  if($search_for == "" && !$filter_date_order && !$filter_date_expire){
    $error_list[$i] = "Search for is required";
    $i++;
  }
  
  if($filter_date_order)
  {
    $to_month_order     = $_REQUEST['to_month_order'];
    $to_day_order       = $_REQUEST['to_day_order'];
    $to_year_order      = $_REQUEST['to_year_order'];
    $month_order        = $_REQUEST['month_order'];
    $day_order          = $_REQUEST['day_order'];
    $year_order         = $_REQUEST['year_order'];
    
    $max_date_order=mktime(23,59,59, $to_month_order,$to_day_order,$to_year_order);
    $min_date_order=mktime(23,59,59, $month_order,$day_order,$year_order);
  } 

  if($filter_date_expire)
  {
      $to_month_expire     = $_REQUEST['to_month_expire'];
      $to_day_expire       = $_REQUEST['to_day_expire'];
      $to_year_expire      = $_REQUEST['to_year_expire'];
      $month_expire        = $_REQUEST['month_expire'];
      $day_expire          = $_REQUEST['day_expire'];
      $year_expire         = $_REQUEST['year_expire'];
      $max_date_expire=mktime(23,59,59, $to_month_expire,$to_day_expire,$to_year_expire);
      $min_date_expire=mktime(23,59,59, $month_expire,$day_expire,$year_expire);
  } 
  
  if(!is_array($error_list))
  {
    $orders = $order->GetOrderSearchResult($search_for,$search_in,$max_date_order,$min_date_order,$max_date_expire,$min_date_expire);
    $i = 0;
    foreach ($orders as $value) 
    {
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
        $orders[$i]['expire_in'] = "<font color='#ff0000'>Expire</font>";
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
  }
}

function DeleteOrder($order_id_list) 
{
  global $order, $tpl, $success;
  
  for($i=0;$i<=count($order_id_list);$i++)
  {
    $order->Delete($order_id_list[$i]);
  }
  header("Location: order.php?pf=browse&success=true");
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
      $paging = $paging."<a href='order.php?browse&page=".$page_."'>$page_</a>&nbsp;&nbsp;";
    }
    return $paging;
  }
}
/*###########################################################
Section : Main
###########################################################*/
$user->AuthenticationAdmin();

if (empty($pf)) 
{
  ShowAllOrders();
  ShowFormAllOrders();
}
elseif ($pf == 'browse') 
{
  ShowAllOrders();
  ShowFormAllOrders();
}
elseif ($pf == 'delete') 
{
  DeleteOrder($_REQUEST['delete']);
}
elseif ($pf == 'search') 
{
  ShowSearchResult();
  ShowFormSearchResult();
}
?>
