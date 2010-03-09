<?php
include '../init.php';
$pf = $_REQUEST['pf'];

function ShowFormInvoiceConfiguration() 
{
  global $tpl, $error_list, $company, $contact, $address, $phone, $email, $success;
  
  $tpl->assign('pf', $_REQUEST['pf']);
  $tpl->assign('company', $company);
  $tpl->assign('contact', $contact);
  $tpl->assign('address', $address);
  $tpl->assign('phone', $phone);
  $tpl->assign('email', $email);
  $tpl->assign('success', $success);
  $tpl->assign('error', $error_list);
  $tpl->display('admin/invoice.html');
}

function GetInvoiceConfiguration() {
  global $inv_class, $error_list, $company, $contact, $address, $phone, $email, $success;
  
  $invoice_config_data  = $inv_class->GetInvoiceConfiguration();
  $process             = $_REQUEST['process'];
  $i = 0;
  if($process == "edit")
  {
    $company  = stripslashes($_REQUEST['company']);
    $contact  = stripslashes($_REQUEST['contact']);
    $address  = stripslashes($_REQUEST['address']);
    $phone    = $_REQUEST['phone'];
    $email    = $_REQUEST['email'];
    
    if($company == "" || $contact == ""  || $address == ""  || $phone == ""  || $email == "")
    {
      if($company == "")
      {
        $error_list[$i] = "Company is requered";
        $i++;
      }
      if($contact == "")
      {
        $error_list[$i] = "Contact is requered";
        $i++;
      }
      if($address == "")
      {
        $error_list[$i] = "Address is requered";
        $i++;
      }
      if($phone == "")
      {
        $error_list[$i] = "Phone is requered";
        $i++;
      }
      if($email == "")
      {
        $error_list[$i] = "Email is requered";
        $i++;
      }
    }
    elseif(!IsEmailAddress($email))
    {
      $error_list[$i] = "Invalid email format";
        $i++;
    }
    if(!is_array($error_list))
    {
      $inv_class->Update($company,$address,$contact,$phone,$email);
      $success = true;
    }
  }
  else
  {
    $company  = $invoice_config_data['company'];
    $contact  = $invoice_config_data['contact'];
    $address  = $invoice_config_data['address'];
    $phone    = $invoice_config_data['phone'];
    $email    = $invoice_config_data['email'];
  }
}

/*###########################################################
Section : Main
###########################################################*/
$user->AuthenticationAdmin();

if (empty($pf)) {
  GetInvoiceConfiguration();
  ShowFormInvoiceConfiguration();
}
elseif ($pf == 'config') {
  GetInvoiceConfiguration();
  ShowFormInvoiceConfiguration();
}
?>
