<?
include "../init.php";
include "2co.inc.php";

ignore_user_abort(true);
set_time_limit(300);

define('2CO_RETURN', 1);

$referers = array("www.2checkout.com", "www.2checkout.com", "2checkout.com");
$valid_referer = FALSE;
if ($_SERVER['HTTP_REFERER']) {
  while (list(, $host) = @each($referers)) {
    if (eregi($host, $_SERVER['HTTP_REFERER'])) {
      $valid_referer = TRUE;
    }
  }
}
else {
  $valid_referer = TRUE;
}

if (!$valid_referer) {
  exit;
}

if ($_POST['credit_card_processed'] != 'Y') {
  Redirect(urldecode($_POST['cancel_return']), FALSE);
  exit;
}

if ($_POST['secret'] != $co_secret) {
  exit;
}

if (ereg('ipn-co.php', $_POST['notify_url'])) {
  include "ipn-co.php";
  header('location:'.urldecode($_POST['return']));
  exit;
}
elseif (ereg('2co.check-invoice.php', $_POST['notify_url'])) {
  include "2co.check-invoice.php";
  Redirect(urldecode($_POST['return']), FALSE);
  exit;
}

?>
