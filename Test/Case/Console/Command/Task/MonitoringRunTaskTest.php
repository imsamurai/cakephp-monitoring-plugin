<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 31.07.2014
 * Time: 16:50:33
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
declare(ticks = 1);

App::uses('MonitoringRunTask', 'Monitoring.Console/Command/Task');
App::uses('Monitoring', 'Monitoring.Model');
App::uses('MonitoringReport', 'Monitoring.Model');
App::uses('MonitoringChecker', 'Monitoring.Lib/Monitoring');
App::uses('ConsoleOutput', 'Console');

/**
 * MonitoringRunTaskTest
 * 
 * @property string $out Output
 * @property string $err Errors
 * @property ConsoleOutput $Output Standart output
 * @property ConsoleOutput $Error Errors output
 * 
 * @package MonitoringTest
 * @subpackage Console.Command.Task
 */
class MonitoringRunTaskTest extends CakeTestCase {

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
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		$this->skipUnless(php_sapi_name() === "cli", 'This tests available only for cli mode!');

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
	 * Test run
	 * 
	 * @param array $checkers
	 * @param array $checkResults
	 * @param string $errors
	 * @param string $output
	 * @param array $sendMail
	 * @param string $exception
	 * 
	 * @dataProvider runProvider
	 */
	public function testRun(array $checkers, array $checkResults, $errors, $output, array $sendMail, $exception) {
		$MonitoringReport = $this->getMockForModel('Monitoring.MonitoringReport', array('send'));
		if (!$exception) {
			$MonitoringReport->expects($this->exactly(count($checkers)))->method('send')->will($this->returnValueMap($sendMail));
		} else {
			$MonitoringReport->expects($this->exactly(count($checkers)))->method('send')->willThrowException(new Exception($exception));
		}

		$Monitoring = $this->getMockForModel('Monitoring.Monitoring', array(
			'getActiveCheckers',
			'saveCheckResults'
		));
		$Monitoring->expects($this->once())->method('getActiveCheckers')->will($this->returnValue($checkers));
		$Monitoring->expects($this->exactly(count($checkers)))->method('saveCheckResults')->will($this->returnValueMap($checkResults));

		$Runner = new MonitoringRunTask($this->Output, $this->Error);
		$Runner->Monitoring = $Monitoring;
		$Runner->MonitoringReport = $MonitoringReport;

		$Monitoring->getDataSource()->reconnect();
		$start = microtime(true);
		$Runner->execute();
		$runTime = microtime(true) - $start;

		//debug($this->out);
		//debug($this->err);
		$this->assertLessThan(20, $runTime);

		$this->assertStringMatchesFormat($errors, $this->err);
		$this->assertStringMatchesFormat($output, $this->out);
	}

	/**
	 * Data provider for testRun
	 * 
	 * @return array
	 */
	public function runProvider() {
		return array(
			//set #0
			array(
				//checkers
				array(
					array(
						'id' => 1,
						'timeout' => 2,
						'class' => 'MonitoringRunTaskTestCheckerTimeouted',
						'name' => 'MonitoringRunTaskTestCheckerTimeouted',
						'settings' => array()
					)
				),
				//checkResults
				array(
					array(1, 'OK', '', true)
				),
				//errors
				"%sError%s 'MonitoringRunTaskTestCheckerTimeouted'",
				//output
				"%sRun%s 'MonitoringRunTaskTestCheckerTimeouted'\n" .
				"%sSent mail%s for 'MonitoringRunTaskTestCheckerTimeouted'",
				//sendMail
				array(
					array(1, true)
				),
				//exception
				null
			),
			//set #1
			array(
				//checkers
				array(
					array(
						'id' => 1,
						'timeout' => 2,
						'class' => 'MonitoringRunTaskTestCheckerTrue',
						'name' => 'MonitoringRunTaskTestCheckerTrue',
						'settings' => array()
					),
					array(
						'id' => 2,
						'timeout' => 2,
						'class' => 'MonitoringRunTaskTestCheckerFalse',
						'name' => 'MonitoringRunTaskTestCheckerFalse',
						'settings' => array()
					)
				),
				//checkResults
				array(
					array(1, 'OK', '', true),
					array(2, 'FAIL', 'my fault!', false),
				),
				//errors
				"%sError%s 'MonitoringRunTaskTestCheckerFalse'",
				//output
				"%sRun%s 'MonitoringRunTaskTestCheckerTrue'" .
				"\n" .
				"%sOK%s 'MonitoringRunTaskTestCheckerTrue'" .
				"\n" .
				"%sMail not sent%s for 'MonitoringRunTaskTestCheckerTrue'" .
				"\n" .
				"%sRun%s 'MonitoringRunTaskTestCheckerFalse'" .
				"\n" .
				"%sSent mail%s for 'MonitoringRunTaskTestCheckerFalse'",
				//sendMail
				array(
					array(1, null),
					array(2, true),
				),
				//exception
				null
			),
			//set #2
			array(
				//checkers
				array(
					array(
						'id' => 2,
						'timeout' => 2,
						'class' => 'MonitoringRunTaskTestCheckerFalse',
						'name' => 'MonitoringRunTaskTestCheckerFalse',
						'settings' => array()
					)
				),
				//checkResults
				array(
					array(2, 'FAIL', 'my fault!', false),
				),
				//errors
				"%sError%s 'MonitoringRunTaskTestCheckerFalse'" .
				"\n" .
				"%sFail to sent mail%s 'MonitoringRunTaskTestCheckerFalse'",
				//output
				"%sRun%s 'MonitoringRunTaskTestCheckerFalse'",
				//sendMail
				array(
					array(2, false),
				),
				//exception
				null
			),
			//set #3
			array(
				//checkers
				array(
					array(
						'id' => 2,
						'timeout' => 2,
						'class' => 'MonitoringRunTaskTestCheckerFalse',
						'name' => 'MonitoringRunTaskTestCheckerFalse',
						'settings' => array()
					)
				),
				//checkResults
				array(
					array(2, 'FAIL', 'my fault!', false),
				),
				//errors
				"%sError%s 'MonitoringRunTaskTestCheckerFalse'" .
				"\n" .
				"%ssome exception message%s" .
				"\n" .
				"%sFail to sent mail%s 'MonitoringRunTaskTestCheckerFalse'",
				//output
				"%sRun%s 'MonitoringRunTaskTestCheckerFalse'",
				//sendMail
				array(
					array(2, false),
				),
				//exception
				'some exception message'
			),
		);
	}

	/**
	 * Test option parser
	 */
	public function testGetOptionParser() {
		$Runner = new MonitoringRunTask($this->Output, $this->Error);
		$OptonParser = $Runner->getOptionParser();
		$this->assertSame('Runs active checkers', $OptonParser->description());
	}

}

/**
 * MonitoringRunTaskTestCheckerTimeouted
 * 
 * @package MonitoringTest
 * @subpackage Console.Command.Task
 */
class MonitoringRunTaskTestCheckerTimeouted extends MonitoringChecker {

	/**
	 * {@inheritdoc}
	 */
	public function check() {
		while (true) {
			
		}
		return true;
	}

}

/**
 * MonitoringRunTaskTestCheckerTrue
 * 
 * @package MonitoringTest
 * @subpackage Console.Command.Task
 */
class MonitoringRunTaskTestCheckerTrue extends MonitoringChecker {

	/**
	 * {@inheritdoc}
	 */
	public function check() {
		return true;
	}

}

/**
 * MonitoringRunTaskTestCheckerFalse
 * 
 * @package MonitoringTest
 * @subpackage Console.Command.Task
 */
class MonitoringRunTaskTestCheckerFalse extends MonitoringChecker {

	/**
	 * {@inheritdoc}
	 */
	public function check() {
		$this->addError('my fault!');
		return false;
	}

}
