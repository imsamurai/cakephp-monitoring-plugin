<?
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 09.07.2013
 * Time: 20:20:20
 * Format: http://book.cakephp.org/2.0/en/views.html
 *
 */
/* @var $this View */
?>
<h1>Add <?= $this->Html->link('monitoring', array('action' => 'index')); ?></h1>
<hr>
<br>

<?= $this->element('forms/monitoring_create'); ?>