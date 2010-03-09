<?php
include '../init.php';
$pf = $_REQUEST['pf'];

function ShowFormAddProducts() {
  global $tpl, $error_list, $currency_code, $currency_name;
  
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('currency_code',$currency_code);
  $tpl->assign('currency_name',$currency_name);
  $tpl->assign('error',$error_list);
  $tpl->assign('def_path',CFG_PROTECT_PATH);
  $tpl->assign('def_url',CFG_PROTECT_URL);
  $tpl->assign('name',addslashes($_REQUEST['name']));
  $tpl->assign('description',addslashes($_REQUEST['description']));
  $tpl->assign('price', $_REQUEST['price']);
  $tpl->assign('duration',$_REQUEST['duration']);
  $tpl->assign('duration_unit',$_REQUEST['duration_unit']);
  $tpl->assign('path',$_REQUEST['path']);
  $tpl->assign('url',$_REQUEST['url']);
  $tpl->display('admin/product.html');
}

function AddProduct() {
  global $tpl, $product, $error_list;
  
  $name           = stripslashes($_REQUEST['name']);
  $description    = stripslashes($_REQUEST['description']);
  $price          = $_REQUEST['price'];
  $duration       = $_REQUEST['duration'];
  $duration_unit  = $_REQUEST['duration_unit'];
  $path           = stripslashes($_REQUEST['path']);
  $url            = stripslashes($_REQUEST['url']);
  
  $i = 0;
  if($name == "" || $description == "" || $price == "" || $duration  == "" || $path == "" || $url == "")
  {
    if($name == "")
    {
      $error_list[$i] = "Name is required";
      $i++;
    }
    if($description == "")
    {
      $error_list[$i] = "Description is required";
      $i++;
    }
    if($price == "")
    {
      $error_list[$i] = "Price password is required";
      $i++;
    }
    if($duration == "")
    {
      $error_list[$i] = "Duration is required";
      $i++;
    }
    if($path == "")
    {
      $error_list[$i] = "Path is required";
      $i++;
    }
    if($url == "")
    {
      $error_list[$i] = "Url is required";
      $i++;
    }    
  }
  elseif($product->CheckProductName($name))
  {
    $error_list[$i] = "Product name is already exist";
    $i++;    
  }
  elseif(!IsDigit($duration))
  {
    $error_list[$i] = "Duration must be digit format";
    $i++;    
  }
  
  if(!is_array($error_list)) 
  {
    $path = strtolower($path);
    $url  = strtolower($url);
    $product->Add($name,$description,$price,$duration,$duration_unit,$path,$url);
    AddHtaccess($path);
    
    $message  = "Adding new product successful <br />";
    $message .= "<input type='button' value='back' onclick=\"javascript:window.location.href='product.php?pf=browse'\">";
    
    $tpl->assign('message',$message);
    $tpl->display('admin/generic.html');    
  }
  else 
  {
    ShowFormAddProducts();  
  }
}

function ShowFormAllProducts() 
{
  global $tpl, $products, $success, $paging;
  
  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('success',$_REQUEST['success']);
  $tpl->assign('products',$products);
  $tpl->assign('paging',$paging);
  $tpl->display('admin/product.html');
}

function ShowAllProducts() 
{
  global $product, $products, $paging;

  $total_data         = $product->GetProductTotal();
  $total_data_in_page = LIMIT_PRODUCT_PAGE;
  $paging             = paging($total_data,$total_data_in_page);
  $page               = $_REQUEST['page']-1;
  if($page<=0)
  {
    $products_data = $product->BrowseAllProducts(0,$total_data_in_page);
  }
  else
  {
    $products_data = $product->BrowseAllProducts($page*$total_data_in_page,$total_data_in_page);
  }
  
  $i = 0;
  foreach ($products_data as $value) 
  {
    $products[$i]['no']         = $i+1;
    $products[$i]['product_id'] = $value['product_id'];
    $products[$i]['name']       = $value['name'];
    if(strlen($value['description']) > 38)
    {
      $products[$i]['description'] = substr($value['description'],0,38)."...";
    }
    else
    {
      $products[$i]['description'] = $value['description'];
    }
    $products[$i]['price']          = $value['price'];
    $products[$i]['duration']       = $value['duration'];
    $products[$i]['duration_unit']  = $value['duration_unit'];
    $products[$i]['path']           = $value['path'];
    $products[$i]['url']            =  $value['url'];
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


function ShowFormSearchResultProducts() 
{
  global $tpl, $products,$error_list;
  

  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('search_for',$_REQUEST['search_for']);
  $tpl->assign('search_in',$_REQUEST['search_in']);
  $tpl->assign('error',$error_list);
  $tpl->assign('products',$products);
  $tpl->display('admin/product.html');
}

function ShowSearchResultProducts() 
{
  global $product, $products, $error_list;
  

  $search_for = $_REQUEST['search_for'];
  $search_in  = $_REQUEST['search_in'];
  $i = 0;
  if($search_for == ""){
        $error_list[$i] = "Search for is required";
        $i++;
  }
  
  if(!is_array($error_list))
  {
    $products_data = $product->GetProductSearchResult($search_for,$search_in);
    $i = 0;
    foreach ($products_data as $value) {
      $products[$i]['no']         = $i+1;
      $products[$i]['product_id'] = $value['product_id'];
      $products[$i]['name']       = $value['name'];
      if(strlen($value['description']) > 38)
      {
        $products[$i]['description'] = substr($value['description'],0,38)."...";
      }
      else
      {
        $products[$i]['description'] = $value['description'];
      }
      $products[$i]['price']          = $value['price'];
      $products[$i]['duration']       = $value['duration'];
      $products[$i]['duration_unit']  = $value['duration_unit'];
      $products[$i]['path']           = $value['path'];
      $products[$i]['url']            =  $value['url'];
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
}

function ShowFormDetailProduct() 
{
  global $tpl, $product_id, $name, $description, $price, $duration, $duration_unit, $path, $url, $success, $error_list, $currency_code, $currency_name;

  $tpl->assign('pf',$_REQUEST['pf']);
  $tpl->assign('success',$success);
  $tpl->assign('error',$error_list);
  $tpl->assign('product_id',$product_id);
  $tpl->assign('name', $name);
  $tpl->assign('currency_code',$currency_code);
  $tpl->assign('currency_name',$currency_name);
  $tpl->assign('description',$description);
  $tpl->assign('price', $price);
  $tpl->assign('duration',$duration);
  $tpl->assign('duration_unit',$duration_unit);
  $tpl->assign('def_path',CFG_PROTECT_PATH);
  $tpl->assign('def_url',CFG_PROTECT_URL);
  $tpl->assign('path',$path);
  $tpl->assign('url',$url);
  $tpl->display('admin/product.html');
}

function ShowDetailProduct() 
{
  global $tpl, $product, $product_id, $name, $description, $price, $duration, $duration_unit, $path, $url, $success, $error_list;
  
  $product_id   = $_REQUEST['product_id'];
  $process      = $_REQUEST['process'];
  $product_data = $product->GetProduct($product_id);
  
  if ($process == 'edit') 
  {
    $product_id     = $_REQUEST['product_id'];
    $name           = stripslashes($_REQUEST['name']);
    $description    = stripslashes($_REQUEST['description']);
    $price          = $_REQUEST['price'];
    $duration       = $_REQUEST['duration'];
    $duration_unit  = $_REQUEST['duration_unit'];
    $path           = stripslashes($_REQUEST['path']);
    $url            = stripslashes($_REQUEST['url']);

    $i = 0;
    if($name == "" || $description == "" || $price == "" || $duration  == "" || $path == "" || $url == "")
    {
      if($name == "")
      {
        $error_list[$i] = "Name is required";
        $i++;
      }
      if($description == "")
      {
        $error_list[$i] = "Description is required";
        $i++;
      }
      if($price == "")
      {
        $error_list[$i] = "Price password is required";
        $i++;
      }
      if($duration == "")
      {
        $error_list[$i] = "Duration is required";
        $i++;
      }
      if($path == "")
      {
        $error_list[$i] = "Path is required";
        $i++;
      }
      if($url == "")
      {
        $error_list[$i] = "Url is required";
        $i++;
      }    
    }
    elseif (strtolower($name) != strtolower($product_data['name']) ) 
    {
      if($product->CheckProductName($name))
      {
        $error_list[$i] = "Product name is already exist";
        $i++;    
      }
    }

    elseif(!IsDigit($duration))
    {
      $error_list[$i] = "Duration must be digit";
      $i++;    
    }

    if(!is_array($error_list)) 
    {
      $path = strtolower($path);
      $url  = strtolower($url);
      $product->Update($product_id,$name,$description,$price,$duration,$duration_unit,$path,$url);
      UpdateHtaccess( $path,$product_data['path']);
      $success = true;
    }
  }  
  else 
  {
    $product_id     = $product_data['product_id'];
    $name           = $product_data['name'];
    $description    = $product_data['description'];
    $price          = $product_data['price'];
    $duration       = $product_data['duration'];
    $duration_unit  = $product_data['duration_unit'];
    $path           = $product_data['path'];  
    $url            = $product_data['url'];
  }
}

function DeleteProduct($product_id_list) 
{
  global $product, $tpl, $order, $success;
  
  $id_list = "";
  for($i=0;$i<=count($product_id_list)-1;$i++)
  {
    $product_data = $order->ReCheckOrder($product_id_list[$i]);
    if ($product_data["name"] != "") 
    {
      if($id_list=="")
      {
        $id_list   = $product_id_list[$i];
      }
      else
      {
        $id_list   = $id_list.','.$product_id_list[$i];
      }
    }    
    else 
    {
      $path = $product->GetProduct($product_id_list[$i]);
      $product->Delete($product_id_list[$i]);
      DeleteHtaccess("{$path["path"]}");
      $j++;
    }
  }
    
  if($id_list != "") {
    header("Location: confirm_delete.php?id_list={$id_list}");
  }
  else {
    header("Location: product.php?pf=browse&success=true");
  }
}

function paging($total_data,$total_data_in_page)
{
  if($total_data <=$total_data_in_page)
  {
    return "";
  }
  else
  {
    if($total_data % $total_data_in_page == 0)
    {
      $page_total = integer_divide($total_data, $total_data_in_page);
    }
    else
    {
      $page_total = integer_divide($total_data, $total_data_in_page) + 1;
    }
    $paging = "Page : ";
    for($page=0;$page<=$page_total-1;$page++)
    {
      $page_ = $page+1;
      $paging = $paging."<a href='product.php?browse&page=".$page_."'>$page_</a>&nbsp;&nbsp;";
    }
    return $paging;
  }
}
/*###########################################################
Section : Main
###########################################################*/
$user->AuthenticationAdmin();

if (empty($pf)) 
{
  ShowAllProducts();
  ShowFormAllProducts();
}
elseif ($pf == 'browse') 
{
  ShowAllProducts();
  ShowFormAllProducts();
}
elseif ($pf == 'add') 
{
  GetPaymentCurrency();
  if ($process == 'add') 
  {
    AddProduct();
  }
  else
  {
    ShowFormAddProducts();
  }
}
elseif ($pf == 'detail') 
{
  GetPaymentCurrency();
  ShowDetailProduct();
  ShowFormDetailProduct();
}
elseif ($pf == 'delete') 
{
  DeleteProduct($_REQUEST['delete']);
}
elseif ($pf == 'search')
{
  ShowSearchResultProducts() ;
  ShowFormSearchResultProducts();
}
?>
