<?php

/**
 * MonitoringLogFixture
 *
 * @package Monitoring.Test.Fixture
 */
class MonitoringLogFixture extends CakeTestFixture {

	public $useDbConfig = 'test';

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20, 'key' => 'primary'),
		'monitoring_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20),
		'code' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10),
		'code_string' => array('type' => 'string', 'null' => false, 'default' => 'OK', 'length' => 100),
		'stderr' => array('type' => 'string', 'null' => false, 'default' => ''),
		'stdout' => array('type' => 'string', 'null' => false, 'default' => ''),
		'created' => array('type' => 'datetime', 'null' => false),
	);

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = array(
		array('id' => 1, 'monitoring_id' => 1, 'code' => 0, 'code_string' => 'OK', 'created' => '2012-05-05 12:22:22')
	);

}
