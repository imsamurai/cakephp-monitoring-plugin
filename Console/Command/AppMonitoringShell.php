<?

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:00
 * Format: http://book.cakephp.org/2.0/en/console-and-shells.html#creating-a-shell
 */

/**
 * @package Monitoring.Console.Command
 */
class AppMonitoringShell extends AppShell {

	public $tasks = array();
	public $uses = array();
	public $pluginName = 'Monitoring';

	public function runCommand($command, $argv) {
		Configure::write('debug', (int) Hash::get($this->params, 'debug'));
		$this->OptionParser = $this->getOptionParser();
		if (!empty($this->params['help'])) {
			$this->_welcome();
			return $this->out($this->OptionParser->help($command));
		}
		if ($command && $command !== 'main' && $command !== 'execute') {
			if (!in_array(Inflector::camelize($command), $this->enabledTasks) && !method_exists($this, $command)) {
				$out = parent::runCommand($command, $argv);
				return $out;
			}

			$this->tasks = array($this->pluginName . '.' . Inflector::camelize($command));
			$this->loadTasks();
			$out = parent::runCommand($command, $argv);
			return $out;
		} else {
			return parent::runCommand($command, $argv);
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->addOption('debug', array(
			'help' => 'Sets debug level',
			'short' => 'd'
		))->addOption('silent', array(
			'help' => 'Removes banner',
			'short' => 's',
			'boolean' => true,
			'default' => false
		));

		return $parser;
	}

}