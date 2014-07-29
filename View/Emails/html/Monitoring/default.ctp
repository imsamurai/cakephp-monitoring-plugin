<?php
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 09.07.2013
 * Time: 15:50:45
 * Format: http://book.cakephp.org/2.0/en/views.html
 *
 * @package Monitoring.View
 */
App::uses('Sanitize', 'Utility');

/* @var $this View */
?>
<h1>Error report</h1>
<strong>Check:</strong> <?= $checker['name']; ?><br />
<strong>Result:</strong> <?= $checker['last_code_string']; ?><br />
<strong>Description:</strong> <?= $this->Text->autoLink($checker['description'], array('target' => '_blank')); ?><br />
<strong>Checked:</strong> <?= $checker['last_check']; ?><br />
<strong>Next check:</strong> <?= $checker['next_check']; ?><br />
<strong>Errors:</strong><pre><?= $logs[0]['error']; ?></pre><br />
<!-- default report !-->