<?php

/**
 * MonitoringFixture
 *
 * @package Monitoring.Test.Fixture
 */
class MonitoringFixture extends CakeTestFixture {

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
		'name' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => 50),
		'class' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => 500),
		'description' => array('type' => 'string', 'null' => false, 'default' => ''),
		'cron' => array('type' => 'string', 'null' => false, 'length' => 100, 'default' => '*/5 * * * *'),
		'timeout' => array('type' => 'integer', 'null' => false, 'default' => 600, 'length' => 10),
		'last_code_string' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => 100),
		'last_check' => array('type' => 'datetime', 'null' => false),
		'next_check' => array('type' => 'datetime', 'null' => false),
		'active' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1),
		'priority' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10),
		'emails' => array('type' => 'string', 'null' => false, 'default' => ''),
		'settings' => array('type' => 'string', 'null' => false, 'default' => ''),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $records = array(
	);


}
