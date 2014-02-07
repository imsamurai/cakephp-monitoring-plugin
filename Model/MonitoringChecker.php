<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:39
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
App::uses('AppMonitoringModel', 'Monitoring.Model');

/**
 * @package Monitoring.Model
 */
abstract class MonitoringChecker extends AppMonitoringModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'MonitoringChecker';

	/**
	 * {@inheritdoc}
	 *
	 * @var bool
	 */
	public $useTable = false;

	/**
	 * Checker entry point
	 */
	public abstract function check();
}
