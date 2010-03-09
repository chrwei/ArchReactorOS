<?

class DB_Connection {
  var $conn = null;
  var $debug = false;
  var $num_query = 0; 
  var $error_msg = "";
  var $affected_rows = 0;

  function DB_Connection($host, $username, $pwd, $db) {
    $this->Connect($host, $username, $pwd, $db);
  }

  function Connect($host, $username, $pwd, $db) {
    global $db_use_persistent;
    if ($db_use_persistent) {
      $this->conn = mysql_pconnect($host, $username, $pwd);
    }
    else {
      $this->conn = mysql_connect($host, $username, $pwd);
    }
    mysql_select_db($db, $this->conn);
  }

  function Execute($query) {
    $query = trim($query);
    $this->num_query++;
    if ($this->debug) {
      static $mysql_query_number;
      static $mysql_query_time;
      $mysql_query_number++;
      $time_start = (float) array_sum(explode(' ', microtime()));
    }
    $result = @mysql_query($query, $this->conn);
    if ($this->debug) {
      $time_end = (float) array_sum(explode(' ', microtime()));
      $time = $time_end - $time_start;
      $time = sprintf("%01.4f", $time);
      $mysql_query_time += $time;
      $mysql_query_time = sprintf("%01.3f", $mysql_query_time);
      if ($time > 1) {
        $time = "<b>$time [slow query]</b>";
      }
      if (preg_match('/^select/i', $query)) {
        $num_rows = @mysql_num_rows($result);
        echo "<table width='100%' border='1' cellspacing='1'><tr valign='top'><td width='70'>QUERY #$mysql_query_number: </td><td>$query (result : $num_rows, time : $time)</td><td align='right' width='30'>$mysql_query_time</td></tr></table>";
      }
      else {
        echo "<table width='100%' border='1' cellspacing='1><tr valign='top'><td width='70'>QUERY #$mysql_query_number: </td><td>$query (time : $time)</td></td><td align='right' width='30'>$mysql_query_time</td></tr></table>";
      }
      if (mysql_error()) {
        echo "<table width='100%' border='1' cellspacing='1><tr valign='top'><td width='70'><font color='red'><b>ERROR : </b></font></td><td><font color='red'><b>".mysql_error()."</b></font></td><td align='right' width='30'>$mysql_query_time</td></tr></table>";
      }
    }
    $this->error_msg = mysql_error();
    if ($this->error_msg) {
      return false;
    }
    else {
      if (preg_match("/^(update|insert|delete)/msi", $query)) {
        $this->affected_rows = @mysql_affected_rows($this->conn);
        return new DB_Resultset_empty();
      }
      else {
        return new DB_Resultset($result, $query);
      }
    }
  }

  function PageExecute($query, $pg_which, $pg_size) {
    if (!$pg_which) {
      $pg_which = 1;
    }
    $query_total = $query;
    if (!preg_match('/group by/msi', $query_total)) {
      $query_total = preg_replace("|select(.*?)from|ms", "select count(*) as c from", $query_total);
    }
    $result = $this->Execute($query_total);
    if (preg_match('/group by/msi', $query_total)) {
      $num_rows = $result->RecordCount();
    }
    else {
      $num_rows = ($result->Fields('c')) ? $result->Fields('c') : 0;
    }
    $start = ($pg_which - 1) * $pg_size;
    $query = $query . " limit $start, $pg_size";
    $result->Close();
    $result = $this->Execute($query);
    $result->num_rows = $num_rows;
    return $result;
  }

  function InsertID() {
    return @mysql_insert_id($this->conn);
  }

  function Close() {
    return true;
  }

  function FetchArray($query) {
    $result = $this->Execute($query);
    $arr = array ();
    while ($row = $result->FetchRow()) {
      $arr[] = $row;
    }
    $result->Close();
    return $arr;
  }

  function FetchOne($query) {
    $result = $this->Execute($query . ' limit 1');
    $row = $result->FetchRow();
    $result->Close();
    return $row;
  }

  function Lookup($field, $table, $where) {
    $result = $this->Execute("select $field from $table where $where limit 1");
    $value = $result->Fields($field);
    $result->Close();
    return $value;
  }

  function CountQuery() {
    return $this->num_query;
  }

  function ErrorMsg() {
    return $this->error_msg;
  }

  function AffectedRows() {
    return $this->affected_rows;
  }
}

class DB_Resultset {
  var $resultset = null;
  var $num_rows = null;
  var $num_field = null;
  var $current_row = null;
  var $EOF = true;
  var $query = null;

  function DB_Resultset(&$resultset, $query = '') {
    $this->resultset = $resultset;
    $this->query = $query;
    if (preg_match('/^select|show|describe|explain/msi', $query)) {
      $this->MoveNext();
    }
  }

  function RecordCount() {
    if (is_null($this->num_rows)) {
      $this->num_rows = @mysql_num_rows($this->resultset);
    }
    return $this->num_rows;
  }

  function FieldCount() {
    if (is_null($this->num_field)) {
      $this->num_field = mysql_num_fields($this->resultset);
    }
    return $this->num_field;
  }

  function FetchField($offset) {
    $field = mysql_fetch_field($this->resultset, $offset);
    $field->max_length = mysql_field_len($this->resultset, $offset);
    return $field; 
  }

  function MetaType($field_type) {
    switch($field_type) {
      case preg_match('/char/i', $field_type):
        return 'C';
      case preg_match('/int|float|double/i', $field_type):
        return 'I';
      case preg_match('/text/i', $field_type):
        return 'X';
      case preg_match('/blob/', $field_type):
        return 'B';
      default:
        return '';
    }
  }

  function Move($offset) {
    if (@mysql_data_seek($this->resultset, $offset)) {
      return $this->MoveNext();
    }
    else {
      return false;
    }
  }

  function MoveFirst() {
    return $this->Move(0);
  }

  function MoveLast() {
    return $this->Move($this->num_rows - 1);
  }

  function MoveNext() {
    if ($row = @mysql_fetch_assoc($this->resultset)) {
      $this->current_row = $row;
      $this->EOF = false;
      return true;
    }
    else {
      $this->EOF = true;
      return false;
    }
  }

  function Fields($name) {
    return $this->current_row[$name];
  }

  function FetchRow() {
    if (!$this->EOF) {
      $row = $this->current_row;
      $this->MoveNext();
      return $row;
    }
    else {
      return false;
    }
  }

  function Close() {
    if ($this->resultset) {
      mysql_free_result($this->resultset);
    }
  }
}

class DB_Resultset_empty {
  var $resultset = null;
  var $num_rows = null;
  var $num_field = null;
  var $current_row = null;
  var $EOF = true;
  var $query = null;

  function DB_Resultset() {
    return true;
  }

  function RecordCount() {
    return 0;
  }

  function FieldCount() {
    return 0;
  }

  function FetchField($offset) {
    return false;
  }

  function MetaType($field_type) {
    return false;
  }

  function Move($offset) {
    return false;
  }

  function MoveFirst() {
    return false;
  }

  function MoveLast() {
    return false;
  }

  function MoveNext() {
    return false;
  }

  function Fields($name) {
    return false;
  }

  function FetchRow() {
    return false;
  }

  function Close() {
    return true;
  }
}


$ADODB_SESS_CONN = null;
$ADODB_SESS_MD5 = false;

class DB_Session {

  function Open($save_path, $session_name) {
    global $ADODB_SESS_CONN, $dbServer, $dbHostname, $dbUsername, $dbPassword, $dbName;
    //echo "open<br>";
    if (is_null($ADODB_SESS_CONN)) {
      $ADODB_SESS_CONN = new DB_Connection($dbHostname, $dbUsername, $dbPassword, $dbName);
    }
    return true;
  }

  function Close() {
    global $ADODB_SESS_CONN;
    //echo "close<br>";
    if (!is_null($ADODB_SESS_CONN)) {
      $ADODB_SESS_CONN->Close();
    }
    return true;
  }

  function Read($key) {
    global $ADODB_SESS_CONN, $ADODB_SESS_MD5;
    //echo "read<br>";
    $data = '';
    if ($ADODB_SESS_CONN) {
      $query = "select data from idx_sessions where sesskey = '$key' AND expiry >= " . time();
      $result = $ADODB_SESS_CONN->Execute($query);
      if ($result->RecordCount()) {
        $data = rawurldecode($result->Fields('data'));
      }
      $ADODB_SESS_MD5 = md5($data);
      $result->Close();
    }
    return $data;
  }

  function Write($key, $data) {
    global $ADODB_SESS_CONN, $ADODB_SESS_MD5;
    //echo "write<br>";
    if ($ADODB_SESS_CONN) {
      $lifetime = ini_get('session.gc_maxlifetime');
      if ($lifetime <= 1) {
        $lifetime = 1440;
      }
      $expiry = time() + $lifetime;
      if ($ADODB_SESS_MD5 !== false && $ADODB_SESS_MD5 == md5($data)) {
        $query = "update idx_sessions set expiry = '$expiry' where sesskey = '$key'";
      }
      else {
        $data = rawurlencode($data);
        $query = "replace into idx_sessions (sesskey, expiry, data) values ('$key', '$expiry', '$data')";
      }
      $ADODB_SESS_CONN->Execute($query);
    }
    return true;
  }

  function Destroy($key) {
    global $ADODB_SESS_CONN;
    //echo "detroy<br>";
    if ($ADODB_SESS_CONN) {
      $query = "delete from idx_sessions where sesskey = '$key'";
      $ADODB_SESS_CONN->Execute($query);
    }
		return true;
  }

  function GC($maxlifetime) {
    global $ADODB_SESS_CONN;
    //echo "gc<br>";
    if ($ADODB_SESS_CONN) {
      $query = "delete from idx_sessions where expiry < ".time();
      $ADODB_SESS_CONN->Execute($query);
      $query = "optimize table idx_sessions";
      $ADODB_SESS_CONN->Execute($query);
    }
    return true;
  }

  function Init() {
    //echo "init<br>";
    session_module_name('user');
    session_set_save_handler(
      array('DB_Session', 'Open'),
      array('DB_Session', 'Close'),
      array('DB_Session', 'Read'),
      array('DB_Session', 'Write'),
      array('DB_Session', 'Destroy'),
      array('DB_Session', 'GC')
    );
  }
}

DB_Session::Init();

?>