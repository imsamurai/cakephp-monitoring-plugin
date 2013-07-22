<?
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 09.07.2013
 * Time: 15:50:45
 * Format: http://book.cakephp.org/2.0/en/views.html
 *
 */
App::uses('Sanitize', 'Utility');

/* @var $this IDEView */
?>
<h1>Error report</h1>
<strong>Check:</strong> <?= $checker['Monitoring']['name']; ?><br />
<strong>Result:</strong> <?= $checker['Monitoring']['last_code_string']; ?><br />
<strong>Description:</strong> <?= $this->Text->autoLink($checker['Monitoring']['description'], array('target' => '_blank')); ?><br />
<strong>Checked:</strong> <?= $checker['Monitoring']['last_check']; ?><br />
<strong>Next check:</strong> <?= $checker['Monitoring']['next_check']; ?><br />
<strong>Errors:</strong><pre><?= Sanitize::html($checker['MonitoringLog'][0]['stderr']); ?></pre><br />
<strong>Output:</strong><pre><?= Sanitize::html($checker['MonitoringLog'][0]['stdout']); ?></pre><br />