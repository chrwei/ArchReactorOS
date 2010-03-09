<?php 
include '../init.php';

/*###########################################################
Section : Main
###########################################################*/
$user->AuthenticationAdmin();
$tooltip = $_REQUEST['tooltip'];

if ($tooltip == 'user') {
  $user_data    = $user->GetUser($_REQUEST['id']);
  $user_id      = $user_data['user_id'];
  $username     = $user_data['username'];
  $firstname    = $user_data['firstname'];
  $lastname     = $user_data['lastname'];
  $email        = $user_data['email'];
  $date         = date("m-d-y",$user_data['date']);  
  
  echo "
    <table >
      <tr>
        <td class='td1_tooltip'>Firstname</td>
        <td class='td2_tooltip'>: $firstname</td>
      </tr>
      <tr>
        <td class='td1_tooltip'>Lastname</td>
        <td class='td2_tooltip'>: $lastname</td>
      </tr>
      <tr>
        <td class='td1_tooltip'>Email</td>
        <td class='td2_tooltip'>: $email</td>
      </tr>
      <tr>
        <td class='td1_tooltip'>Join Date</td>
        <td class='td2_tooltip'>: $date</td>
      </tr>        
    </table>
  ";
}
else if ($tooltip == 'product') {
  GetPaymentCurrency();
  $product_data   = $product->GetProduct($_REQUEST['id']); 
  
  $product_id       = $product_data['product_id'];
  $name             = $product_data['name'];
  $description      = $product_data['description'];
  $price            = $product_data['price'];
  $duration         = $product_data['duration'];
  $duration_unit    = $product_data['duration_unit'];
  
  echo "
    <table >
      <tr>
        <td class='td1_tooltip'>price</td>
        <td class='td2_tooltip'>: $currency_code. $price</td>
      </tr>";
      
      if ($duration_unit == 'd') { 
        $duration_unit = 'day';
      }
      else if ($duration_unit == 'm') { 
        $duration_unit = 'month';
      }
      else if ($duration_unit == 'y') { 
        $duration_unit = 'year';
      }     
      echo " 
      <tr>
        <td class='td1_tooltip'>duration</td>
        <td class='td2_tooltip'>:
         $duration ($duration_unit)
         
         </td>
      </tr>
    </table>
  ";
}

if ($tooltip == 'order') {
  
  
  $orders       = $order->GetOrder($_REQUEST['order_id']);
  
  
  
  $user_id      = $orders['user_id'];
  $firstname    = $orders['firstname'];
  $lastname     = $orders['lastname'];
  $email        = $orders['email'];
  $description  = $orders['description'];
  $date_order   = date("m-d-Y ",$orders['date_order']);
  $date_expire  = date("m-d-Y ",$orders['date_expire']);

  echo "
    <table >
      <tr>
        <td class='td1_tooltip'>Firstname</td>
        <td class='td2_tooltip'>: $firstname</td>
      </tr>
      <tr>
        <td class='td1_tooltip'>Lastname</td>
        <td class='td2_tooltip'>: $lastname</td>
      </tr>
      <tr>
        <td class='td1_tooltip'>Email</td>
        <td class='td2_tooltip'>: $email</td>
      </tr>
      <tr>
        <td class='td1_tooltip'>Description</td>
        <td class='td2_tooltip'>: $description</td>
      </tr>        
      <tr>
        <td class='td1_tooltip'>Date order</td>
        <td class='td2_tooltip'>: $date_order</td>
      </tr>
      <tr>
        <td class='td1_tooltip'>Date Expire</td>
        <td class='td2_tooltip'>: $date_expire</td>
      </tr>      
    </table>
  ";  
}
if ($tooltip == 'coupon') {
  $coupon_detail_data = $coupon->GetCouponDetail($_REQUEST['id']);
  
  $coupon_code    = $coupon_detail_data['coupon_code'];
  $coupon_value   = $coupon_detail_data['coupon_value'];
  $period = date("F-d-Y",$coupon_detail_data['start_date'])." to ".date("F-d-Y",$coupon_detail_data['expire_date']);
  if($coupon_detail_data['expire_usage'] == 0)
      $coupon_detail_data['expire_usage'] = 'unlimited';
  $usage          = $coupon_detail_data['usage_count']." / ".$coupon_detail_data['expire_usage'];

  echo "
    <table >
      <tr>
        <td class='td1_tooltip'>Code</td>
        <td class='td2_tooltip'>: $coupon_code</td>
      </tr>
      <tr>
        <td class='td1_tooltip'>Value</td>
        <td class='td2_tooltip'>: $coupon_value</td>
      </tr>
      <tr>
        <td class='td1_tooltip'>Period</td>
        <td class='td2_tooltip'>: $period</td>
      </tr>       
      <tr>
        <td class='td1_tooltip'>Usage</td>
        <td class='td2_tooltip'>: $usage</td>
      </tr>     
    </table>
  ";  
}
?>
