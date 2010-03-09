<?php
class Invoice
{
  function GetInvoiceConfiguration()
  {
    global $db;
    
    $query  = "select * from invoice_config";
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    return $data;
  }
  
  function Update($company,$address,$contact,$phone,$email)
  {
    global $db;
    
    $record["company"]  = $company;
    $record["address"]  = $address; 
    $record["contact"]  = $contact;
    $record["phone"]    = $phone; 
    $record["email"]    = $email;
    
    $db->AutoExecute('invoice_config', $record, 'UPDATE', "company <> ''"); 
  }
  
  function GetInvoice($invoice_id)
  {
    global $db;
    
    $query  = "select * from invoice where invoice_id = ".intval($invoice_id);
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    return $data;    
  }
  
  function AddInvoice($invoice_id,$invoice_date,$due_date,$invoiced_to,$service,$description,$price,$discount_price,$total_price,$currency_code,$comment,$paid,$paid_date,$paid_gateway,$email)
  {
    global $db;
    
    $record["invoice_id"]     = $invoice_id;
    $record["invoice_date"]   = $invoice_date; 
    $record["due_date"]       = $due_date;
    $record["invoiced_to"]    = $invoiced_to; 
    $record["service"]        = $service;
    $record["description"]    = $description;
    $record["price"]          = $price;
    $record["discount_price"] = $discount_price;
    $record["total_price"]    = $total_price;
    $record["currency_code"]  = $currency_code;
    $record["comment"]        = $comment;
    $record["paid"]           = $paid;
    $record["paid_date"]      = $paid_date;
    $record["paid_gateway"]   = $paid_gateway;
    $record["email"]          = $email;
    $db->AutoExecute('invoice',$record,'INSERT');
  }
}
?>