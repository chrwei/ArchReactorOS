<?php
class User {
	
	public function __construct(){
	}
	
	function Add($username, $password, $firstname, $lastname, $email, $address1, $address2, $city, $state, $zip, $phone)
	{
		global $db;
	
		$record["username"] = $username;
		if ($password != '')
			$record["password"] = md5($password); 
		$record["firstname"] = $firstname;
		$record["lastname"] = $lastname; 
		$record["email"] = $email;
		$record["address1"] = $address1;
		$record["address2"] = $address2;
		$record["city"] = $city;
		$record["state"] = $state;
		$record["zip"] = $zip;
		$record["phone"]= $phone;
		$record["date"] = time();
		$db->AutoExecute('user',$record,'INSERT');
		
		$query = "SELECT user_id FROM user WHERE username = '".mysql_real_escape_string($username)."'";
		$result = $db->Execute($query);
		$user	 = $result->FetchRow();
		
		return $user['user_id'];
	}
	
	function CheckUser($username,$email) {
		
		global $db;
		
		$query = "select user_id from user where username = '".mysql_real_escape_string($username)."' or email = '".mysql_real_escape_string($email)."'";
		$result = $db->Execute($query);
		$rows = $result->GetRows();
		if ($rows) 
			return true;
		else	
			return false;	
	}
	
	function CheckUserLogin($username) {
		global $db;
		
		$query = "select * from user where username = '".mysql_real_escape_string($username)."'";
		$result = $db->Execute($query);
		$rows = $result->GetRows();
		return $rows;
		if ($rows) 
			return true;
		else	
			return false;	
	}

	function CheckEmailExist($email) {
		global $db;
		
		$query = "select * from user where email = '".mysql_real_escape_string($email)."'";
		$result = $db->Execute($query);
		$rows = $result->FetchRow();
		return $rows;	
	}
	
	
	function CheckPasswordLogin($username,$password) {
		global $db;
		
		$query = "select user_id from user where active=1 and password = '".md5($password)."' and username = '".mysql_real_escape_string($username)."'";
		$result = $db->Execute($query);
		$rows = $result->GetRows();
		if ($rows) 
			return true;
		else	
			return false;	
	}	
	
	function Login($username, $password, $expire) {
		global $db;

		// get password

		$query	= "select password, admin from user where active=1 and username = '".mysql_real_escape_string($username)."'";
		$result = $db->Execute($query);
		$user	 = $result->FetchRow();
		
		if (md5($password) == $user['password']) {
			$_SESSION["SESSION_USER_AUTHENTICATED"] = 1;

			$username = strtolower($username);
			$_SESSION["SESSION_USERNAME"] = $username;
			$_SESSION["SESSION_PASSWORD"] = $password;
			$_SESSION['nicemember_session_username'] = $username;

			if ($user['admin']) {	 
				$_SESSION["SESSION_ADMIN_AUTHENTICATED"] = 1;
			}
			else {
				$_SESSION["SESSION_ADMIN_AUTHENTICATED"] = 0;
			}
			
			return $user['admin'];
		}
	}

	function Logout() {
		session_unset();
		session_destroy();
	}
		
	function AuthenticationAdmin() {
		global $db;
		$query	= "select user_id, admin from user where active=1 AND username = '".mysql_real_escape_string($_SESSION['SESSION_USERNAME'])."' and password = '".md5($_SESSION['SESSION_PASSWORD'])."'";
		$result = $db->Execute($query);
		$user	 = $result->FetchRow();
		
		if (!$user['user_id']) {
			header("Location: ".CFG_SITE_URL."/login.php?b={$_SERVER['REQUEST_URI']}");
		}
		else {
			if ($_SESSION['SESSION_ADMIN_AUTHENTICATED'] != 1) {
				header("Location: ".CFG_SITE_URL);
			}
		}
	}
	
	function AuthenticationUser() {
		global $db;
		$query	= "select user_id, admin from user where active=1 AND username = '".mysql_real_escape_string($_SESSION['SESSION_USERNAME'])."' and password = '".md5($_SESSION['SESSION_PASSWORD'])."'";
		$result = $db->Execute($query);
		$user	 = $result->FetchRow();
		
		if (!$user['user_id']) {
			header("Location: ".CFG_SITE_URL."/login.php?b={$_SERVER['REQUEST_URI']}");
		}
	}
	
	function GetUsersSearchResult($search_for,$search_in)
	{
		global $db;
		
		$query = "select * from user where `$search_in` like('%".mysql_real_escape_string($search_for)."%')";
		$result = $db->Execute($query);
		return $result->GetRows();
		
	}
	
	function BrowseAllUsers($start,$limit) {
		global $db;
		
//		$query = "select * from user order by user_id limit $start,$limit";
		$query = "
SELECT
  `user`.`user_id`,
  `user`.`username`,
  `user`.`firstname`,
  `user`.`lastname`,
  GROUP_CONCAT(CONCAT(`product`.`path`, ' ',FROM_UNIXTIME(`orders`.`date_expire`, '%Y-%m')) ORDER BY `orders`.`date_expire` SEPARATOR ', ') as paid
FROM
  `user` LEFT JOIN `orders` ON `user`.`user_id` = `orders`.`user_id` AND `orders`.`date_expire` >= UNIX_TIMESTAMP(DATE_SUB(now(), INTERVAL 2 MONTH))
    LEFT JOIN `product` ON `orders`.`product_id` = `product`.`product_id`
GROUP BY 
  `user`.`user_id`,
  `user`.`username`,
  `user`.`firstname`,
  `user`.`lastname`
ORDER BY
  `user`.`firstname`,
  `user`.`lastname` 
 ";

		$result = $db->Execute($query);
		return $result->GetRows();
	}
	function GetUserTotal() {
		global $db;
		
		$query = "select count(*) as total from user";
		$result = $db->Execute($query);
		$total = $result->FetchRow();
		return $total['total'];
	}
	function GetAllUsers() {
		global $db;
		
		$query = "select * from user order by user_id";
		$result = $db->Execute($query);
		return $result->GetRows();
	}
	
	function GetUser($user_id) {
		global $db;
		
		$query = "select * from user where user_id=".intval($user_id);
		$result = $db->Execute($query);
		return $result->FetchRow();
	}
	
	
	function Update($user_id, $username, $password, $firstname, $lastname, $email, $address1, $address2, $city, $state, $zip, $phone) {
		global $db;
		
		$record["username"] = $username;
		$record["firstname"] = $firstname;
		$record["lastname"] = $lastname; 
		$record["email"] = $email;
		$record["address1"] = $address1;
		$record["address2"] = $address2;
		$record["city"] = $city;
		$record["state"] = $state;
		$record["zip"] = $zip;
		$record["phone"]= $phone;
	
		if($password != "")
			$record["password"]	 = md5($password);
		$db->AutoExecute('user', $record, 'UPDATE', "user_id =".intval($user_id));

	}	

	function RandomPassword($user_id) {
		global $db;
		
		$password = createRandomPassword(12);
		$record["password"]	 = md5($password);
		$db->AutoExecute('user', $record, 'UPDATE', "user_id =".intval($user_id));
		return $password;
	}	
	
	function Delete($user_id) {
		global $db;
		
		$query = "update user set active=0 where user_id=".intval($user_id);
		$db->Execute($query);
	}	
	
	function CheckUserActive($username) {
		global $db;
		
		//$query	= "select * from user where username='".mysql_real_escape_string($username)."' AND active=1";
		$query	= "
SELECT
  `user`.`user_id`,
  `user`.`username`,
  `user`.`firstname`,
  `user`.`lastname`,
  `user`.`email`,
  `product`.`path`
FROM
  `user` LEFT JOIN `orders` ON `user`.`user_id` = `orders`.`user_id` AND `orders`.`date_expire` >= UNIX_TIMESTAMP(DATE_SUB(now(), INTERVAL 1 MONTH))
    LEFT JOIN `product` ON `orders`.`product_id` = `product`.`product_id` AND `product`.`path` = 'F'
WHERE username='".mysql_real_escape_string($username)."' AND active=1
GROUP BY 
  `user`.`user_id`,
  `user`.`username`,
  `user`.`firstname`,
  `user`.`lastname`,
  `user`.`email`
ORDER BY
  `user`.`firstname`,
  `user`.`lastname` 
  ";
		$result = $db->Execute($query);
		$rows	 = $result->FetchRow();
		return $rows;
	}
	
	function GetActiveUserData($username) {
		global $db;
		
		$query	= "select * from user where username='".mysql_real_escape_string($username)."'";
		$result = $db->Execute($query);
		$rows	 = $result->FetchRow();
		return $rows;
	}
}
?>
