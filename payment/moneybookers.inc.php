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
	<a href='paynow.php?id=" . $invoice_id . "&m=paypal'><img src='http://www.moneybookers.com/images/banners/88_en_mb.gif' border='0' alt='Make payments with Moneybookers - it is fast, free and secure!' /></a> 
	</p>
";

$enable_alertpay_sandbox = "0";

?>