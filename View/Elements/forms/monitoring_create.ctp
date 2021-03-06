<?php
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.07.2013
 * Time: 12:55:28
 * Format: http://book.cakephp.org/2.0/en/views.html
 * 
 * @package Monitoring.View
 */
$modelName = 'Monitoring';
echo $this->Form->create($modelName, array(
	'type' => 'post',
	'url' => array(
		'action' => 'save',
		'controller' => 'monitoring'
	)
));
?>
<fieldset>
	<div style="display: none;">
		<?= $this->Form->input('id', array('type' => 'hidden')); ?>
	</div>
	<?= $this->Form->input('name'); ?>
	<?= $this->Form->input('description', array('type' => 'textarea', 'style' => 'width:600px;height:300px;')); ?>
	<?= $this->Form->input('cron', array('help' => 'scheduler (cron syntax)')); ?>
	<?= $this->Form->input('timeout', array('type' => 'number', 'help' => 'maximum waiting time for check in seconds')); ?>
	<?= $this->Form->input('active', array('type' => 'checkbox')); ?>
	<?= $this->Form->input('priority', array('type' => 'number', 'help' => 'zero means highest')); ?>
	<?= $this->Form->input('emails', array('type' => 'textarea', 'help' => 'coma-separated email list that will be used for sending messages', 'style' => 'width:600px;height:100px;')); ?>
	<?php
	if ($isSMSEnabled) {
		echo $this->Form->input('sms', array('type' => 'textarea', 'help' => 'coma-separated phome list that will be used for sending messages', 'style' => 'width:600px;height:100px;'));
	}
	?>
	<?php
	$settingsFields = $this->element("Monitoring/$settingsView", compact('modelName'), array('ignoreMissing' => true));
	if ($settingsFields) {
		echo $this->Html->tag('h3', 'Settings') . $settingsFields;
	}
	?>
	<div class="form-actions" style="text-align:center;">
		<?= $this->Form->submit('Save', array('class' => 'btn btn-primary', 'div' => false)); ?>
	</div>
</fieldset>
<?php
echo $this->Form->end();