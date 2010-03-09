<?php
include '../init.php';

function ConfirmFormDelete() {
  global $tpl, $product,$products;
  
  
  $tpl->assign('id_list',$_REQUEST['id_list']);
  $tpl->assign('products',$products);
  $tpl->display('admin/confirm_delete.html');
}

function ShowDeleteProducts() {
  global $product,$products;
  $products_data = $product->GetDeleteProducts($_REQUEST['id_list']);
  $i = 0;
  
  foreach ($products_data as $value) {
    $products[$i]['no'] = $i+1;
    $products[$i]['product_id'] = $value['product_id'];
    $products[$i]['name'] = $value['name'];
    $order_total =  $product->CountOrderProduct($value['product_id']);
    $products[$i]['order_total'] =$order_total['total'];
    if($i % 2 != 0)
    {
      $products[$i]['color'] = '#f7f7f7';
    }
    else
    {
      $products[$i]['color'] = '#ffffff';
    }
    $i++;
  }
}


function DeleteAllProducts()
{
  global $product;
  
  $product->DeleteAllProduct($_REQUEST['id_list']);
  header("Location: product.php");
}

/*###########################################################
Section : Main
###########################################################*/
$user->AuthenticationAdmin();

if($_REQUEST['process'] == 'delete')
{
  DeleteAllProducts();
}
ShowDeleteProducts();
ConfirmFormDelete();

?>
