<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 23.07.2014
 * Time: 16:42:21
 * Format: http://book.cakephp.org/2.0/en/views.html
 * 
 */
/* @var $this View */

Configure::write('Pagination.pages', Configure::read('Pagination.pages') ? Configure::read('Pagination.pages') : 10);
$config = (array)Configure::read('Monitoring');
$config += array(
	'dateFormat' => 'H:i:s d.m.Y',
);
Configure::write('Monitoring', $config);
