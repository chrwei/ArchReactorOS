<body onload="document.payment.submit()">
<?php
include('paypal.inc.php');

if($enable_paypal_sandbox) 
{
  print "<form name=\"payment\" action=\"https://www.sandbox.paypal.com/cgi-bin/webscr\" method=\"post\">";  
}
else 
{
  print "<form name=\"payment\" action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">";    
}
?>

<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="<?php echo $paypal_email; ?>">
<input type="hidden" name="rm" value="2">
<input type="hidden" name="item_name" value="<?php echo $item_name; ?>">
<input type="hidden" name="item_number" value="1">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="cs" value="0">
<input type="hidden" name="currency_code" value="<?= $currency_code ?>">
<input type="hidden" name="amount" value="<?php echo $price; ?>">
<input type="hidden" name="invoice" value="<?= $invoice_id; ?>">
<input type="hidden" name="custom" value="<?= $custom; ?>">
<input type="hidden" name="notify_url" value="<?= $notify_url; ?>">
<input type="hidden" name="return" value="<?= $return_url; ?>">
<input type="hidden" name="cancel_return" value="<?= $cancel_url; ?>">
</form>  