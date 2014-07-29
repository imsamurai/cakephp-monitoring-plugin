<?php
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 23.04.2012
 * Time: 16:22:13
 *
 * @package Monitoring.View
 */
?>
<div class="alert <?= $class; ?>">
	<a class="close" data-dismiss="alert">×</a>
	<?= $this->Html->tag('strong', $title) . $message; ?>
</div>