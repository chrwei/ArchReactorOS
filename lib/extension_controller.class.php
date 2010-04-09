<?php

class ExtensionController {
	
	public function __construct(){
	}
	
	/** 
	 * This is simply a matter of listing extensions
	 * indexed by the dispatcher.
	 * 
	 * */
	public function listListeners($hook=''){
		$dc = Dispatcher::Instance();
		return($dc->getListeners($hook));
	}
	
	public function listExtensions(){
	}
	
	public function enable($extensionId){
	}
	
	public function disable($extensionId){
	}
}

?>
