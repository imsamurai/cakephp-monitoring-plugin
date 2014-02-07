<?php
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 09.07.2013
 * Time: 15:49:14
 * Format: http://book.cakephp.org/2.0/en/views.html
 *
 */
/* @var $this IDEView */
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php
			//@codingStandardsIgnoreStart
			echo $title_for_layout;
			//@codingStandardsIgnoreEnd
			?></title>
	</head>
	<body>
		<?php
		//@codingStandardsIgnoreStart
		echo $content_for_layout;
		//@codingStandardsIgnoreEnd
		?>

		<p>This email was sent using the <a href="https://github.com/imsamurai/cakephp-monitoring-plugin" target="_blank">CakePHP Monitoring Plugin</a> by <a href ="https://imsamurai.me/" target="_blank">imsamurai</a></p>
	</body>
</html>