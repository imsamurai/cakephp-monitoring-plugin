<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:33:32
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
App::uses('MonitoringChecker', 'Monitoring.Lib/Monitoring');

/**
 * @package Monitoring.Model
 */
class MonitoringSelfCheck extends MonitoringChecker {

	/**
	 * {@inheritdoc}
	 */
	public function check() {
		return true;
	}

}
