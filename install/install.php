<?
/**
 *
 * Copyright(C), Nicecoder, 2000-2005, All Rights Reserved.
 *
 */

session_start();

define('INSTALLATION', 1);

include "_init.php";


/*************************************
 step 1
*************************************/

if (empty($_POST['s'])) {
	$next_step = 2;
	$process_title = "$script_name Installation";
	$message = $message_step1;
}


/*************************************
 step 2
*************************************/
elseif ($_POST['s']==2) {
	$next_step = 3;
	$process_title = "Checking Server";
	$message = CheckServerReq();
}


/*************************************
 step 3
*************************************/
elseif ($_POST['s']==3) {
	if (InitExixst()) {
		$next_step = 5;
	}
	else {
		$next_step = 4;
	}
	$process_title = "Checking File Permission";
	$message = CheckFilePermission();
}


/*************************************
 step 4
*************************************/
elseif ($_POST['s']==4) {
	$next_step = 5;
	$process_title = "Application Setting";
	$message = ShowForm();
}


/*************************************
 step 5
*************************************/
elseif ($_POST['s']==5) {
	if (!InitExixst()) {
		$error_message = CheckForm();
	}
	else {
		unset($_POST['sql_option']['Fresh Installation']);
	}
	if (empty($error_message)) {
		$next_step = 6;
		WriteConfigurationFile();
		$process_title = "Prepare Database";
		$message = ShowFormSQL();
	}
	else {
		$next_step = 5;
		$process_title = "Application Setting";
		$message = ShowForm();
	}
}


/*************************************
 step 6
*************************************/
elseif ($_POST['s']==6) {
	if (!empty($_POST['sql_select'])) {
		$next_step = 7;
		$process_title = "Prepare Database";
		$message = ExecuteSQL($_POST['sql_select']);
		
	}
	else {
		$next_bt = "";
		$process_title = "Installation Complete";
		$message = Finish();
	}
}


/*************************************
 step 7
*************************************/
elseif ($_POST['s']==7) {
	$next_bt = "";
	$process_title = "Installation Complete";
	$message = Finish();
}

include "_install.html.php";

/*************************************
 function
*************************************/

function CheckServerReq() {
	global $req_php_module, $req_php_version, $module_ok, $module_failed;

	$phpversion = phpversion();
	$out = "<p>PHP $req_php_version ... ";
	if(version_compare($req_php_version, phpversion(), "<=")) {
		$out .= "$module_ok (current version: $phpversion)";
	}
	else {
		$out .= "$module_failed (current version: $phpversion)";
	}

	foreach($req_php_module as $key => $val) {
		$out .= "<p>PHP module $key is ";
		if(function_exists($val)) {
			$out .= $module_ok;
		}
		else {
			$out .= $module_failed;
		}
	}

	return $out;
}


function CheckFilePermission() {
	global $req_chmod_777, $writeable_ok, $writeable_failed;

	foreach($req_chmod_777 as $val) {

		$out .= "<p><span>../".$val['name']." .... ";
		
		if ($val['type'] == 'file')
		{
			if (!file_exists($val['name']))
			{
				file_put_contents("../".$val['name'], "");
			}
			chmod("../".$val['name'], 0777);
			if(is_writeable("../".$val['name'])) {
				$out .= $writeable_ok;
			}
			else {
				$out .= $writeable_failed." correct with: touch ".dirname(dirname(__FILE__))
							."/".$val['name']." && chmod 777 ".dirname(dirname(__FILE__))
							."/".$val['name'];
			}
		}
		if ($val['type'] == 'dir')
		{
			if (!file_exists($val['name']))
			{
				mkdir("../".$val['name']);
			}
			chmod("../".$val['name'], 0777);
			if(is_writeable("../".$val['name'])) {
				$out .= $writeable_ok;
			}
			else {
				$out .= $writeable_failed." correct with: mkdir ".dirname(dirname(__FILE__))
							."/".$val['name']." && chmod 777 ".dirname(dirname(__FILE__))
							."/".$val['name'];
			}
		}
		
		$out .= '</span>';
	}
	return $out;
	
}

function ShowForm() {
	global $message_step5, $error_message;

	if(empty($_POST['pf'])) {
		if(!isset($_POST['base_path']))
		{
			$path = dirname(__FILE__);
			$path = str_replace("\\", "/", $path);
			$base_path = str_replace('/install', '/', $path);
		}
		else
			$base_path = $_POST['base_path'];

		$host = $_SERVER["HTTP_HOST"];
		if (empty($host)) {
			$host = getenv("HTTP_HOST");
		}
		if (!isset($_SERVER["REQUEST_URI"]) || !$_SERVER["REQUEST_URI"]) {
			if (!($_SERVER["REQUEST_URI"] = @$_SERVER["PHP_SELF"])) {
				$_SERVER["REQUEST_URI"] = $_SERVER["SCRIPT_NAME"];
			}
			if (isset($_SERVER["QUERY_STRING"])) {
				$_SERVER["REQUEST_URI"] .= "?" . $_SERVER[ "QUERY_STRING" ];
			}
		}
		if (!isset($_POST['site_url']))
		{
			$ref = str_replace('/install/install.php', '', "http://" . $host. $_SERVER["REQUEST_URI"]);
			$site_url = $ref;
		}
		else
			$site_url = $_POST['site_url'];

		if (!isset($_POST['dbHostname']))
			$dbHostname = 'localhost';
		else
			$dbHostname = $_POST['site_url'];
	}

	$out = str_replace('<%$base_path%>', $base_path, $message_step5);
	$out = str_replace('<%$site_url%>', $site_url, $out);
	$out = str_replace('<%$dbUsername%>', $_POST['dbUsername'], $out);
	$out = str_replace('<%$dbPassword%>', $_POST['dbPassword'], $out);
	$out = str_replace('<%$dbHostname%>', $dbHostname, $out);
	$out = str_replace('<%$dbName%>', $_POST['dbName'], $out);
	$out = str_replace('<%$site_name%>', stripslashes($_POST['site_name']), $out);
	$out = str_replace('<%$email%>', $_POST['email'], $out);
	$out = str_replace('<%$error_message%>', $error_message, $out);

	return $out;
}


function CheckForm() {
	// check database
	$found_db = true;
	@mysql_connect($_POST['dbHostname'],$_POST['dbUsername'],$_POST['dbPassword'])
		OR $err = "<li>Unable to connect to database, correct user/password or create a mysql user with: 
					CREATE USER '$_POST[dbUsername]'@'localhost' IDENTIFIED BY  '{yourpassword}'; 
					GRANT USAGE ON *.* TO  '$_POST[dbUsername]'@'localhost';</li>";
	@mysql_select_db($_POST['dbName'])
		or $err .= "<li>Unable to select database, correct db name or create database with :
					CREATE DATABASE IF NOT EXISTS  `$_POST[dbName]` ;  GRANT ALL PRIVILEGES ON  `$_POST[dbName]`.* TO  '$_POST[dbUsername]'@'localhost';</li>";
	if(!empty($err)) $found_db = false;

	if(!IsValidEmailAddress($_POST['email'])) {
		$err .= "<li>Invalid Email Pattern</li>";
	}
	
	if(!empty($err)) {
		$err = "<ul>" . $err . "</ul>";
	}
	
	return $err;
}


function WriteConfigurationFile() {

	$_SESSION['site_path'] = $_POST['base_path'];
	$_SESSION['site_url'] = $_POST['site_url'];
	$_SESSION['site_name'] = $_POST['site_name'];
	$_SESSION['site_email'] = $_POST['email'];
	
	if (!InitExixst()) {
		$content = file_get_contents('init.tpl.php');
		$content = str_replace('<%$base_path%>', $_POST['base_path'], $content);
		$content = str_replace('<%$site_url%>', $_POST['site_url'], $content);
		$content = str_replace('<%$dbUsername%>', $_POST['dbUsername'], $content);
		$content = str_replace('<%$dbPassword%>', $_POST['dbPassword'], $content);
		$content = str_replace('<%$dbHostname%>', $_POST['dbHostname'], $content);
		$content = str_replace('<%$dbName%>', $_POST['dbName'], $content);
		$content = str_replace('<%$site_name%>', $_POST['site_name'], $content);
		$content = str_replace('<%$email%>', $_POST['email'], $content);
		$content = str_replace('<%$dbName%>', $_POST['dbName'], $content);
	}
	else {
		
		$contents = file('../init.php');
		$app = array();
		while (list(, $line) = each($contents)) {
			preg_match("|(.*?)=(.*?);|ms", $line, $match);
			$match[1] = trim($match[1]);
			$match[2] = trim($match[2]);
			if ($match[1]) {
				$app[$match[1]] = $match[2];
			}
		}
		unset($app['static $forbidden']);
		unset($app['$GLOBALS[$k]']);
		unset($app['$value']);
		unset($app['$_GET']);
		unset($app['$_POST']);
		unset($app['$_COOKIE']);
		unset($app['$indexu_version']);

		$contents = file('../init.php');
		while (list($k, $line) = each($contents)) {
			preg_match("|(.*?)=(.*?);|ms", $line, $match);
			$match[1] = trim($match[1]);
			$match[2] = trim($match[2]);
			if ($match[1] && isset($app[$match[1]])) {
				$contents[$k] = "	" . $match[1] . " = " . $app[$match[1]] . ";";
			}
			else {
				$contents[$k] = rtrim($contents[$k]);
			}
		}
		$content = implode("\r\n", $contents);
	}

	$filename = '../init.php';
	$fp = fopen($filename, 'w');
	fwrite($fp, $content);
	fclose($fp);
}


function ShowFormSQL() {
	global $sql_option;

	$i = 0;
	$out = "<p>Select your installation type:<br>";
	foreach($sql_option as $key => $val) {
		if ($i==0) {
			$checked = "checked";
		}
		else {
			$checked = "";
		}
		$out .= "<br><input type=radio name=sql_select value=\"$val\" $checked> $key";
		$i++;
	}
	return $out;
}

function ExecuteSQL($file) {
	global $message_step6, $insert_to_database;
	
	$site_name			= $_SESSION['site_name'];
	$site_path			= $_SESSION['site_path'];
	$site_url			 = $_SESSION['site_url'];
	$site_mail			= $_SESSION['site_email'];
	$notify_email	 = $_SESSION['site_email'];
	$notify_from		= $_SESSION['site_name']." Team";
	$notify_expire	= 3;


	define('INSTALL_TIME',1);
	include "../init.php";

	$dbConn = $db;

	$lines = file($file);
	foreach ($lines as $line_num => $line) {
		$buffer = trim($line);

		if(!empty($buffer) && substr($buffer,0,1) != "#") {
			if(substr($buffer,-1,1)==";") {
				$query .= $line;
				$result = $dbConn->Execute($query);
				$query = "";
			}
			else {
				$query .= $line;
			}
		}
	}
	$query= "
	INSERT INTO `config` 
	(`config_id`, `name`, `value`) 
	VALUES 
	(1, 'site_name', '$site_name'),
	(2, 'site_path', '$site_path'),
	(3, 'site_url', '$site_url'),
	(4, 'site_mail', '$site_mail'),
	(9, 'notify_email', '$notify_email'),
	(10, 'notify_from', '$notify_from'),
	(11, 'notify_expire', '$notify_expire');
	";
	$dbConn->Execute($query);

	$query= "
	INSERT INTO `user` 
	(`user_id`, `username`, `password`, 
	`firstname`, `lastname`, `email`, 
	`address1`, `address2`, `city`, `state`, `zip`, 
	`phone`, `date`, `admin`, `active`) 
	VALUES 
	(1, 'admin', md5('adminpw'), 'Administrator', 
	'Administrator', '$site_mail', 
	'', '', '', '', '', '', ".time().", 1, 1);
	";
	$dbConn->Execute($query);
	
	// do finalization here
	$file = str_replace('.sql', '.php', $file);
	if (file_exists($file)) {
		include $file;
	}

	return $message_step6;
}


function Finish() {
	global $message_step7;

	$out = $message_step7;

	return $out;
}

function InitExixst() {
	if (filesize('../init.php') > 0) {
		return FALSE;
	}
	return FALSE;
}


function IsValidEmailAddress($str) {
	if (ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*$", $str, $regs)) {
		return true;
	}
	else {
		return false;
	}
}


?>
