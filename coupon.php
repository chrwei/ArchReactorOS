<?php

include 'init.php';
GetPaymentCurrency();
$user->AuthenticationUser();
if($pf == "check")
{
  $discount_data = $coupon->CheckProductDiscount($coupon_code, $product_id);
  if(!$discount_data)
  {
    echo "<font color='red'><br>Your coupon code is invalid. Please another one.<br><br></font>";
  }
  else
  {
    $percentage = strrpos($discount_data['coupon_value'], "%");
    if($percentage)
    {
      $percent                  = str_replace("%", "", $discount_data['coupon_value']);
      $coupon_value_type        = "percentage";
      $percentage_coupon_value  = $percent;
      $net_price = $discount_data['price'] - ($discount_data['price']*($percent/100));
      echo "<font color='green'><br>Congratulation, You have earned ".$discount_data['coupon_value']." price discount, the product price is now at $currency_code. $net_price<br><br></font>";
    }
    else
    {
      $coupon_value_type = "price";
      $price_coupon_value = $discount_data['coupon_value'];
      $net_price = $discount_data['price']-$discount_data['coupon_value'];
      echo "<font color='green'><br>Congratulation, You get a $currency_code. ".$discount_data['coupon_value']." price discount, the price is now at $currency_code. $net_price<br><br></font>";
    }
  }
}
?>