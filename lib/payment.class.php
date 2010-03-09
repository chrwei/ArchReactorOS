<?php
class Payment
{
  function GetAllCurrency()
  {
    global $db;
    
    $query  = "select * from currency order by currency_code";
    $result = $db->Execute($query);
    $data   = $result->GetRows();
    
    return $data;
  }

  function GetPaymentCurrency()
  {
    global $db;
    
    $query  = "select * from currency where currency_usage = '1'";
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    return $data;
  }

  function ChangeUseCurrency($currency_id)
  {
    global $db;
    
    if($currency_id)
    {
      $record["currency_usage"] = 0;
      $db->AutoExecute('currency', $record, 'UPDATE', "currency_id <> 0");       
      
      $record["currency_usage"] = 1;
      $db->AutoExecute('currency', $record, 'UPDATE', "currency_id =".intval($currency_id)); 
    }
  }
  
  function GetAllPaymentGateway()
  {
    global $db;
    
    $query  = "select * from payment_gateway order by payment_gateway_id";
    $result = $db->Execute($query);
    $data   = $result->GetRows();
    
    return $data;
  }
  
  function GetPaymentGatewayDetail($gateway_name)
  {
    global $db;
    
    $query  = "select * from payment_gateway where payment_gateway_name = '".mysql_escape_string($gateway_name)."'";
    $result = $db->Execute($query);
    $data   = $result->FetchRow();
    
    return $data;
  }
  
  function EditPaymentGateway($payment_gateway_name, $payment_gateway_account,$payment_gateway_status)
  {
    global $db;
    
    $record["payment_gateway_account"] = $payment_gateway_account;
    $record["payment_gateway_status"] = $payment_gateway_status;
    
    $db->AutoExecute('payment_gateway', $record, 'UPDATE', "payment_gateway_name ='".mysql_escape_string($payment_gateway_name)."'"); 
  }


  function AddPaymentGateway()
  {
    global $db;
    
    $record["payment_gateway_name"]   = "moneybookers";
    $record["payment_gateway_account"] = "kosong";
    $record["payment_gateway_status"] = 0;
    
    $db->AutoExecute('payment_gateway', $record, 'INSERT'); 
  }
}
?>