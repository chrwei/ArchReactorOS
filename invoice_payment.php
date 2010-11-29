<?php
include 'init.php';
$user->AuthenticationUser();

function GetInvoice()
{
  global $inv_class, $invoice_id, $invoice_to, $company, $contact, $address, $phone, $email, $invoice_date, $due_date, $terms, $service, $description, $comment, $currency, $qty, $unit_price, $line_price, $paid, $paid_date, $discount, $subtotal, $total;
  
  $invoice_config_data  = $inv_class->GetInvoiceConfiguration();
  $company              = $invoice_config_data['company'];
  $contact              = $invoice_config_data['contact'];
  $address              = $invoice_config_data['address'];
  $phone                = $invoice_config_data['phone'];
  $email                = $invoice_config_data['email'];
  
  $invoice_id     = $_REQUEST['invoice_id'];
  $invoice_data   = $inv_class->GetInvoice($invoice_id);
  $invoice_date   = date("n/d/Y",$invoice_data['invoice_date']);
  $due_date       = date("n/d/Y",$invoice_data['due_date']); 
  $invoice_to     = $invoice_data['invoiced_to']; 
  $terms          = $invoice_data['paid_gateway']; 
  $service        = $invoice_data['service']; 
  $description    = $invoice_data['description']; 
  $currency       = $invoice_data['currency_code'];  
  $qty            = 1; 
  $comment        = $invoice_data['comment']; 
  $paid_date      = date("n/d/Y",$invoice_data['paid_date']); 
  $paid           = $invoice_data['paid']; 
  $unit_price     = $invoice_data['price'];
  $line_price     = $unit_price;
  $subtotal       = $line_price;
  $discount       = $invoice_data['discount_price'];
  $total          = $invoice_data['total_price'];

}

function ShowInvoice()
{
  global $tpl, $invoice_id, $invoice_to, $company, $contact, $address, $phone, $email, $invoice_date, $due_date, $terms, $service, $description, $comment, $currency, $qty, $unit_price, $line_price, $paid, $paid_date, $discount, $subtotal, $total;
  
  $tpl->assign('invoice_id',$invoice_id);
  $tpl->assign('invoice_to',$invoice_to);
  $tpl->assign('company',$company);
  $tpl->assign('contact',$contact);
  $tpl->assign('address',$address);
  $tpl->assign('phone',$phone);
  $tpl->assign('email',$email);
  $tpl->assign('invoice_date',$invoice_date);
  $tpl->assign('due_date',$due_date);
  $tpl->assign('terms',$terms);
  $tpl->assign('service',$service);
  $tpl->assign('description',$description);
  $tpl->assign('comment',$comment);
  $tpl->assign('currency',$currency);
  $tpl->assign('qty',$qty);
  $tpl->assign('unit_price',$unit_price);
  $tpl->assign('line_price',$line_price);
  $tpl->assign('discount',$discount);
  $tpl->assign('paid',$paid);
  $tpl->assign('paid_date',$paid_date);
  $tpl->assign('total',$total);
  $tpl->assign('subtotal',$subtotal);
  $tpl->display('invoice_payment.html');
}

/*===================================================
  main
===================================================*/
GetInvoice($_REQUEST['invoice_id']);
ShowInvoice();

