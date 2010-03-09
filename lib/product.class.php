<?php

class Product {
  
  function Add($name,$description,$price,$duration,$duration_unit,$path,$url) {
    global $db;
    
    $record["name"] = $name;
    $record["description"] = $description; 
    $record["price"] = $price;
    $record["duration"] = $duration; 
    $record["duration_unit"] = $duration_unit;
    $record["path"] = $path;
    $record["url"] = $url;
    
    
    $db->AutoExecute('product',$record,'INSERT');  
  }
  
  function CheckProductName($name) {
    global $db;
    
    $query = "select product_id from product where name = '".mysql_escape_string($name)."'";
    $result = $db->Execute($query);
    $rows = $result->GetRows();
    if ($rows) 
      return true;
    else  
      return false;  
  }  

  function BrowseAllProducts($start,$limit) {
    global $db;
    
    $query = "select * from product order by product_id limit ".intval($start).",".intval($limit);
    $result = $db->Execute($query);
    return $result->GetRows();
  }
  
  function GetProductTotal() {
    global $db;
    
    $query = "select count(*) as total from product";
    $result = $db->Execute($query);
    $total = $result->FetchRow();
    return $total['total'];
  }

  
  function GetAllProducts($admin=false) {
    global $db;
    
    $query = "select * from product";
    if (!$admin)
    	$query .= " where NOT path='F'";
    $query .= " order by product_id";
    $result = $db->Execute($query);
    return $result->GetRows();
  }

  function GetProductSearchResult($search_for,$search_in) {
    global $db;
    
    
    if($search_in == "duration_unit")
    {
      if(strtolower($search_for) == "day")
        $search_for = "d";
      if(strtolower($search_for) == "month")
        $search_for = "m";
      if(strtolower($search_for) == "year")
        $search_for = "y";
    }

    $query = "select * from product where `$search_in` like('%".mysql_escape_string($search_for)."%')";
    $result = $db->Execute($query);
    return $result->GetRows();
  }

  
  function GetProduct($product_id) {
    global $db;
    
    $query = "select * from product where product_id=".intval($product_id);
    $result = $db->Execute($query);
    return $result->FetchRow();
  }
  
  function GetEscapedIdList($id_list) {
  	$id_list_array = explode(",", $id_list);
  	$out = array();
  	foreach ($id_list_array as $id) {
  		$out[] = intval($id);
  	}
  	return implode(",", $out);
  }
  
  function GetDeleteProducts($id_list) {
    global $db;
    $id_list = $this->GetEscapedIdList($id_list);
    $query = "select * from product where product_id in ($id_list)";
        $result = $db->Execute($query);
    return $result->GetRows();
  }
  
  
  function DeleteAllProduct($id_list){
     global $db;
     
    $id_list = $this->GetEscapedIdList($id_list);
     
    $query = "select * from product where product_id in ($id_list)";
    $result = $db->Execute($query);
    $product_data = $result->GetRows();
            
    $query = "delete from product where product_id in ($id_list)";
    $db->Execute($query);
    
    $query = "delete from orders where product_id in ($id_list)";
    $db->Execute($query);
    
    foreach($product_data as $value)
    {
      DeleteHtaccess($value["path"]);
    }
  }
  
  function CountOrderProduct($product_id) {
    global $db;
    
    $query = "select count(*) as total from product p, orders o where o.product_id=p.product_id and o.product_id=".intval($product_id); 
    $result = $db->Execute($query);
    return $result->FetchRow();
  }
  
  function Update($product_id,$name,$description,$price,$duration,$duration_unit,$path,$url) {
    global $db;
    
    $record["name"] = $name;
    $record["description"] = $description;
    $record["price"] = $price; 
    $record["duration"] = $duration;
    $record["duration_unit"] = $duration_unit;
    $record["path"] = $path;
    $record["url"] = $url;
    $db->AutoExecute('product', $record, 'UPDATE', "product_id =".intval($product_id));
  }    
 
  function Delete($product_id) {
    global $db;
    
    $query = "delete from product where product_id=".intval($product_id);
    $db->Execute($query);
    $query = "delete from orders where product_id=".intval($product_id);
    $db->Execute($query);
    
        
  }  
  
  function GetProductGroup($path) {
    global $db;
    
    $query = "select * from product where path='".mysql_escape_string($path)."'";
    $result = $db->Execute($query);
    return $result->GetRows();
  }
  
  function CheckPath($path)
  {
    global $db;
    $query = "select distinct path from product where path='".mysql_escape_string($path)."'";
    $result = $db->Execute($query);
    $path = $result->FetchRow();
    return $path['path'];
  }
  
  function GetProductActive() {
    global $db;

    $query = "select count(distinct name) as total from product p, orders o where p.product_id = o.product_id";
    $result = $db->Execute($query);
    $path = $result->FetchRow();
    return $path['total'];
  }
  
  function GetProductNotFree()
  {
    global $db;
    
    $query  = "select * from product where price <> 0";
    $result = $db->Execute($query);
    $data   = $result->GetRows();
    
    return $data;
  }
}
?>
