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
		$listeners = $dc->getListeners($hook);
		$output = array();
		foreach($listeners as $hook => $lstnr){
			foreach($lstnr as $class){
				$final = array_keys($class);
				$output[] = array('hook' => $hook, 'listener' => $final[0]);
			}
		}
		return $output;
	}
	
	public function listExtensions(){
	}
	
	public function enable($extensionId){
	}
	
	public function disable($extensionId){
	}
}

?>
