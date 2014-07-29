<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:00
 * Format: http://book.cakephp.org/2.0/en/console-and-shells.html#shell-tasks
 */
App::uses('AdvancedTask', 'AdvancedShell.Console/Command/Task');
App::uses('CakeEmail', 'Network/Email');
App::uses('MonitoringChecker', 'Monitoring.Lib/Monitoring');

/**
 * @package Monitoring.Console.Command.Task
 * 
 * @property Monitoring $Monitoring Monitoring model
 * @property MonitoringReport $MonitoringReport MonitoringReport model
 */
class MonitoringRunTask extends AdvancedTask {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'Run';

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $uses = array('Monitoring.Monitoring', 'Monitoring.MonitoringReport');

	/**
	 * Execute all active checkers
	 *
	 * @return void
	 */
	public function execute() {
		parent::execute();
		$checkers = $this->Monitoring->getActiveCheckers();

		foreach ($checkers as $checker) {
			$this->out("Check '{$checker['name']}'");

			try {
				list($plugin, $class) = pluginSplit($checker['class']);
				App::uses($class, $plugin . '.' . Configure::read('Monitoring.checkersPath'));
				$Checker = new $class($checker['settings']);
				$success = $Checker->check();
				$status = $Checker->getStatus();
				$error = $Checker->getError();
			} catch (Exception $Exception) {
				$success = false;
				$error = $Exception->getMessage();
				$status = MonitoringChecker::STATUS_FAIL;
			}

			$this->Monitoring->saveCheckResults($checker['id'], $status, $error);

			if (!$success) {
				$this->err("<error>Error</error> '{$checker['name']}'");
			} else {
				$this->out("<ok>OK</ok> '{$checker['name']}'");
			}

			$this->_sendReport($checker['id']);
		}
	}

	/**
	 * Send email report
	 *
	 * @param id $checkerId
	 */
	protected function _sendReport($checkerId) {
		try {
			$success = $this->MonitoringReport->send($checkerId);
		} catch (Exception $Exception) {
			$success = false;
			$this->err("<error>" . $Exception->getMessage() . "</error>");
		}
		if ($success) {
			$this->out("<ok>Sent mail</ok> for checker #$checkerId");
		} else {
			$this->err("<error>Fail to sent mail</error> for checker #$checkerId");
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description('Runs active checkers');
		return $parser;
	}

}
