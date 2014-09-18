<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 09.09.2014
 * Time: 11:46:52
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */

/**
 * MonitoringControllerTest
 * 
 * @package MonitoringTest
 * @subpackage Controller
 */
class MonitoringControllerTest extends ControllerTestCase {

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
		Configure::write('Pagination.limit', 10);
	}

	/**
	 * Test index action
	 */
	public function testIndex() {
		$paginate = array(
			'Monitoring' => array(
				'limit' => Configure::read('Pagination.limit'),
				'order' => array('id' => 'desc')
			)
		);

		$Controller = $this->generate('Monitoring.Monitoring', array(
			'methods' => array(
				'paginate'
			)
		));

		$Controller->expects($this->once())->method('paginate')->with('Monitoring')->willReturn(array('Monitoring pagination data'));

		$this->testAction('/monitoring/monitoring/index', array(
			'method' => 'GET'
		));

		$this->assertEqual($paginate, $Controller->paginate);
		$this->assertSame(array('Monitoring pagination data'), $Controller->viewVars['data']);
	}

	/**
	 * Test save
	 * 
	 * @param array $query
	 * @param bool $saved
	 * @param string $message
	 * @dataProvider saveProvider
	 */
	public function testSave(array $query, $saved, $message) {
		$Controller = $this->generate('Monitoring.Monitoring', array(
			'models' => array(
				'Monitoring.Monitoring' => array('save')
			),
			'components' => array(
				'Session' => array('setFlash')
			),
			'methods' => array('redirect', 'referer')
		));

		$Controller->Monitoring
				->expects($this->once())
				->method('save')
				->with($query)
				->willReturn($saved);

		$Controller->Session
				->expects($this->once())
				->method('setFlash')
				->with($message);

		$Controller
				->expects($this->once())
				->method('referer')
				->willReturn('referer');
		$Controller
				->expects($this->once())
				->method('redirect')
				->with('referer');

		$this->testAction('/monitoring/monitoring/save/', array(
			'method' => 'POST',
			'data' => $query
		));
	}

	/**
	 * Data provider for testSave
	 * 
	 * @return array
	 */
	public function saveProvider() {
		return array(
			//set #0
			array(
				//query
				array(
					'id' => 1
				),
				//saved
				false,
				//message
				"Unable to saved monitoring."
			),
			//set #2
			array(
				//query
				array(
					'id' => 2
				),
				//saved
				true,
				//message
				"Monitoring saved."
			),
		);
	}

	/**
	 * Test edit
	 * 
	 * @param int $id
	 * @param array $checker
	 * @param string $class
	 * @param bool $isSMSEnabled
	 * @param string $exception
	 * @dataProvider editProvider
	 */
	public function testEdit($id, array $checker, $class, $isSMSEnabled, $exception) {
		if ($class) {
			$defaultSettings = array('default_key' => 'default_value');
			Configure::write("Monitoring.checkers.$class.defaults", $defaultSettings);
		}

		$Controller = $this->generate('Monitoring.Monitoring', array(
			'models' => array(
				'Monitoring.Monitoring' => array('read'),
				'Monitoring.MonitoringReport' => array('isSMSEnabled')
			)
		));

		$Controller->Monitoring
				->expects($this->once())
				->method('read')
				->with(null, $id)
				->willReturn($checker);

		if ($exception) {
			$this->expectException($exception);
		} else {
			$Controller->MonitoringReport
					->expects($this->once())
					->method('isSMSEnabled')
					->willReturn($isSMSEnabled);
		}

		$this->testAction('/monitoring/monitoring/edit/' . $id, array(
			'method' => 'POST'
		));

		$this->assertSame(Inflector::underscore($class), $Controller->viewVars['settingsView']);
		$this->assertSame($isSMSEnabled, $Controller->viewVars['isSMSEnabled']);

		$data = $checker;
		$data['Monitoring']['settings'] = (array)$data['Monitoring']['settings'] + (array)$defaultSettings;
		$this->assertSame($data, $Controller->request->data);
	}

	/**
	 * Data provider for testEdit
	 * 
	 * @return array
	 */
	public function editProvider() {
		return array(
			//set #0
			array(
				//id
				1,
				//checker
				array(),
				//class
				'',
				//isSMSEnabled
				true,
				//exception
				"NotFoundException"
			),
			//set #1
			array(
				//id
				2,
				//query
				array(
					'Monitoring' => array(
						'id' => 2,
						'class' => 'SomePlugin.SomeClass',
						'settings' => array()
					)
				),
				//class
				'SomeClass',
				//isSMSEnabled
				true,
				//exception
				null
			),
			//set #2
			array(
				//id
				2,
				//query
				array(
					'Monitoring' => array(
						'id' => 2,
						'class' => 'SomePlugin.SomeClass',
						'settings' => array(
							'key' => 'val'
						)
					)
				),
				//class
				'SomeClass',
				//isSMSEnabled
				false,
				//exception
				null
			),
			//set #2
			array(
				//id
				2,
				//query
				array(
					'Monitoring' => array(
						'id' => 2,
						'class' => 'SomeOtherClass',
						'settings' => array(
							'default_key' => 'not_default_value'
						)
					)
				),
				//class
				'SomeOtherClass',
				//isSMSEnabled
				false,
				//exception
				null
			),
		);
	}
	
	/**
	 * Test logs action
	 * 
	 * @param int $id
	 * @param array $checker
	 * @param string $exception
	 * @dataProvider logsProvider
	 */
	public function testLogs($id, array $checker, $exception) {
		$paginate = array(
			'MonitoringLog' => array(
				'limit' => Configure::read('Pagination.limit'),
				'order' => array('id' => 'desc'),
				'conditions' => array(
					'monitoring_id' => $id
				)
			)
		);

		$Controller = $this->generate('Monitoring.Monitoring', array(
			'models' => array(
				'Monitoring.Monitoring' => array('read'),
				'Monitoring.MonitoringReport' => array('isSMSEnabled')
			),
			'methods' => array(
				'paginate'
			)
		));
		
		$Controller->Monitoring
				->expects($this->once())
				->method('read')
				->with(null, $id)
				->willReturn($checker);

		if ($exception) {
			$this->expectException($exception);
		} else {
			$Controller->expects($this->once())->method('paginate')->with('MonitoringLog')->willReturn(array('MonitoringLog pagination data'));
		}

		$this->testAction('/monitoring/monitoring/logs/' . $id, array(
			'method' => 'GET'
		));

		$this->assertEqual($paginate, $Controller->paginate);
		$this->assertSame(array('MonitoringLog pagination data'), $Controller->viewVars['data']);
	}
	
	/**
	 * Data provider for testLogs
	 * 
	 * @return array
	 */
	public function logsProvider() {
		return array(
			//set #0
			array(
				//id
				1,
				//checker
				array(),
				//exception
				'NotFoundException'
			),
			//set #1
			array(
				//id
				2,
				//checker
				array(
					'Monitoring' => array(
						'id' => 2
					)
				),
				//exception
				''
			),
		);
	}

}
