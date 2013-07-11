<?

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 19:33:53
 * Format: http://book.cakephp.org/2.0/en/console-and-shells.html#shell-tasks
 */
App::uses('AppMonitoringShell', 'Monitoring.Console/Command');

/**
 * @package Monitoring.Console.Command.Task
 */
class RunCheckerTask extends AppMonitoringShell {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $uses = array('Monitoring.Monitoring');

	/**
	 * Monitoring model
	 *
	 * @var Monitoring
	 */
	public $Monitoring = null;

	/**
	 * Runs checker
	 */
	public function execute() {
		try {
			list($plugin, $name) = pluginSplit($this->args[0]);
			$plugin = Inflector::camelize($plugin);
			$name = 'Monitoring'.Inflector::camelize($name).'Check';
			if ($plugin) {
				$className = "$plugin.$name";
			} else {
				$className = $name;
			}
			$Checker = ClassRegistry::init($className);
			$Checker->check();
		} catch (Exception $Exception) {
			throw new Exception($Exception->getMessage());
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description('Run single checker')
				->addArgument('name', array(
					'required' => true,
					'help' => 'Checker model class name'
				))
		;
		return $parser;
	}

}