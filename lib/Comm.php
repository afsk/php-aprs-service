<?php
/**
 * APRS-IS communication class
 * 
 * @author Alex Mirea <yo3igc@gmail.com>
 * @license http://creativecommons.org/licenses/by/3.0/
 */
class Comm {
	/**
	 * @var file pointer
	 */
	private $_fp = FALSE;
	
	/**
	 * Connect to APRS-IS
	 * @return boolean
	 */
	public function connect() {
		if (!$this->isConnected()) {
			$this->_fp = fsockopen(Config::$aprs_is_host, Config::$aprs_is_port);
			if (!$this->isConnected()) {
				return FALSE;
			}
		}
		return TRUE;
	}
	
	/**
	 * Read a line from APRS-IS server
	 * @return mixed
	 */
	public function read() {
		if (!$this->isConnected()) {
			return FALSE;
		}
		return fgets($this->_fp, 512);
	}
	
	/**
	 * Write a line to APRS-IS server
	 * @param string $content
	 */
	public function write($content) {
		if (!$this->isConnected()) {
			return FALSE;
		}
		$content = $content . "\n";
		return fwrite($this->_fp, $content);
	}
	
	/**
	 * Check if the connection is up
	 * @return boolean
	 */
	public function isConnected() {
		return $this->_fp !== FALSE;
	}
	
	/**
	 * Class destructor
	 * Close connection
	 */
	public function __destruct() {
		if ($this->_fp !== FALSE) {
			fclose($this->_fp);
		}
	}
}