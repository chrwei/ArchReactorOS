<?php
session_start();

define ('DB_USER','<%$dbUsername%>');
define ('DB_PWD','<%$dbPassword%>');
define ('DB_HOST','<%$dbHostname%>');
define ('DB_NAME','<%$dbName%>');
define ('CFG_SITE_PATH','<%$base_path%>');
define ('CFG_DATA_PATH','<%$base_path%>data/');

include CFG_SITE_PATH.'config.php';
include CFG_SITE_PATH.'lib/template.lib.php';
include CFG_SITE_PATH.'lib/adodb5/adodb.inc.php';
include CFG_SITE_PATH.'lib/phpmailer/class.phpmailer.php';
include CFG_SITE_PATH.'lib/functions.php';
include CFG_SITE_PATH.'lib/form_validation.lib.php';
include CFG_SITE_PATH.'lib/user.class.php';
include CFG_SITE_PATH.'lib/email.class.php';
include CFG_SITE_PATH.'lib/product.class.php';
include CFG_SITE_PATH.'lib/order.class.php';
include CFG_SITE_PATH.'lib/banned.class.php';
include CFG_SITE_PATH.'lib/coupon.class.php';
include CFG_SITE_PATH.'lib/payment.class.php';
include CFG_SITE_PATH.'lib/invoice.class.php';

$db = ADONewConnection('mysql');
$db->Connect(DB_HOST, DB_USER, DB_PWD, DB_NAME);
$db->debug = false;
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$tpl        = new Template;
$mail       = new Email;
$user       = new User;
$product    = new Product;
$order      = new Order;
$banned     = new Banned;
$coupon     = new Coupon;
$pay_class  = new Payment;
$inv_class  = new Invoice;

if (strpos($_SERVER['PHP_SELF'], '/install/') != TRUE) {
  GetConfig();
}

$tpl->assign('site_name',CFG_SITE_NAME);
$tpl->assign('admin_autenticated',$_SESSION['SESSION_ADMIN_AUTHENTICATED']);
$tpl->assign('sessions_username',$_SESSION['SESSION_USERNAME']);

/*===================================================
  php configuration
===================================================*/
ini_set("max_execution_time", "60");
ini_set('url_rewriter.tags', '');

/*===================================================
  register_globals & magic_quotes_gpc = on
===================================================*/
function safe_extract($var) {
  static $forbidden = array('_FILES', '_ENV', '_GET', '_POST', '_COOKIE', '_SERVER', '_SESSION', 'GLOBALS');
  while (list($k, $v) = @each($var)) {
    if (!in_array($k, $forbidden)) {
      $GLOBALS[$k] = $v;
    }
    else {
      exit;
    }
  }
}

function addslashes_deep($value) {
   $value = is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
   return $value;
}

if (!get_magic_quotes_gpc()) {
  if (!empty($_GET)) {
    $_GET = addslashes_deep($_GET);
    safe_extract($_GET);
  }
  if (!empty($_POST)) {
    $_POST = addslashes_deep($_POST);
    safe_extract($_POST);
  }
  if (!empty($_COOKIE)) {
    $_COOKIE = addslashes_deep($_COOKIE);
    safe_extract($_COOKIE);
  }
}
elseif (!ini_get('register_globals')) {
  if (!empty($_GET)) safe_extract($_GET);
  if (!empty($_POST)) safe_extract($_POST);
  if (!empty($_COOKIE)) safe_extract($_COOKIE);
}
?>
