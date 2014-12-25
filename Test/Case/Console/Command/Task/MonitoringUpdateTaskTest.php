<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 31.07.2014
 * Time: 16:50:33
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
declare(ticks = 1);

App::uses('MonitoringUpdateTask', 'Monitoring.Console/Command/Task');
App::uses('Monitoring', 'Monitoring.Model');
App::uses('ConsoleOutput', 'Console');

/**
 * MonitoringUpdateTaskTest
 * 
 * @property string $out Output
 * @property string $err Errors
 * @property ConsoleOutput $Output Standart output
 * @property ConsoleOutput $Error Errors output
 * 
 * @package MonitoringTest
 * @subpackage Console.Command.Task
 */
class MonitoringUpdateTaskTest extends CakeTestCase {

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		$this->out = '';
		$this->err = '';
		$this->Output = $this->getMock('ConsoleOutput', array(
			'_write'
		));
		$this->Output->expects($this->any())
				->method('_write')
				->will($this->returnCallback(
								function($out) {
									$this->out .= $out;
								})
		);
		$this->Error = $this->getMock('ConsoleOutput', array(
			'_write'
		));
		$this->Error->expects($this->any())
				->method('_write')
				->will($this->returnCallback(
								function($out) {
									$this->err .= $out;
								})
		);
	}

	/**
	 * Test update
	 * 
	 * @param array $checkersInDB
	 * @param array $absentCheckers
	 * @param bool $absentCheckersSuccess
	 * @param array $checkers
	 * @param array $successes
	 * @param string $errors
	 * @param string $output
	 * 
	 * @dataProvider updateProvider
	 */
	public function testUpdate(array $checkersInDB, array $absentCheckers, $absentCheckersSuccess, array $checkers, array $successes, $errors, $output) {
		$Monitoring = $this->getMock('Object', array(
			'findNewCheckers',
			'findAllCheckerClasses',
			'add',
			'find',
			'deleteAll'
		));
		$Monitoring->expects($this->once())->method('findAllCheckerClasses')->willReturn($checkers);
		$Monitoring->expects($this->once())->method('findNewCheckers')->willReturn($checkers);
		$Monitoring->expects($this->once())
				->method('find')
				->with('list', array('fields' => array('class')))
				->willReturn($checkersInDB);
		$Monitoring->expects($this->exactly((int)!empty($absentCheckers)))
				->method('deleteAll')
				->with(array('class' => $absentCheckers), true, true)
				->willReturn($absentCheckersSuccess);

		$Monitoring->expects($this->exactly(count($checkers)))->method('add')->willReturnMap($successes);

		$Runner = new MonitoringUpdateTask($this->Output, $this->Error);
		$Runner->Monitoring = $Monitoring;
		$Runner->execute();

		$this->assertStringMatchesFormat($errors, $this->err);
		$this->assertStringMatchesFormat($output, $this->out);
	}

	/**
	 * Data provider for testUpdate
	 * 
	 * @return array
	 */
	public function updateProvider() {
		return array(
			//set #0
			array(
				//checkersInDB
				array(),
				//absentCheckers
				array(),
				//absentCheckersSuccess
				null,
				//checkers
				array(),
				//successes
				array(),
				//errors
				'',
				//output
				''
			),
			//set #1
			array(
				//checkersInDB
				array(),
				//absentCheckers
				array(),
				//absentCheckersSuccess
				null,
				//checkers
				array('c1', 'c2', 'c3', 'c4', 'c5'),
				//successes
				array(
					array('c1', true),
					array('c2', false),
					array('c3', true),
					array('c4', false),
					array('c5', false),
				),
				//errors
				"%SNot added%S c2\n%SNot added%S c4\n%SNot added%S c5\n",
				//output
				"%SAdded%S c1\n%SAdded%S c3\n"
			),
			//set #2
			array(
				//checkersInDB
				array('c1', 'c2', 'c3', 'c4', 'c5'),
				//absentCheckers
				array('c1', 'c2', 'c3', 'c4', 'c5'),
				//absentCheckersSuccess
				true,
				//checkers
				array(),
				//successes
				array(),
				//errors
				'',
				//output
				'%SRemoved absent checkers (c1, c2, c3, c4, c5)%S'
			),
			//set #3
			array(
				//checkersInDB
				array('c1', 'c2', 'c3', 'c4', 'c5'),
				//absentCheckers
				array('c1', 'c2', 'c3', 'c4', 'c5'),
				//absentCheckersSuccess
				false,
				//checkers
				array(),
				//successes
				array(),
				//errors
				'%SCan\'t remove absent checkers%S',
				//output
				''
			),
			//set #4
			array(
				//checkersInDB
				array('c6'),
				//absentCheckers
				array('c6'),
				//absentCheckersSuccess
				true,
				//checkers
				array('c1', 'c2', 'c3', 'c4', 'c5'),
				//successes
				array(
					array('c1', true),
					array('c2', false),
					array('c3', true),
					array('c4', false),
					array('c5', false),
				),
				//errors
				"%SNot added%S c2\n%SNot added%S c4\n%SNot added%S c5\n",
				//output
				"%SAdded%S c1\n%SAdded%S c3\n%SRemoved absent checkers (c6)%S"
			),
			//set #5
			array(
				//checkersInDB
				array('c6'),
				//absentCheckers
				array('c6'),
				//absentCheckersSuccess
				false,
				//checkers
				array('c1', 'c2', 'c3', 'c4', 'c5'),
				//successes
				array(
					array('c1', true),
					array('c2', false),
					array('c3', true),
					array('c4', false),
					array('c5', false),
				),
				//errors
				"%SNot added%S c2\n%SNot added%S c4\n%SNot added%S c5\n%SCan't remove absent checkers%S\n",
				//output
				"%SAdded%S c1\n%SAdded%S c3\n"
			),
		);
	}

	/**
	 * Test option parser
	 */
	public function testGetOptionParser() {
		$Runner = new MonitoringUpdateTask($this->Output, $this->Error);
		$OptonParser = $Runner->getOptionParser();
		$this->assertSame('Update all new checkers', $OptonParser->description());
	}

}
	