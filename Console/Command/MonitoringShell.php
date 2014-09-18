<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:00
 * Format: http://book.cakephp.org/2.0/en/console-and-shells.html#creating-a-shell
 */
App::uses('AdvancedShell', 'AdvancedShell.Console/Command');

/**
 * Monitoring shell
 * 
 * @package Monitoring
 * @subpackage Console.Command
 */
class MonitoringShell extends AdvancedShell {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $tasks = array(
		'Run' => array(
			'className' => 'Monitoring.MonitoringRun'
		),
		'Update' => array(
			'className' => 'Monitoring.MonitoringUpdate'
		)
	);

}
