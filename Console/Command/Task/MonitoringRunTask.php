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
			$result = $this->_run($checker);
			$this->_handleResult($checker, $result);
		}
	}

	/**
	 * Run checker
	 * 
	 * @param array $checker
	 * @return array
	 */
	protected function _run(array $checker) {
		$this->out("<warning>Run</warning> '{$checker['name']}'");

		$this->Monitoring->getDataSource()->reconnect();
		return $this->Monitoring->run($checker);
	}

	/**
	 * Handle checker result
	 * 
	 * @param array $checker
	 * @param array $result
	 */
	protected function _handleResult(array $checker, array $result) {
		if (!$result['success']) {
			$this->err("<error>Error</error> '{$checker['name']}'");
		} else {
			$this->out("<ok>OK</ok> '{$checker['name']}'");
		}
		$this->Monitoring->getDataSource()->reconnect();
		$this->Monitoring->saveCheckResults($checker['id'], $result['status'], $result['error']);
		$this->_sendReport($checker);
	}

	/**
	 * Send email report
	 *
	 * @param array $checker
	 */
	protected function _sendReport(array $checker) {
		try {
			$success = $this->MonitoringReport->send($checker['id']);
		} catch (Exception $Exception) {
			$success = false;
			$this->err("<error>" . $Exception->getMessage() . "</error>");
		}
		if ($success === true) {
			$this->out("<ok>Sent mail</ok> for '{$checker['name']}'");
		} elseif ($success === false) {
			$this->err("<error>Fail to sent mail</error> for '{$checker['name']}'");
		} else {
			$this->out("<warning>Mail not sent (inactive/no emails/etc)</warning> for '{$checker['name']}'");
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
