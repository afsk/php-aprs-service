#!/usr/local/bin/php -q
<?php
/**
 * PHP APRS Service
 * @author Alexandru Mirea <yo3igc@gmail.com>
 * @license http://creativecommons.org/licenses/by/3.0/
 */

// run forever
set_time_limit(0);

// path to service directory
define('BASE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

// include autoloader class
require_once(BASE_PATH . 'Loader.php');

// include autoloader class
require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Daemon.php');

try {
	// register autoloader
	Loader::getInstance()->register();

	$options = array(
		'appName' => 'paprsd',
		'appDir' => dirname(__FILE__),
		'appDescription' => 'Listen for APRS messages and reply',
		'authorName' => 'Alexandru Mirea - YO3IGC',
		'authorEmail' => 'yo3igc@gmail.com',
		'sysMaxExecutionTime' => '0',
		'sysMaxInputTime' => '0',
		'sysMemoryLimit' => '32M',
		'appRunAsGID' => 1000,
		'appRunAsUID' => 1000,
	);
	System_Daemon::setOptions($options);
	System_Daemon::start();

	$db = new Database();
	$comm = new Comm;
	
	while (!System_Daemon::isDying()) {
		if (!$comm->isConnected()) {
			System_Daemon::info('Opening connection to server');
			if (!$comm->connect()) {
				System_Daemon::info('Failed connection to server. Retry in 10 seconds');
				System_Daemon::iterate(9);
			}
			else {
				$welcome_message = $comm->read();
				$comm->write(
					'user ' . Config::$service_callsign . 
					' pass ' . Config::$aprs_pass . ' vers paprsd 0.1 filter t/m u/' . Config::$service_callsign
				);
				// log auth result
				System_Daemon::info($comm->read());
			}
		}

		if ($comm->isConnected()) {
			// this instruction is blocking the current thread!!!
			$raw_message = $comm->read();
			$aprs_message = new AprsMessage($raw_message);
			if ($aprs_message->isDirty() && $aprs_message->getTo() == Config::$service_callsign) {
				$db->add($aprs_message);
				if ($aprs_message->getAck() != FALSE) {
					System_Daemon::info('Sending ACK');
					$comm->write($aprs_message->getAck());
				}
			}
			unset($aprs_message);
			System_Daemon::iterate();
		}
		else {
			System_Daemon::iterate(1);
		}			
	}
	System_Daemon::stop();
}
catch (Exception $e) {
	openlog('paprsd', LOG_PID | LOG_PERROR, LOG_LOCAL0);
	syslog(LOG_ERR, $e->getMessage());
	closelog();
	exit(1);
}