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
	<a href='paynow.php?id=" . $invoice_id . "&m=2co'><img src='http://www.2checkout.com/images/overview/btns/25.jpg' alt='Pay Now with 2checkout...' border='0' /></a> 
	</p>
";

?>