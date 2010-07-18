<?

$script_name = "ArchReactorOS 1.0";

$req_php_version = '5.1.0';

$req_php_module = array(
  'MySQL' => 'mysql_connect'
);

$req_chmod_777 = array(
  array('name'=>'init.php', 'type'=>'file'),
  array('name'=>'templates_c', 'type'=>'dir'),
  array('name'=>'extensions', 'type'=>'dir')
);

$sql_option = array(
  'Fresh Installation' => '_membership.sql',
  'Reinstall (no need to create/upgrade database)' => ''
);

$next_bt = "<input type=submit value='Next Step'>";

$module_ok = "<font color=green>Installed</font>";
$module_failed = "<font color=red>Not Installed</font>";

$writeable_ok = "<font color=green>writeable</font>";
$writeable_failed = "<font color=red>not writeable</font>";

$message_step1 = "
<p>Welcome to $script_name installation!</p>
<p>Please follow the steps carefully. If you have any troubles,
do not hesitate to contact our support department.</p>
";


$message_step5 = "
    <input type=hidden name=pf value=1>
    <font color=red><p><%\$error_message%></p></font>
    <table class='app_setting' align='left'>
      <tr>
        <td colspan='2'>
          <b>Database Configuration</b>
        </td>
      </tr>
      <tr>
        <td class='td1'>Username
        </td>
        <td  class='td2'><input class='textbox' type='text' size='40' name='dbUsername' value='<%\$dbUsername%>'></td>
      </tr>
      <tr>
        <td class='td1'>Password
        </td>
        <td  class='td2'><input class='textbox' type='text' size='40' name='dbPassword' value='<%\$dbPassword%>'></td>
      </tr>
      <tr>
        <td class='td1'>Host
        </td>
        <td  class='td2'><input class='textbox' type='text' size='40' name='dbHostname' value='<%\$dbHostname%>'></td>
      </tr>
      <tr>
        <td class='td1'>Database Name
        </td>
        <td  class='td2'><input class='textbox' type='text' size='40' name='dbName' value='<%\$dbName%>'></td>
      </tr>

      <tr>
        <td colspan='2'>
          <b>URL and Path</b>
        </td>
      </tr>
      <tr>
        <td class='td1'>Website URL (ends without /)
        </td>
        <td  class='td2'><input class='textbox' type='text' size='40' name='site_url' value=\"<%\$site_url%>\"></td>
      </tr>
      <tr>
        <td class='td1'>Base Path (ends with /)
        </td>
        <td  class='td2'><input class='textbox' type='text' size='40' name='base_path' value=\"<%\$base_path%>\"></td>
      </tr>

      <tr>
        <td colspan='2'>
          <b>Website</b>
        </td>
      </tr>
      <tr>
        <td class='td1'>Website Name
        </td>
        <td  class='td2'><input class='textbox' type=text size='40' name=site_name value=\"<%\$site_name%>\"></td>
      </tr>
      <tr>
        <td class='td1'>Email address
        </td>
        <td class='td2'><input class='textbox' type=text size='40' name=email value=\"<%\$email%>\"></td>
      </tr>
    </table>
";

$message_step6 = "
  SQL query executed successfully.
";

$message_step6_err = "
  SQL query errored, fix and refresh or go back a page or 2.
";

$message_step7 = "
<p>You have successfully installed AROS!
Please delete this <b>/install</b> folder to secure
to your web site. Thank you and enjoy our product.</p>
<p>Your initial password for 'admin' is 'adminpw', change it as soon as possible.</p>
<p><a href=../admin>Go to admin panel</a></p>
";
?>
