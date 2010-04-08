<?php
/**
 * $Id$
 * @author David Raison <david@hackerspace.lu>
 * @license GPLv3
 * @version 0.1.1
 * This is the ArchReactor OS dispatch controller
 */

class Dispatcher {
	
	private $_observers;
	
	/**
	 * constructs the dispatcher
	 * set variables and index the listeners.
	 */
	public function __construct(){
		$this->_dir = dirname(__FILE__).'extensions';
		$this->_observers = array();
		$this->_indexListeners();
	}

	/** 
	 * triggers a hook
	 * @param $args array of parameters to be passed to callback function
	 * @param $event string triggered event
	 */
	public function trigger($event,$args){
		foreach($this->_observers[$event] as $listener => $params){
			if(is_array($params))
				list($method,$async) = $params;
			else $method = $params;
			$this->_runListener($listener,$method,$args);	
		}
	}
	
	private function _runListener($listener,$method,$args){
		$lstnr = new $listener;
		if(method_exists($lstnr,$method)){
			call_user_func_array(array($lstnr,$method),$args);
			return true;
		} else throw new Exception("Extension $listener does not have required method $method!");
	}
	
	private function _indexListeners(){
		$dh = opendir($this->_dir);
		while($file = readdir($dh)){
			if(preg_match('/^\.+.*/',$file)) continue; // filter everything starting with a dot
			elseif(strstr($file,'.extension.php')){
				if(file_exists($this->_dir.DIRECTORY_SEPARATOR.$file)){
					include_once $this->_dir.DIRECTORY_SEPARATOR.$file;
					$this->_observers = array_merge_recursive($this->_observers,$arosHooks);
				}
			}
		}
		closedir($dh);
	}
}
?>
