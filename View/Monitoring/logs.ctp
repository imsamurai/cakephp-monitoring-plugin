<?
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.07.2013
 * Time: 13:24:14
 * Format: http://book.cakephp.org/2.0/en/views.html
 *
 */
/* @var $this View */
?>
<h1><?= $this->Html->link('Monitoring', array('action' => 'index')); ?> logs for '<?= $this->Html->link($Monitoring['name'], array('action' => 'edit', $Monitoring['id'])); ?>'</h1>
<?
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
			<th>Code</th>
			<th>CodeString</th>
			<th>Stderr</th>
			<th>Stdout</th>
			<th>Created</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($data as $item) {
			?>
			<tr>
				<td><?= $item['MonitoringLog']['id']; ?></td>
				<td><?= $item['MonitoringLog']['code']; ?></td>
				<td><?= $this->Html->tag('span', $item['MonitoringLog']['code_string'], array('class' => 'label label-' . ($item['MonitoringLog']['code_string'] == 'OK' ? 'success' : 'important'))); ?></td>
				<td style="white-space: pre;"><?= Sanitize::html(preg_replace('/(\[[0-9;]{1,}m)/ims', '', $item['MonitoringLog']['stderr'])); ?></td>
				<td style="white-space: pre;"><?= Sanitize::html(preg_replace('/(\[[0-9;]{1,}m)/ims', '', $item['MonitoringLog']['stdout'])); ?></td>
				<td><?= $this->Time->format(Configure::read('Monitoring.dateFormat'), $item['MonitoringLog']['created']); ?></td>

			</tr>
			<?php
		}
		?>
	</tbody>
</table>
<?= $this->element('pagination');
