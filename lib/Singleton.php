<?php
/**
 * Singleton abstract class
 *
 * @author Alex Mirea <yo3igc@gmail.com>
 * @license http://creativecommons.org/licenses/by/3.0/
 */
abstract class Singleton
{
	/**
	 * @var array Singleton instances
	 */
	protected static $_instances = array();
	
	/**
	 * Returns instance object
	 * @return Singleton instance
	 */
	public static function getInstance() {
		$class = get_called_class();
		if (empty(self::$_instances[$class])) {
			self::$_instances[$class] = new $class();
		}
		return self::$_instances[$class];
	}
}
