<?

/*===================================================
  Enable/Disable this payment system
===================================================*/
$enable = "1";
/*===================================================
  HTML code that will appear in invoice
  Do not need to change this variable
===================================================*/
$invoice_html = "
	<p> 
	<a href='paynow.php?id=" . $invoice_id . "&m=paypal'><img src='http://www.paypal.com/images/x-click-but02.gif' border='0' alt='Make payments with PayPal - it is fast, free and secure!' /></a> 
	</p>
";

// Do not change here
$enable_paypal_sandbox = "0";
?>