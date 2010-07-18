<?php
/**
 * $Id:$
 * @author David Raison <david@hackerspace.lu>
 * @license GPLv3
 * @version 0.0.1
 * This is the ArchReactor OS extensions management console
 */

include '../init.php';
$user->AuthenticationAdmin();

// Instantiate our extension controller
$ec = new ExtensionController;

switch($_REQUEST['pf']){
	case 'search':	// fall-through!
	case 'browse':	// id
	default:
		$listeners = $ec->listListeners($_REQUEST['hook']);
		$tpl->assign('pf',$_REQUEST['pf']);
		$tpl->assign('listeners',$listeners);
		$tpl->display('admin/extension.html');
	break;
	case 'enable':
	break;
	case 'disable':
	break;
}

?>
