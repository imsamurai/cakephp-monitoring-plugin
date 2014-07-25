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
	 * Error
	 *
	 * @var string
	 */
	protected $_error = null;

	/**
	 * Checker settings
	 *
	 * @var array 
	 */
	protected $_settings = null;

	/**
	 * Constructor
	 * 
	 * @param array $settings
	 */
	public function __construct(array $settings) {
		$this->_settings = $settings + (array)Configure::read('Monitoring.checkers.' . get_class($this) . '.defaults');
	}

	/**
	 * Checker entry point
	 */
	public abstract function check();

	/**
	 * Return error
	 * 
	 * @return string
	 */
	public function getError() {
		return $this->_error;
	}

	/**
	 * Add error
	 * 
	 * @param string $error
	 */
	public function addError($error) {
		$this->_error = ($this->_error ? "\n" : '') . (string)$error;
	}

}
