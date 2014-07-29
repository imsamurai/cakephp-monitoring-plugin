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
<h1>Monitoring List</h1>
<?php
if (empty($data)) {
	echo $this->element('basics/no_data');
	return;
}
echo $this->element('pagination/pagination');
?>
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th><?= $this->Paginator->sort('name'); ?></th>
			<th><?= $this->Paginator->sort('last_code_string', 'Result'); ?></th>
			<th><?= $this->Paginator->sort('last_check'); ?></th>
			<th><?= $this->Paginator->sort('next_check'); ?></th>
			<th><?= $this->Paginator->sort('cron'); ?></th>
			<th><?= $this->Paginator->sort('active'); ?></th>
			<th><?= $this->Paginator->sort('created'); ?></th>
			<th><?= $this->Paginator->sort('modified'); ?></th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($data as $one) {
			?>
			<tr>
				<td title="<?= 'class: ' . $one['Monitoring']['class']; ?>">
					<?= $this->Html->link($one['Monitoring']['name'], array('controller' => 'monitoring', 'action' => 'edit', $one['Monitoring']['id'])); ?>
				</td>
				<td><?= $this->Html->tag('span', $one['Monitoring']['last_code_string'], array('class' => 'label label-' . ($one['Monitoring']['last_code_string'] == 'OK' ? 'success' : 'important'))); ?></td>
				<td>
					<?php
					$lastCheck = $one['Monitoring']['last_check'] != '0000-00-00 00:00:00' ? $one['Monitoring']['last_check'] : null;
					if ($lastCheck) {
						echo $this->Html->tag('span', $this->Time->timeAgoInWords($lastCheck, array(
									'format' => Configure::read('Monitoring.dateFormat'
							))), array(
							'title' => $lastCheck
						));
					} else {
						echo 'none';
					}
					?>
				</td>
				<td>
					<?=
					$this->Html->tag('span', $this->Time->timeAgoInWords($one['Monitoring']['next_check'], array(
								'format' => Configure::read('Monitoring.dateFormat'
						))), array(
						'title' => $one['Monitoring']['next_check']
					));
					?>
				</td>
				<td><?= $one['Monitoring']['cron']; ?></td>
				<td><?= $one['Monitoring']['active'] ? 'yes' : 'no'; ?></td>
				<td>
					<?=
					$this->Html->tag('span', $this->Time->timeAgoInWords($one['Monitoring']['created'], array(
								'format' => Configure::read('Monitoring.dateFormat'
						))), array(
						'title' => $one['Monitoring']['created']
					));
					?>
				</td>
				<td>
					<?=
					$this->Html->tag('span', $this->Time->timeAgoInWords($one['Monitoring']['modified'], array(
								'format' => Configure::read('Monitoring.dateFormat'
						))), array(
						'title' => $one['Monitoring']['modified']
					));
					?>
				</td>
				<td>
					<?= $this->Html->link('Edit', array('controller' => 'monitoring', 'action' => 'edit', $one['Monitoring']['id']), array('class' => 'btn btn-mini')); ?>
					<?= $this->Html->link('Logs', array('controller' => 'monitoring', 'action' => 'logs', $one['Monitoring']['id']), array('class' => 'btn btn-mini')); ?>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
<?=
$this->element('pagination/pagination');
