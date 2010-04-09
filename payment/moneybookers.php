<body onload="document.payment.submit()">
<form name="payment" action="https://www.moneybookers.com/app/payment.pl" method="post">
<input type="hidden" name="pay_to_email" value="<? echo $moneybookers_email; ?>">
<input type="hidden" name="transaction_id" value="<?  echo $invoice_id;  ?>">
<input type="hidden" name="return_url" value="<? echo $return_url; ?>">
<input type="hidden" name="cancel_url" value="<? echo $cancel_url; ?>">
<input type="hidden" name="status_url" value="<? echo $notify_url; ?>">
<input type="hidden" name="language" value="EN">
<input type="hidden" name="merchant_fields" value="custom,itemname">
<input type="hidden" name="custom" value="<? echo urlencode($custom); ?>">
<input type="hidden" name="itemname" value="<? echo $name; ?>">
<input type="hidden" name="amount" value="<? echo $price ?>">
<input type="hidden" name="currency" value="<?echo $currency_code;?>">
<input type="hidden" name="detail1_description" value="<?echo $description;?>">
<input type="hidden" name="detail1_text" value="<? echo $item_name; ?>">
<input type="hidden" name="confirmation_note" value="Thanks">
</form>