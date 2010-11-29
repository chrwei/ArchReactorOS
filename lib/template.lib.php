<?php

/**
 * $Id:$
 * Proxy for Smarty
 * @author David Raison <david@hackerspace.lu>
 * @version 0.4.1
 * @license GPLv3
 */

include dirname(__FILE__) . "/smarty/Smarty.class.php";

class Template extends Smarty {

	public function __construct() {
  
		// telling smarty about our directory structure
		$this->template_dir		= CFG_SITE_PATH.'templates/';
		$this->compile_dir		= CFG_SITE_PATH.'templates_c/';
		$this->cache_dir 		= '';
		$this->config_dir 		= '';
		
		// setting some smarty vars
		$this->caching 			= FALSE;	// set to true in production
		$this->use_sub_dirs		= false;
		$this->debugging 		= false;		// debug popup
		$this->compile_check 	= TRUE;		// set to false in production
		$this->security 		= FALSE;	// set to true if untrusted parties have access to template files
			
		parent::__construct();
      }
}
?>
