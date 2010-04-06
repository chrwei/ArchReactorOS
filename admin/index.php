<?php
include '../init.php';

global $user;

function ShowFormAllOrders() {
	global $tpl, $orders, $order, $user, $product, $paging;
	
	$total_data = $order->GetTotalOrders();
	$total_data_in_page = LIMIT_ORDER_PAGE;

	$order_totals_data = $order->GetOrdersTotals();
	
	$i = -1;
	$last_prod = '';
	foreach($order_totals_data as $value)
	{
		if ($last_prod != $value['name'])
		{
			$i++;
			$last_prod = $value['name'];
			$order_totals[$i]['name'] = $value['name'];
			if($i % 2 != 0)
				$order_totals[$i]['color'] = '#f7f7f7';
			else
				$order_totals[$i]['color'] = '#ffffff';
		}
			
		
		$order_totals[$i]['date_arr'][] = array('date' => date('Y-m', $value['date_order']), 'total' => $value['total']);
	}

	$paging = paging($total_data,$total_data_in_page);
	$page = $_REQUEST['page']-1;
	if($page<=0)
	{
		$orders_data = $order->BrowseAllOrders(0,$total_data_in_page);
	}
	else
	{
		$orders_data = $order->BrowseAllOrders($page*$total_data_in_page,$total_data_in_page);
	}

	$i = 0;
	foreach ($orders_data as $value) {
		$orders[$i]['no'] = $i+1;
		$orders[$i]['order_id'] = $value['order_id'];
		$orders[$i]['name'] = $value['name'];
		$orders[$i]['user_id'] = $value['user_id'];
		$orders[$i]['username'] = $value['username'];
		$orders[$i]['date_order'] = date("m-d-Y ",$value['date_order']);
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
			$orders[$i]['expire_in'] = "<font color='green'>Active</font>";
		}
		$orders[$i]['description'] = $value['description']; 
		$i++;
	}
	$total_products = $product->GetProductTotal();
	$total_users = $user->GetUserTotal();
	$total_orders = $order->GetTotalOrders();
	$product_active = $product->GetProductActive();
	$product_expired = $total_products - $product_active;
	
	$tpl->assign('product_expired',$product_expired);
	$tpl->assign('product_active',$product_active);
	$tpl->assign('last_month',date("Y-m", strtotime("-1 month")));
	$tpl->assign('this_month',date("Y-m")); 
	$tpl->assign('order_totals',$order_totals);
	$tpl->assign('total_users',$total_users);
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
