<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 23.07.2014
 * Time: 16:42:21
 * Format: http://book.cakephp.org/2.0/en/views.html
 */
Configure::write('Pagination.pages', Configure::read('Pagination.pages') ? Configure::read('Pagination.pages') : 10);
$config = Hash::mergeDiff((array)Configure::read('Monitoring'), array(
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
			),
			'sms' => array(
				'enabled' => array(
					'fail' => true,
					'stillFail' => false,
					'success' => false,
					'backToNormal' => false,
				),
				'subject' => array(
					'fail' => 'Monitoring: %s is fail!',
					'stillFail' => 'Monitoring: %s still failing!',
					'success' => 'Monitoring: %s is ok!',
					'backToNormal' => 'Monitoring: %s back to normal!',
				),
				//you must setup valid source
				'source' => null,
				'desc' => 'Monitoring'
			)
		));
Configure::write('Monitoring', $config);
