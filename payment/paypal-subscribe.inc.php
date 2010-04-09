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
	<a href='paynow.php?id=" . $invoice_id . "&m=paypal'><img src='https://www.paypal.com/en_US/i/btn/x-click-but20.gif' border='0' alt='Make payments with PayPal - it is fast, free and secure!' /></a> 
	</p>
";

// do not change here
$enable_paypal_sandbox = "0";
?>