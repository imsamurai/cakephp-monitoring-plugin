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
<h1>Monitoring List</h1>
<?php
if (empty($data)) {
	echo $this->element('basics/no_data');
	return;
}
echo $this->element('pagination');
?>
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>Id</th>
			<th>Name</th>
			<th>LastCode</th>
			<th>LastCheck</th>
			<th>NextCheck</th>
			<th>Cron</th>
			<th>Active</th>
			<th>Created</th>
			<th>Modified</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($data as $one) {
			?>
			<tr>
				<td><?= $one['Monitoring']['id']; ?></td>
				<td><?= $one['Monitoring']['name']; ?></td>
				<td><?= $this->Html->tag('span', $one['Monitoring']['last_code_string'], array('class' => 'label label-' . ($one['Monitoring']['last_code_string'] == 'OK' ? 'success' : 'important'))); ?></td>
				<td><?= $one['Monitoring']['last_check'] != '0000-00-00 00:00:00' ? $this->Time->format(Configure::read('Monitoring.dateFormat'), $one['Monitoring']['last_check']) : 'none'; ?></td>
				<td><?= $this->Time->format(Configure::read('Monitoring.dateFormat'), $one['Monitoring']['next_check']); ?></td>
				<td><?= $one['Monitoring']['cron']; ?></td>
				<td><?= $one['Monitoring']['active'] ? 'yes' : 'no'; ?></td>
				<td><?= $this->Time->format(Configure::read('Monitoring.dateFormat'), $one['Monitoring']['created']); ?></td>
				<td><?= $this->Time->format(Configure::read('Monitoring.dateFormat'), $one['Monitoring']['modified']); ?></td>
				<td>
					<?= $this->Html->link('Edit', array('controller' => 'monitorings', 'action' => 'edit', $one['Monitoring']['id']), array('class' => 'btn btn-mini')); ?>
					<?= $this->Html->link('Logs', array('controller' => 'monitorings', 'action' => 'logs', $one['Monitoring']['id']), array('class' => 'btn btn-mini')); ?>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
<?= $this->element('pagination'); ?>