<?php
/**
 * FIFO register class
 * @author Alex Mirea <yo3igc@gmail.com>
 */
class Queue extends Singleton {
	/**
	 * @var array
	 */
	protected $_register = array();
	
	/**
	 * Get the oldest item from queue
	 * @param string $namespace
	 */
	function pop($namespace = '_default') {
		if (empty($this->_register[$namespace])) {
			return FALSE;
		}
		$element = array_pop($this->_register[$namespace]);
		if (--$element['ttl']) { // requeue element
			$this->push($element['payload'], $namespace, $element['ttl']);
		}
		return $element['payload'];
	}
	
	/**
	 * Add item to queue
	 * @param mixed $var
	 * @param string $namespace
	 * @param integer $ttl
	 * @return mixed
	 */
	function push($var, $namespace = '_default', $ttl = 1) {
		if (empty($this->_register[$namespace])) {
			$this->_register[$namespace] = array();
		}
		return array_push(
			$this->_register[$namespace], 
			array(
				'payload' => $var,
				'ttl' => $ttl,
			)
		);
	}
}