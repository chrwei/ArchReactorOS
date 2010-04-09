<?
$enable = "1";

/*===================================================
  Your paypal email address
===================================================*/
?>
<body onload="document.payment.submit()">
<form name="payment" method="post" action="https://www.alertpay.com/PayProcess.aspx" >
<input type="hidden" name="ap_purchasetype" value="<?= $ap_purchasetype ?>"/>
<input type="hidden" name="ap_merchant" value="<?= $payalert_email?>"/>  
<input type="hidden" name="ap_itemname" value="<?= $item_name ?>"/>  
<input type="hidden" name="ap_currency" value="<?= $ap_currency ?>"/>  
<input type="hidden" name="ap_returnurl" value="<?= $return_url ?>">  
<input type="hidden" name="apc_1" value="<? echo urlencode($custom); ?>"/>  
<input type="hidden" name="ap_itemcode" value="<?= $product_id ?>"/>  
<input type="hidden" name="ap_quantity" value="1"/>  
<input type="hidden" name="ap_description" value="<?= $description ?>"/>  
<input type="hidden" name="ap_amount" value="<?= $price ?>"/>  
<input type="hidden" name="ap_cancelurl" value="<?= $cancel_url ?>"/>  
</form>