<?

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:00
 * Format: http://book.cakephp.org/2.0/en/console-and-shells.html#shell-tasks
 */
App::uses('AppMonitoringShell', 'Monitoring.Console/Command');
App::uses('CakeEmail', 'Network/Email');

/**
 * @package Monitoring.Console.Command.Task
 */
class CheckersTask extends AppMonitoringShell {

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
			$arguments = $this->_argsToString(array(
				$checker['name'],
				'-d' => (int) Configure::read('debug'),
				'-s'
			));
			$Process = new Process('Console/cake Monitoring.monitoring run_checker ' . $arguments, APP);
			$Process->setTimeout($checker['timeout']);
			$Process->run(function ($type, $buffer) {
						if ('err' === $type) {
							$this->err($buffer);
						} else {
							$this->out($buffer);
						}
					});
			$this->Monitoring->saveCheckResults($checker['id'], $Process->getExitCode(), $Process->getExitCodeText(), $Process->getErrorOutput(), $Process->getOutput());
			$this->out("Finish check '{$checker['name']}' with code '{$Process->getExitCodeText()}'");
			if ($Process->getExitCode() != 0 && !empty($checker['emails'])) {
				$this->_sendReport($checker['id']);
			}
		}
	}

	/**
	 * Send email report
	 *
	 * @param int $checkerId
	 */
	protected function _sendReport($checkerId) {
		$emailConfig = (array) Configure::read('Monitoring.Email') + array(
			'send' => true,
			'config' => 'default',
			'subject' => 'Monitoring alert caused by %s returned code: %s!'
		);
		if (!$emailConfig['send']) {
			return;
		}

		$this->Monitoring->contain(array(
			'MonitoringLog' => array(
				'limit' => 1,
				'order' => array('created' => 'DESC')
			)
		));

		$checker = $this->Monitoring->find('first', array(
			'conditions' => array(
				'id' => $checkerId
			)
		));

		if (is_callable($emailConfig['subject'])) {
			$subject = $emailConfig['subject']($checker);
		} else {
			$subject = sprintf($emailConfig['subject'], $checker['Monitoring']['name'], $checker['Monitoring']['last_code_string']);
		}

		list($checkerPlugin, $checkerName) = pluginSplit($checker['Monitoring']['name']);
		if ($checkerPlugin) {
			$checkerPlugin = Inflector::camelize($checkerPlugin) . '.';
		}
		$emails = explode(',', $checker['Monitoring']['emails']);
		$emails = array_map($emails, 'trim');
		$Email = new CakeEmail();
		$Email->config($emailConfig['config'])
				->to($emails)
				->subject($subject)
				->template($checkerPlugin . 'Monitoring/' . Inflector::underscore($checkerName), 'monitoring')
				->viewVars(compact('checker'))
				->emailFormat(CakeEmail::MESSAGE_HTML)
				->helpers(array('Html', 'Text'))
		;
		App::build(array(
			'View' => array(
				App::pluginPath('Monitoring') . 'View' . DS
			)
		), App::APPEND);
		try {
			$Email->send();
		} catch (MissingViewException $Exception) {
			$Email->template($checkerPlugin.'Monitoring/default', 'monitoring')->send();
		}

		$this->out("Sent mail for '{$checker['Monitoring']['name']}'");
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

	/**
	 * Convert array of arguments into string
	 *
	 * @param array $arguments
	 * @return string
	 */
	protected function _argsToString(array $arguments) {
		$stringArguments = '';
		foreach ($arguments as $name => $value) {
			if (is_numeric($name)) {
				$stringArguments.=' ' . $value;
			} else {
				$stringArguments.=' ' . $name . ' ' . ProcessUtils::escapeArgument($value);
			}
		}

		return $stringArguments;
	}

}