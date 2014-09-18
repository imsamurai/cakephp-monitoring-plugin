<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Feb 7, 2014
 * Time: 5:22:47 PM
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */

/**
 * AllMonitoringTest
 * 
 * @package MonitoringTest
 * @subpackage Test
 */
class AllMonitoringTest extends PHPUnit_Framework_TestSuite {

	/**
	 * 	All Monitoring tests suite
	 *
	 * @return PHPUnit_Framework_TestSuite the instance of PHPUnit_Framework_TestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All Monitoring Tests');
		$basePath = App::pluginPath('Monitoring') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($basePath);

		return $suite;
	}

}