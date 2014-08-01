<?php

declare(ticks = 1);
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:33:32
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
App::uses('MonitoringChecker', 'Monitoring.Lib/Monitoring');

/**
 * @package Monitoring.Lib.Checker
 */
class MonitoringSelfCheck extends MonitoringChecker {

	/**
	 * {@inheritdoc}
	 */
	public function check() {
		sleep(20);
		return true;
	}

}
