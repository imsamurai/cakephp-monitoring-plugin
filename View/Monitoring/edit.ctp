<?php
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 09.07.2013
 * Time: 20:20:20
 * Format: http://book.cakephp.org/2.0/en/views.html
 *
 * @package Monitoring.View
 */
/* @var $this View */
?>
<h1>Edit <?= $this->Html->link('monitoring', array('action' => 'index')) . ' (' . $this->request->data('Monitoring.class') . ')'; ?></h1>
<hr>
<br>

<?php
echo $this->element('forms/monitoring_create');
