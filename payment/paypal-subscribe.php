<body onload="document.payment.submit()">
<?
include('paypal-subscribe.inc.php');
if($enable_paypal_sandbox) {
  print "<form name=\"payment\" action=\"https://www.sandbox.paypal.com/cgi-bin/webscr\" method=\"post\">";  
}
else {
  print "<form name=\"payment\" action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">";    
}
?>
<input type="hidden" name="cmd" value="_xclick-subscriptions">
<input type="hidden" name="business" value="<?= $paypal_email; ?>">
<input type="hidden" name="rm" value="2">
<input type="hidden" name="item_name" value="<?= $item_name; ?>">
<input type="hidden" name="item_number" value="1">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="cs" value="0">
<input type="hidden" name="currency_code" value="<?= $currency_code ?>">
<input type="hidden" name="invoice" value="<?= $invoice_id; ?>">
<input type="hidden" name="custom" value="<?= $custom; ?>">
<input type="hidden" name="notify_url" value="<?= $notify_url; ?>">
<input type="hidden" name="return" value="<?= $return_url; ?>">
<input type="hidden" name="cancel_return" value="<?= $cancel_url; ?>">
<input type="hidden" name="a3" value="<?= $total; ?>">
<input type="hidden" name="p3" value="<?= $listing_period; ?>">
<input type="hidden" name="t3" value="<?= $listing_period_code; ?>">
<input type="hidden" name="src" value="1">
<input type="hidden" name="sra" value="1">
</form>