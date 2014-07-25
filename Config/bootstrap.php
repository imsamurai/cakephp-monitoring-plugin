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
			'checkersPath' => 'Lib/Monitoring',
			'defaults' => array(
				'cron' => '*/5 * * * *',
				'timeout' => 600,
				'active' => false,
				'priority' => 0
			),
			'email' => array(
				'enabled' => true,
				'config' => 'default',
				'subject' => 'Monitoring alert caused by %s!'
			),
			'checkers' => array(
				'MonitoringSelfFailCheck' => array(
					'defaults' => array(
						'errorText' => 'MonitoringSelfFailCheck error text'
					)
				)
			)
		));
Configure::write('Monitoring', $config);
