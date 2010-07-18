<?php
/**
 * $Id:$
 * @author David Raison <david@hackerspace.lu>
 * @license GPLv3
 * @version 0.1.1
 * This is the ArchReactor OS dispatch controller
 * Singleton
 */

class Dispatcher {
	
	private $_observers;
	private static $_instance = null;
	
	/**
	 * constructs the dispatcher
	 * set variables and index the listeners.
	 */
	private function __construct(){
		$this->_dir = CFG_SITE_PATH.'extensions';
		$this->_observers = array();
		$this->_indexListeners($this->_dir);
	}
	
	/**
	 * Ensure that we always have but one instance of this Controller
	 * @return Dispatcher object
	 */
	public static function Instance(){
		if (self::$_instance == null)
			self::$_instance = new Dispatcher;
		return self::$_instance;
	}

	/** 
	 * triggers a hook
	 * @param $args array of parameters to be passed to callback function
	 * @param $event string triggered event
	 */
	public function trigger($hook,$args=''){
		if(empty($this->_observers[$hook])) return;    // nothing here, go away!...
		foreach($this->_observers[$hook] as $current_observers){
			foreach($current_observers as $listener => $params){
				if(is_array($params))
					list($method,$args) = $params;
				else $method = $params;
				$this->_runListener($listener,$method,$args);
			}
		}
	}
	
	/**
	 * Let us be queried about the registered listeners
	 * */
	public function getListeners($hook=''){
		return ($hook != '') ? array($hook => $this->_observers[$hook]) : $this->_observers;
	}
	
	private function _runListener($listener,$method,$args=''){
		$lstnr = new $listener;
		if(method_exists($lstnr,$method)){
			call_user_func_array(array($lstnr,$method),$args);
			return true;
		} else throw new Exception("Extension $listener does not have required method $method!");
	}
	
	private function _indexListeners($dir){
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
			return;
		} else throw new Exception('Extensions directory does not exist or is not readable');
	}
	
}
?>
