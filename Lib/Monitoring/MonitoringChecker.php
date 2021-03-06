<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:39
 * Format: http://book.cakephp.org/2.0/en/models.html
 */

/**
 * Main class for checkers
 * 
 * @package Monitoring
 * @subpackage Monitoring
 */
abstract class MonitoringChecker {

	/**
	 * Statuses
	 */
	const STATUS_OK = "OK";
	const STATUS_FAIL = "FAIL";
	
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
	 * Return status
	 * 
	 * @return string
	 */
	public function getStatus() {
		return $this->_error ? static::STATUS_FAIL : static::STATUS_OK;
	}

	/**
	 * Add error
	 * 
	 * @param string $error
	 */
	public function addError($error) {
		$this->_error .= ($this->_error ? "\n" : '') . (string)$error;
	}

}
