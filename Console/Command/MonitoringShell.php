<?

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:00
 * Format: http://book.cakephp.org/2.0/en/console-and-shells.html#creating-a-shell
 */

App::uses('AppShell', 'Monitoring.Console/Command');

/**
 * @package Monitoring.Console.Command
 */
class MonitoringShell extends AppShell {

	public $enabledTasks = array(
		'Checkers',
		'RunChecker'
	);

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description('Monitoring shell options');

		foreach ($this->enabledTasks as $task_name) {
			$parser->addSubcommand(Inflector::underscore($task_name), array(
				'help' => $this->Tasks->load($this->pluginName.'.'.$task_name)->getOptionParser()->description(),
				'parser' => $this->Tasks->load($this->pluginName.'.'.$task_name)->getOptionParser()
			));
		}
		return $parser;
	}

}