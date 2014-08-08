<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 18:09:32
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
App::uses('Monitoring', 'Monitoring.Model');
App::uses('MonitoringChecker', 'Monitoring.Lib/Monitoring');

/**
 * MonitoringTest
 * 
 * @property Monitoring $Monitoring Monitoring model
 * 
 * @package Monitoring.Test.Case.Model
 */
class MonitoringTest extends CakeTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.Monitoring.Monitoring',
		'plugin.Monitoring.MonitoringLog'
	);

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->Monitoring = ClassRegistry::init('Monitoring.Monitoring');
		$config = array(
			'dateFormat' => 'H:i:s d.m.Y',
			'dbDateFormat' => 'Y-m-d H:i:s',
			'checkersPath' => 'Lib/Monitoring',
			'defaults' => array(
				'cron' => '0 15 * * *',
				'timeout' => 600,
				'active' => false,
				'priority' => 0
			),
			'email' => array(
				'enabled' => array(
					'fail' => true,
					'stillFail' => true,
					'success' => false,
					'backToNormal' => true,
				),
				'config' => 'default',
				'subject' => array(
					'fail' => 'Monitoring: %s is fail!',
					'stillFail' => 'Monitoring: %s still failing!',
					'success' => 'Monitoring: %s is ok!',
					'backToNormal' => 'Monitoring: %s back to normal!',
				)
			),
			'views' => array(
				'pluginFirst' => false
			),
			'checkers' => array(
				'MonitoringSelfFailCheck' => array(
					'defaults' => array(
						'errorText' => 'MonitoringSelfFailCheck error text'
					)
				)
			)
		);
		Configure::write('Monitoring', $config);
	}

	/**
	 * Test get active checkers
	 * 
	 * @param array $checkers
	 * @param bool $checkNext
	 * @param array $ids
	 * 
	 * @dataProvider getActiveCheckersProvider
	 */
	public function testGetActiveCheckers(array $checkers, $checkNext, array $ids) {
		$this->Monitoring->saveMany($checkers);
		$checkers = $this->Monitoring->getActiveCheckers($checkNext);
		$this->assertSame($ids, array_map('intval', Hash::extract($checkers, '{n}.id')));
	}

	/**
	 * Data provider for testGetActiveCheckers
	 * 
	 * @return array
	 */
	public function getActiveCheckersProvider() {
		return array(
			//set #0
			array(
				//checkers
				array(
					array(
						'id' => 2,
						'name' => 'Test2',
						'active' => 0,
						'cron' => '*/5 * * * *',
						'priority' => 0,
						'last_check' => date(Configure::read('Monitoring.dbDateFormat'))
					),
					array(
						'id' => 3,
						'name' => 'Test3',
						'active' => 1,
						'cron' => '* * * * *',
						'last_check' => date(Configure::read('Monitoring.dbDateFormat'))
					),
					array(
						'id' => 4,
						'name' => 'Test4',
						'active' => 1,
						'cron' => '* * * * *',
						'priority' => 1,
						'last_check' => '0000-00-00 00:00:00'
					)
				),
				//checkNext
				false,
				//ids
				array(
					4, 3
				)
			),
			//set #1
			array(
				//checkers
				array(
					array(
						'id' => 2,
						'name' => 'Test2',
						'active' => 0,
						'cron' => '*/5 * * * *',
						'priority' => 0,
						'last_check' => date(Configure::read('Monitoring.dbDateFormat'))
					),
					array(
						'id' => 3,
						'name' => 'Test3',
						'active' => 1,
						'cron' => '*/3 */4 * * *',
						'last_check' => date(Configure::read('Monitoring.dbDateFormat'))
					),
					array(
						'id' => 4,
						'name' => 'Test4',
						'active' => 1,
						'cron' => '1 12 * * *',
						'priority' => 1,
						'last_check' => '0000-00-00 00:00:00'
					)
				),
				//checkNext
				true,
				//ids
				array()
			),
		);
	}

	/**
	 * Test BeforeSave
	 * 
	 * @param array $checkerIn
	 * @param array $checkerOut
	 * 
	 * @dataProvider beforeSaveProvider
	 */
	public function testBeforeSave(array $checkerIn, array $checkerOut) {
		$this->Monitoring->set($checkerIn);
		$this->Monitoring->beforeSave();
		$this->assertSame($checkerOut, $this->Monitoring->data[$this->Monitoring->alias]);
	}

	/**
	 * Data provider for testBeforeSave
	 * 
	 * @return array
	 */
	public function beforeSaveProvider() {
		return array(
			//set #0
			array(
				//checkerIn
				array(
					'id' => 4,
					'name' => 'Test4',
					'active' => 1,
					'cron' => '0 0 * * *',
					'priority' => 1,
					'last_check' => '0000-00-00 00:00:00'
				),
				//checkerOut
				array(
					'id' => 4,
					'name' => 'Test4',
					'active' => 1,
					'cron' => '0 0 * * *',
					'priority' => 1,
					'last_check' => '0000-00-00 00:00:00',
					'next_check' => (new DateTime('tomorrow'))->format(Configure::read('Monitoring.dbDateFormat'))
				)
			),
			//set #1
			array(
				//checkerIn
				array(
					'id' => 4,
					'name' => 'Test4',
					'active' => 1,
					'priority' => 1,
					'last_check' => '0000-00-00 00:00:00'
				),
				//checkerOut
				array(
					'id' => 4,
					'name' => 'Test4',
					'active' => 1,
					'priority' => 1,
					'last_check' => '0000-00-00 00:00:00',
					'cron' => '0 15 * * *',
					'next_check' => Cron\CronExpression::factory('0 15 * * *')
							->getNextRunDate('now')
							->format(Configure::read('Monitoring.dbDateFormat'))
				)
			),
		);
	}

	public function testFindAllCheckerClasses() {
		$classes = $this->Monitoring->findAllCheckerClasses();
		$this->assertNotEmpty($classes);
		$this->assertTrue(count(array_intersect($classes, array(
					'Monitoring.MonitoringSelfCheck',
					'Monitoring.MonitoringSelfFailCheck'
				))) === 2);
	}

	public function testFindNewCheckers() {
		$this->Monitoring->add('Monitoring.MonitoringSelfCheck');
		$classes = $this->Monitoring->findNewCheckers();
		$this->assertNotEmpty($classes);
		$this->assertTrue(count(array_intersect($classes, array(
					'Monitoring.MonitoringSelfCheck',
					'Monitoring.MonitoringSelfFailCheck'
				))) === 1);
	}

	/**
	 * Test save check results
	 * 
	 * @param int $id
	 * @param string $status
	 * @param string $error
	 * 
	 * @dataProvider saveCheckResultsProvider
	 */
	public function testSaveCheckResults($id, $status, $error) {
		$this->Monitoring->add('Monitoring.MonitoringSelfCheck', array('id' => $id));
		$this->Monitoring->saveCheckResults($id, $status, $error);
		$this->Monitoring->contain(array(
			'MonitoringLog'
		));
		$checker = $this->Monitoring->findById($id);
		//debug($checker);
		$this->assertEqual($checker['Monitoring']['last_code_string'], $status);
		$this->assertEqual($checker['MonitoringLog'][0]['code_string'], $status);
		$this->assertEqual($checker['MonitoringLog'][0]['error'], $error);
	}

	/**
	 * Data provider for testSaveCheckResults
	 * 
	 * @return array
	 */
	public function saveCheckResultsProvider() {
		return array(
			//set #0
			array(
				//checkerId
				1,
				//codeString
				MonitoringChecker::STATUS_OK,
				//error
				''
			),
			//set #1
			array(
				//checkerId
				2,
				//codeString
				MonitoringChecker::STATUS_FAIL,
				//error
				'someerror'
			),
		);
	}

}
