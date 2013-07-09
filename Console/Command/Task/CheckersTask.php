<?

use Symfony\Component\Process\Process;

//use Symfony\Component\Process\ProcessUtils;

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:00
 * Format: http://book.cakephp.org/2.0/en/console-and-shells.html#shell-tasks
 */
App::uses('AppShell', 'Monitoring.Console/Command');

/**
 * @package Monitoring.Console.Command.Task
 */
class CheckersTask extends AppShell {

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
	 * Execute all active checkers
	 *
	 * @return void
	 */
	public function execute() {
		$checkers = $this->Monitoring->getActiveCheckers();

		foreach ($checkers as $checker) {
			$this->out("Start check '{$checker['name']}'");
			$Process = new Process('Console/cake Monitoring.monitoring run_checker', APP, array(
				$checker['name'],
				'-d' => $this->params['debug']
			));
			$Process->setTimeout($checker['timeout']);
			$Process->run(function ($type, $buffer) {
						if ('err' === $type) {
							$this->err($buffer);
						} else {
							$this->out($buffer);
						}
					});
			$this->Monitoring->saveCheckResults($checker['id'], $Process->getExitCode(), $Process->getExitCodeText(), $Process->getOutput(), $Process->getErrorOutput());
			$this->out("Finish check '{$checker['name']}' with code '{$Process->getExitCodeText()}'");
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description('Runs active checkers')
		;
		return $parser;
	}

}
