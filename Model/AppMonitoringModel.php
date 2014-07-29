<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 09.07.2013
 * Time: 16:44:11
 * Format: http://book.cakephp.org/2.0/en/models.html
 */

/**
 * @package Monitoring.Model
 */
class AppMonitoringModel extends AppModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'AppMonitoringModel';

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $actsAs = array('Containable');
	
	/**
	 * {@inheritdoc}
	 *
	 * @var int
	 */
	public $recursive = -1;

}