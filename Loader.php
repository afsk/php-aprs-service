<?php
/**
 * Autoload class
 * @author Alex Mirea <yo3igc@gmail.com> 
 * 
 * @license http://creativecommons.org/licenses/by/3.0/ 
 */
class Loader {
	
	/**
	 * @var Loader instance
	 */
	static private $_instance;
	
	/**
	 * @var string
	 */
	private $_lib_dir = 'lib';

	/**
	 * @var string
	 */
	private $_config_dir = 'config';
	
	/**
	 * Get the loader object
	 * @return Loader instance
	 */
	static public function getInstance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	/**
	 * Register autoload method 
	 */
	public function register() {
		spl_autoload_register(array($this, '_autoload'));
	}
	
	/**
	 * Load the file holding the specified class
	 * @param string $class_name 
	 */
	private function _autoload($class_name) {
		if (strpos($class_name, 'Config') !== FALSE) {
			$required_file = BASE_PATH . $this->_config_dir . DIRECTORY_SEPARATOR . $class_name . '.php';
		}
		else {
			$required_file = BASE_PATH . $this->_lib_dir . DIRECTORY_SEPARATOR . $class_name . '.php';
		}
		$this->_require($required_file);
	}
	
	/**
	 * Manualy include a file from lib folder
	 * @param string $file
	 */
	public function load($file) {
		$required_file = BASE_PATH . $this->_lib_dir . DIRECTORY_SEPARATOR . $file;
		$this->_require($required_file);
	}
	/**
	 * Check and include the file
	 * @param string $file
	 * @throws Exception 
	 */
	private function _require($file) {
		if (!file_exists($file)) {
			throw new Exception('Required file not found: ' . $file);
		}
		include_once($file);
	}
}