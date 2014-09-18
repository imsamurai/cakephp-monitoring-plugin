<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 28.07.2014
 * Time: 16:41:48
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
App::uses('AbstractTransport', 'Network/Email');
App::uses('MailTransport', 'Network/Email');
App::uses('CakeEmail', 'Network/Email');
App::uses('MonitoringChecker', 'Monitoring.Lib/Monitoring');

/**
 * MonitoringReportTest
 * 
 * @property Monitoring $Monitoring Monitoring model
 * @property MonitoringReport $Report Monitoring Report model
 * 
 * @package MonitoringTest
 * @subpackage Model
 */
class MonitoringReportTest extends CakeTestCase {

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
		$this->Report = ClassRegistry::init('Monitoring.MonitoringReport');
		Configure::write('Monitoring', array(
			'dateFormat' => 'H:i:s d.m.Y',
			'dbDateFormat' => 'Y-m-d H:i:s',
			'checkersPath' => 'Lib/Monitoring',
			'defaults' => array(
				'cron' => '*/5 * * * *',
				'timeout' => 600,
				'active' => false,
				'priority' => 0
			),
			'email' => array(
				'enabled' => array(
					'fail' => true,
					'stillFail' => true,
					'success' => true,
					'backToNormal' => true,
				),
				'config' => 'default',
				'subject' => array(
					'fail' => 'Monitoring: %s is fail!',
					'stillFail' => 'Monitoring: %s still failing!',
					'success' => 'Monitoring: %s is ok!',
					'backToNormal' => function($checker) {
						return "Monitoring: {$checker['name']} back to normal!";
					},
				),
			),
			'views' => array(
				'pluginFirst' => true
			)
		));
	}

	/**
	 * Test case when report isn't sent
	 * 
	 * @param array $checker
	 * @param array $logs
	 * @param array $buildConfig
	 * 
	 * @dataProvider noSendProvider
	 */
	public function testNoSend(array $checker, array $logs, array $buildConfig) {
		Configure::write('Monitoring.email', $buildConfig);
		$this->Monitoring->add($checker['class'], $checker);
		foreach ($logs as $log) {
			$this->Monitoring->saveCheckResults($checker['id'], $log['code'], $log['error']);
		}
		
		$Report = $this->getMock('MonitoringReport', array(
			'_getMailer'
		));

		$Report
				->expects($this->never())
				->method('_getMailer');
		$success = $Report->send($checker['id']);
		$this->assertNull($success);
	}
	
	/**
	 * Data provider for testNoSend
	 * 
	 * @return array
	 */
	public function noSendProvider() {
		return array(
			//set #0
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => true					
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_OK,
						'error' => ''
					)
				),
				//buildConfig
				array(
					'enabled' => array(
						'fail' => true,
						'stillFail' => true,
						'success' => false,
						'backToNormal' => true,
					)
				)
			),
			//set #1
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => true			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					)
				),
				//buildConfig
				array(
					'enabled' => array(
						'fail' => false,
						'stillFail' => true,
						'success' => true,
						'backToNormal' => true,
					)
				)
			),
			//set #2
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => true			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					),
					array(
						'code' => MonitoringChecker::STATUS_OK,
						'error' => ''
					)
				),
				//buildConfig
				array(
					'enabled' => array(
						'fail' => true,
						'stillFail' => true,
						'success' => true,
						'backToNormal' => false,
					)
				)
			),
			//set #3
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => true			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					),
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'errrrrrrrrrr'
					)
				),
				//buildConfig
				array(
					'enabled' => array(
						'fail' => true,
						'stillFail' => false,
						'success' => true,
						'backToNormal' => true,
					)
				)
			),
			//set #4
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => false					
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_OK,
						'error' => ''
					)
				),
				//buildConfig
				array(
					'enabled' => array(
						'fail' => true,
						'stillFail' => true,
						'success' => true,
						'backToNormal' => true,
					)
				)
			),
			//set #5
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => false			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					)
				),
				//buildConfig
				array(
					'enabled' => array(
						'fail' => true,
						'stillFail' => true,
						'success' => true,
						'backToNormal' => true,
					)
				)
			),
			//set #6
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => false			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					),
					array(
						'code' => MonitoringChecker::STATUS_OK,
						'error' => ''
					)
				),
				//buildConfig
				array(
					'enabled' => array(
						'fail' => true,
						'stillFail' => true,
						'success' => true,
						'backToNormal' => true,
					)
				)
			),
			//set #7
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => false			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					),
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'errrrrrrrrrr'
					)
				),
				//buildConfig
				array(
					'enabled' => array(
						'fail' => true,
						'stillFail' => true,
						'success' => true,
						'backToNormal' => true,
					)
				)
			),
			//set #8
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => ' ',
					'active' => true			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					),
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'errrrrrrrrrr'
					)
				),
				//buildConfig
				array(
					'enabled' => array(
						'fail' => true,
						'stillFail' => true,
						'success' => true,
						'backToNormal' => true,
					)
				)
			),
			//set #9
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => ' ',
					'active' => true			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					),
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'errrrrrrrrrr'
					)
				),
				//buildConfig
				array(
					'enabled' => true
				)
			),
			//set #10
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => true			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					),
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'errrrrrrrrrr'
					)
				),
				//buildConfig
				array(
					'enabled' => false
				)
			),
		);
	}

	/**
	 * Test send report
	 * 
	 * @param array $checker
	 * @param array $logs
	 * @param string $subject
	 * @param array $messagePattern
	 * 
	 * @dataProvider sendProvider
	 */
	public function testSend(array $checker, array $logs, $subject, array $messagePattern) {
		$this->Monitoring->add($checker['class'], $checker);
		foreach ($logs as $log) {
			$this->Monitoring->saveCheckResults($checker['id'], $log['code'], $log['error']);
		}

		$MailTransport = $this->getMock('MailTransport', array(
			'send'
		));
		$MailTransport
				->expects($this->once())
				->method('send')
				->will(
						$this->returnCallback(function (CakeEmail $email) use($checker, $subject, $messagePattern) {
							$this->assertSame(explode(',', $checker['emails']), array_values($email->to()));
							$this->assertSame(is_callable($subject) ? $subject($checker) : $subject, $email->subject());
							//debug(array_map('trim', $email->message()));
							$this->assertStringMatchesFormat(implode('', $messagePattern), implode('', array_map('trim', $email->message())));

							return true;
						})
		);

		$Mailer = $this->getMock('CakeEmail', array(
			'transportClass'
		));

		$Mailer
				->expects($this->atLeastOnce())
				->method('transportClass')
				->will($this->returnValue($MailTransport));

		$Report = $this->getMock('MonitoringReport', array(
			'_getMailer'
		));

		$Report
				->expects($this->once())
				->method('_getMailer')
				->will($this->returnValue($Mailer));
		$success = $Report->send($checker['id']);
		$this->assertTrue($success);
	}

	/**
	 * Data provider for testSend
	 * 
	 * @return array
	 */
	public function sendProvider() {
		return array(
			//set #0
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => true					
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_OK,
						'error' => ''
					)
				),
				//subject
				'Monitoring: checker is ok!',
				//messagePattern
				array(
					(int)0 => '<!DOCTYPE html>',
					(int)1 => '<html>',
					(int)2 => '<head>',
					(int)3 => '<title>Monitoring: checker is ok!</title>',
					(int)4 => '</head>',
					(int)5 => '<body>',
					(int)6 => '<h1>Error report</h1>',
					(int)7 => '<strong>Check:</strong> checker<br />',
					(int)8 => '<strong>Result:</strong> OK<br />',
					(int)9 => '<strong>Description:</strong> <br />',
					(int)10 => '<strong>Checked:</strong> %s<br />',
					(int)11 => '<strong>Next check:</strong> %s<br />',
					(int)12 => '<strong>Errors:</strong><pre></pre><br />',
					(int)13 => '<!-- default report !-->',
					(int)14 => '%s',
					(int)15 => '</body>',
					(int)16 => '</html>',
					(int)17 => '<!-- default report layout !-->'
				)
			),
			//set #1
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => true			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					)
				),
				//subject
				'Monitoring: checker is fail!',
				//messagePattern
				array(
					(int)0 => '<!DOCTYPE html>',
					(int)1 => '<html>',
					(int)2 => '<head>',
					(int)3 => '<title>Monitoring: checker is fail!</title>',
					(int)4 => '</head>',
					(int)5 => '<body>',
					(int)6 => '<h1>Error report</h1>',
					(int)7 => '<strong>Check:</strong> checker<br />',
					(int)8 => '<strong>Result:</strong> FAIL<br />',
					(int)9 => '<strong>Description:</strong> <br />',
					(int)10 => '<strong>Checked:</strong> %s<br />',
					(int)11 => '<strong>Next check:</strong> %s<br />',
					(int)12 => '<strong>Errors:</strong><pre>blah blah</pre><br />',
					(int)13 => '<!-- default report !-->',
					(int)14 => '%s',
					(int)15 => '</body>',
					(int)16 => '</html>',
					(int)17 => '<!-- default report layout !-->'
				)
			),
			//set #2
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => true			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					),
					array(
						'code' => MonitoringChecker::STATUS_OK,
						'error' => ''
					)
				),
				//subject
				'Monitoring: checker back to normal!',
				//messagePattern
				array(
					(int)0 => '<!DOCTYPE html>',
					(int)1 => '<html>',
					(int)2 => '<head>',
					(int)3 => '<title>Monitoring: checker back to normal!</title>',
					(int)4 => '</head>',
					(int)5 => '<body>',
					(int)6 => '<h1>Error report</h1>',
					(int)7 => '<strong>Check:</strong> checker<br />',
					(int)8 => '<strong>Result:</strong> OK<br />',
					(int)9 => '<strong>Description:</strong> <br />',
					(int)10 => '<strong>Checked:</strong> %s<br />',
					(int)11 => '<strong>Next check:</strong> %s<br />',
					(int)12 => '<strong>Errors:</strong><pre></pre><br />',
					(int)13 => '<!-- default report !-->',
					(int)14 => '%s',
					(int)15 => '</body>',
					(int)16 => '</html>',
					(int)17 => '<!-- default report layout !-->'
				)
			),
			//set #3
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'checker',
					'emails' => 'im.samuray@gmail.com',
					'active' => true			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					),
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'errrrrrrrrrr'
					)
				),
				//subject
				'Monitoring: checker still failing!',
				//messagePattern
				array(
					(int)0 => '<!DOCTYPE html>',
					(int)1 => '<html>',
					(int)2 => '<head>',
					(int)3 => '<title>Monitoring: checker still failing!</title>',
					(int)4 => '</head>',
					(int)5 => '<body>',
					(int)6 => '<h1>Error report</h1>',
					(int)7 => '<strong>Check:</strong> checker<br />',
					(int)8 => '<strong>Result:</strong> FAIL<br />',
					(int)9 => '<strong>Description:</strong> <br />',
					(int)10 => '<strong>Checked:</strong> %s<br />',
					(int)11 => '<strong>Next check:</strong> %s<br />',
					(int)12 => '<strong>Errors:</strong><pre>errrrrrrrrrr</pre><br />',
					(int)13 => '<!-- default report !-->',
					(int)14 => '%s',
					(int)15 => '</body>',
					(int)16 => '</html>',
					(int)17 => '<!-- default report layout !-->'
				)
			),
			//set #4
			array(
				//checker
				array(
					'id' => 1,
					'class' => 'Monitoring.MonitoringSelfCheck',
					'emails' => 'im.samuray@gmail.com',
					'active' => true			
				),
				//logs
				array(
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'blah blah'
					),
					array(
						'code' => MonitoringChecker::STATUS_FAIL,
						'error' => 'errrrrrrrrrr'
					)
				),
				//subject
				'Monitoring: Monitoring.MonitoringSelfCheck still failing!',
				//messagePattern
				array(
					(int)0 => '<!DOCTYPE html>',
					(int)1 => '<html>',
					(int)2 => '<head>',
					(int)3 => '<title>Monitoring: Monitoring.MonitoringSelfCheck still failing!</title>',
					(int)4 => '</head>',
					(int)5 => '<body>',
					(int)6 => '<h1>Error report</h1>',
					(int)7 => '<strong>Check:</strong> Monitoring.MonitoringSelfCheck<br />',
					(int)8 => '<strong>Result:</strong> FAIL<br />',
					(int)9 => '<strong>Description:</strong> <br />',
					(int)10 => '<strong>Checked:</strong> %s<br />',
					(int)11 => '<strong>Next check:</strong> %s<br />',
					(int)12 => '<strong>Errors:</strong><pre>errrrrrrrrrr</pre><br />',
					(int)13 => '<!-- default report !-->',
					(int)14 => '<!-- self check report !-->',
					(int)15 => '%s',
					(int)16 => '</body>',
					(int)17 => '</html>',
					(int)18 => '<!-- default report layout !-->'
				)
			),
		);
	}
	
	/**
	 * Test case if checker not Z
	 */
	public function testNoChecker() {
		$this->expectException('Exception');
		$this->Report->send(-1);
	}

}
