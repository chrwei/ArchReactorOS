<?php

/**
 *
 * nicemember
 * Copyright(C), Nicecoder, 2008, All Rights Reserved.
 *
 */

  include dirname(__FILE__) . "/smarty/Smarty.class.php";

  class Template extends Smarty {

    function Template() {
      $this->Smarty();

      $this->template_dir    = CFG_SITE_PATH.'templates/';
      $this->compile_dir     = CFG_SITE_PATH.'templates_c/';
      $this->caching         = false;
			$this->use_sub_dirs	   = false;
      
    }
  }
?>