<?php
/**
 * Encapsulate an APRS message
 * 
 * @author Alex Mirea <yo3igc@gmail.com>
 * @license http://creativecommons.org/licenses/by/3.0/
 */
class AprsMessage {

	/**
	 * @var string
	 */
	protected $_message_regex = '%^([A-Z0-9\-]+?)\>(.+?)::([A-Z0-9\-\s]+?):([^\|^\{^\~]+)\{?(.{0,5})%';
	
	/**
	 * @var boolean
	 */
	protected $_is_dirty = FALSE;

	/**
	 * @var string
	 */
	protected $_from;

	/**
	 * @var string
	 */
	protected $_to;

	/**
	 * @var string
	 */
	protected $_path;
	
	/**
	 * @var string
	 */
	protected $_message;
	
	/**
	 * @var string
	 */
	protected $_code;

	/**
	 * @return the $_is_dirty
	 */
	public function isDirty() {
		return $this->_is_dirty;
	}
	
	/**
	 * @return the $_from
	 */
	public function getFrom() {
		return $this->_from;
	}

	/**
	 * @return the $_to
	 */
	public function getTo() {
		return $this->_to;
	}

	/**
	 * @return the $_path
	 */
	public function getPath() {
		return $this->_path;
	}

	/**
	 * @return the $_message
	 */
	public function getMessage() {
		return $this->_message;
	}
	
	/**
	 * @return the $_code
	 */
	public function getCode() {
		if (empty($this->_code)) {
			return FALSE;
		}
		return $this->_code;
	}

	/**
	 * @param string $from
	 */
	public function setFrom($from) {
		$this->_from = $from;
		$this->_is_dirty = TRUE;
	}

	/**
	 * @param string $to
	 */
	public function setTo($to) {
		$this->_to = $to;
		$this->_is_dirty = TRUE;
	}

	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->_path = $path;
		$this->_is_dirty = TRUE;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->_message = $message;
		$this->_is_dirty = TRUE;
	}
	
	/**
	 * @param string $_code
	 */
	public function setCode($_code) {
		$this->_code = $_code;
		$this->_is_dirty = TRUE;
	}

	/**
	 * AprsMessage class constructor
	 * @param string $raw_message
	 */
	public function __construct($raw_message = FALSE) {
		if (!empty($raw_message)) {
			$this->_is_dirty = $this->_parse($raw_message);
		}
	}
	
	/**
	 * Generate a raw TNC2 message
	 * @return mixed
	 */
	public function getRaw() {
		if ($this->_is_dirty === FALSE) {
			return FALSE;
		}
		
		$from = $this->getFrom();
		$to = substr(str_pad($this->getTo(), 9), 0, 9);
		$path = $this->getPath();
		$message = substr($this->getMessage(), 0, 67);
		$code = substr($this->getCode(), 0, 5);
		if (empty($from) || empty($to) || empty($path) || empty($message)) {
			return FALSE;
		}
		
		$aprs_message = "{$from}>{$path}::{$to}:$message";
		if (!empty($code)) {
			$aprs_message .= '{' . $code;
		}
		return $aprs_message;
	}
	
	/**
	 * Get ACK message
	 * @return mixed
	 */
	public function getAck() {
		if (!$this->_is_dirty || $this->getCode() === FALSE) {
			return FALSE;
		}
		$aprs_message = $this->getTo() . '>TCPIP*::';
		$aprs_message .= substr(str_pad($this->getFrom(), 9), 0, 9) . ':ack' . substr($this->getCode(),0,5);
		return $aprs_message;
	}
	
	/**
	 * Parse a raw message into individual components
	 * @param string $raw_message
	 * @return boolean
	 */
	private function _parse($raw_message) {
		if (preg_match($this->_message_regex, $raw_message, $matches)) {
			$this->setFrom($matches[1]);
			$this->setPath($matches[2]);
			$this->setTo($matches[3]);
			$this->setMessage($matches[4]);
			$this->setCode($matches[5]);
			return TRUE;
		}
		return FALSE;
	}	
}