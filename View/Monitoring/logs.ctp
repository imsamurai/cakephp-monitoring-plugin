<?php
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.07.2013
 * Time: 13:24:14
 * Format: http://book.cakephp.org/2.0/en/views.html
 *
 * @package Monitoring.View
 */
/* @var $this View */
?>
<h1><?= $this->Html->link('Monitoring', array('action' => 'index')) . ' logs for "' . $this->Html->link($Monitoring['name'], array('action' => 'edit', $Monitoring['id'])) . '"'; ?></h1>
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
			<th>Date</th>
			<th>Code</th>
			<th>Error</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($data as $item) {
			?>
			<tr>
				<td nowrap="nowrap"><?= $this->Time->format(Configure::read('Monitoring.dateFormat'), $item['MonitoringLog']['created']); ?></td>
				<td><?= $this->Html->tag('span', $item['MonitoringLog']['code_string'], array('class' => 'label label-' . ($item['MonitoringLog']['code_string'] == 'OK' ? 'success' : 'important'))); ?></td>
				<td style="white-space: pre;width:100%"><?= $item['MonitoringLog']['error']; ?></td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
<?=
$this->element('pagination/pagination');
