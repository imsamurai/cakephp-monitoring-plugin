<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:33:32
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
declare(ticks = 1);

App::uses('MonitoringChecker', 'Monitoring.Lib/Monitoring');

/**
 * Always fail checker, for test purposes
 * 
 * @package Monitoring
 * @subpackage Monitoring
 */
class MonitoringSelfFailCheck extends MonitoringChecker {

	/**
	 * {@inheritdoc}
	 */
	public function check() {
		$this->addError('Test error');
		$this->addError($this->_settings['errorText']);
		return false;
	}

}
