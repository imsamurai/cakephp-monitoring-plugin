<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:39
 * Format: http://book.cakephp.org/2.0/en/models.html
 */

/**
 * @package Monitoring.Lib
 */
abstract class MonitoringChecker {

	/**
	 * Checker entry point
	 */
	public abstract function check();

	/**
	 * Return last error
	 */
	public abstract function error();
}
