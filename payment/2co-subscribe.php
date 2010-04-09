<?
include "2co-subscribe.inc.php";
if (!$co_recurring) { 
?>
<body onload="document.payment.submit()">
<form name="payment" action="https://www.2checkout.com/2co/buyer/purchase" method="post">
<input type="hidden" name="sid" value="<?= $co_sid ?>">
<input type="hidden" name="cart_order_id" value="<?= substr(strtoupper(md5(uniqid(rand(), true))), 0, 10) ?>">
<input type="hidden" name="total" value="<?= $price ?>">  

<input type="hidden" name="secret" value="<?= $co_secret ?>">
<input type="hidden" name="pay_method" value="CC">
<input type="hidden" name="fixed" value="Y">
<input type="hidden" name="custom" value="<?= urlencode($custom) ?>">

<input type="hidden" name="invoice_id" value="<?= $invoice_id ?>">
<input type="hidden" name="notify_url" value="<?= urlencode($notify_url) ?>">
<input type="hidden" name="return" value="<?= urlencode($return_url) ?>">
<input type="hidden" name="cancel_return" value="<?= urlencode($cancel_url) ?>">

<input type="hidden" name="id_type" value="1">

<input type="hidden" name="c_prod_1" value="XYZ">
<input type="hidden" name="c_name_1" value="<?= $item_name ?>">
<input type="hidden" name="c_description_1" value="-">
<input type="hidden" name="c_price_1" value="<?= $price ?>">
<input type="hidden" name="c_tangible_1" value="N">
</form> 

<? } else { ?>
<body onload="document.payment.submit()">
<form name="payment" action='https://www.2checkout.com/2co/buyer/purchase' method='post'>
<input type='hidden' name='sid' value='<?= $co_sid ?>' >
<input type='hidden' name='quantity' value='1' >
<input type="hidden" name="secret" value="<?= $co_secret ?>">
<input type="hidden" name="notify_url" value="<?= urlencode($notify_url) ?>">
<input type="hidden" name="return" value="<?= urlencode($return_url) ?>">
<input type='hidden' name='product_id' value='<?= $co_prod_id ?>' >
<input type="hidden" name="custom" value="<?= urlencode($custom) ?>">
<input type="hidden" name="total" value="<?= $price ?>">  
</form>
<? } ?>	