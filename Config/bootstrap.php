<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 23.07.2014
 * Time: 16:42:21
 * Format: http://book.cakephp.org/2.0/en/views.html
 */

Configure::write('Pagination.pages', Configure::read('Pagination.pages') ? Configure::read('Pagination.pages') : 10);
$config = (array)Configure::read('Monitoring');
$config += array(
	'dateFormat' => 'H:i:s d.m.Y',
	'checkersPath' => 'Lib/Monitoring',
	'defaults' => array(
		'cron' => '*/5 * * * *',
		'timeout' => 600,
		'active' => false,
		'priority' => 0
	)
);
Configure::write('Monitoring', $config);
