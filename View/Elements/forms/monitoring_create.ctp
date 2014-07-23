<?
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.07.2013
 * Time: 12:55:28
 * Format: http://book.cakephp.org/2.0/en/views.html
 *
 */
/* @var $this IDEView */
?>
<?= $this->Form->create('Monitoring', array('action' => 'create')); ?>
<fieldset>
	<div style="display: none;">
		<?= $this->Form->input('id', array('type' => 'hidden')); ?>
	</div>
	<?= $this->Form->input('name'); ?>
	<?= $this->Form->input('description', array('type' => 'textarea', 'style' => 'width:600px;height:300px;')); ?>
	<?= $this->Form->input('cron', array('default' => '*/5 * * * *', 'help' => 'scheduler (cron syntax)')); ?>
	<?= $this->Form->input('timeout', array('type' => 'number', 'default' => 600, 'help' => 'maximum waiting time for check in seconds')); ?>
	<?= $this->Form->input('active', array('type' => 'checkbox', 'default' => false)); ?>
	<?= $this->Form->input('priority', array('type' => 'number', 'default' => 0, 'help' => 'zero means highest')); ?>
	<?= $this->Form->input('emails', array('type' => 'textarea', 'help' => 'coma-separated email list that will be used for send messages in case of failure', 'style' => 'width:600px;height:100px;')); ?>

	<div class="form-actions" style="text-align:center;">
		<?= $this->Form->submit('Save', array('class' => 'btn btn-primary', 'div' => false)); ?>
	</div>
</fieldset>
<?= $this->Form->end(); ?>
