<?php
/**
 * Service configuration variables
 * 
 * @author Alex Mirea <yo3igc@gmail.com>
 * @license http://creativecommons.org/licenses/by/3.0/
 */
class Config {
	/**
	 * The callsign that the service will be using
	 * @var string
	 */
	public static $service_callsign = '';
	
	/**
	 * APRS-IS password
	 * See http://www.aprs-is.net/Connecting.aspx
	 * @var string
	 */
	public static $aprs_pass = '';
	
	/**
	 * Database connection credentials
	 * @var array
	 */
	public static $db = array(
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database_name' => 'aprs',
	);
	
	public static $aprs_is_host = 'euro.aprs2.net';
	
	public static $aprs_is_port = '14580';
}