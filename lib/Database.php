<?php
/**
 * Class that handles database operations
 * Uses "old" MySQL Functions for compatibility
 * 
 * @author Alex Mirea <yo3igc@gmail.com>
 * @license http://creativecommons.org/licenses/by/3.0/
 */
class Database {
	/**
	 * @var MySQL link identifier
	 */
	protected $_connection;
	
	/**
	 * Database class constructor
	 * @throws Exception
	 */
	public function __construct() {
		if ($this->_connect() === FALSE) {
			throw new Exception('Could not initiate database connection');
		}
	}
	
	/**
	 * Initiate a database connection
	 * @return boolean
	 */
	protected function _connect() {
		if (!$this->_connection) {
			$this->_connection = mysql_connect(Config::$db['host'], Config::$db['username'], Config::$db['password']);
			if ($this->_connection === FALSE ) {
				return FALSE;
			}
			if (!mysql_select_db(Config::$db['database_name'])) {
				return FALSE;
			}
			return mysql_set_charset('utf8', $this->_connection); 
		}
		
		return TRUE;
	}
	
	/**
	 * Add a parsed message into database
	 * @param AprsMessage $message
	 * @return boolean
	 */
	public function add($message) {
		if (!($message instanceof AprsMessage) || $this->_connect() === FALSE || !$message->isDirty()) {
			return FALSE;
		}
		
		$from = mysql_real_escape_string($message->getFrom(), $this->_connection);
		$to = mysql_real_escape_string($message->getTo(), $this->_connection);
		$path = mysql_real_escape_string($message->getPath(), $this->_connection);
		$message_text = mysql_real_escape_string($message->getMessage(), $this->_connection);
		$code = mysql_real_escape_string($message->getCode(), $this->_connection);
		return mysql_query(
			"INSERT INTO `messages` (`from`, `to`, `path`, `message`, `code`) VALUES 
			 ('{$from}', '{$to}', '{$path}', '{$message_text}', '{$code}')",
			$this->_connection
		) !== FALSE;
	}
	
	/**
	 * Get MySQL error
	 * @return string
	 */
	public function getError() {
		return mysql_error($this->_connection);
	}
	
	/**
	 * Database class destructor
	 */
	public function __destruct() {
		mysql_close($this->_connection);
	}
}