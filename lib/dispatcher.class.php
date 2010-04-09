<?php
/**
 * $Id:$
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
		$this->_dir = CFG_SITE_PATH.'extensions';
		$this->_observers = array();
		$this->_indexListeners($this->_dir);
	}

	/** 
	 * triggers a hook
	 * @param $args array of parameters to be passed to callback function
	 * @param $event string triggered event
	 */
	public function trigger($event,$args=''){
		if(empty($this->_observers)) return;    // nothing to see here...
		foreach($this->_observers[$event] as $current_observers){
			foreach($current_observers as $listener => $params){
				if(is_array($params))
					list($method,$args) = $params;
				else $method = $params;
				$this->_runListener($listener,$method,$args);
			}
		}
	}
	
	private function _runListener($listener,$method,$args=''){
		$lstnr = new $listener;
		if(method_exists($lstnr,$method)){
			call_user_func_array(array($lstnr,$method),$args);
			return true;
		} else throw new Exception("Extension $listener does not have required method $method!");
	}
	
	private function _indexListeners(){
		if(file_exists($dir) && is_readable($dir)){
			$dh = opendir($dir);
			while($file = readdir($dh)){
				if(preg_match('/^\.+.*/',$file)) continue; // filter everything starting with a dot
				elseif(is_dir($dir.DIRECTORY_SEPARATOR.$file))
					$this->_indexListeners($dir.DIRECTORY_SEPARATOR.$file);
				elseif(strstr($file,'.extension.php')){
					include_once $dir.DIRECTORY_SEPARATOR.$file;
					if($arosHooks) $this->_observers = array_merge_recursive($this->_observers,$arosHooks);
				} else throw new Exception('Encountered unexpected exception while indexing listeners.');
			}
			closedir($dh);
		}
		return;
	}
	
}
?>
