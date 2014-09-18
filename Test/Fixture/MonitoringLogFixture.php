<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 */

/**
 * MonitoringLogFixture
 *
 * @package MonitoringTest
 * @subpackage Fixture
 */
class MonitoringLogFixture extends CakeTestFixture {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $useDbConfig = 'test';

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'),
		'monitoring_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20),
		'code' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10),
		'code_string' => array('type' => 'string', 'null' => false, 'default' => 'OK', 'length' => 100),
		'error' => array('type' => 'string', 'null' => false, 'default' => ''),
		'created' => array('type' => 'datetime', 'null' => false),
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $records = array();

}
