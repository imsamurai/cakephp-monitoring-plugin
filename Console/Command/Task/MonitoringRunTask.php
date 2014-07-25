<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:00
 * Format: http://book.cakephp.org/2.0/en/console-and-shells.html#shell-tasks
 */
App::uses('AdvancedTask', 'AdvancedShell.Console/Command/Task');
App::uses('CakeEmail', 'Network/Email');

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
				$error = $Checker->getError();
			} catch (Exception $Exception) {
				$success = false;
				$error = $Exception->getMessage();
			}

			$this->Monitoring->saveCheckResults($checker['id'], $success ? 'OK' : 'Error', $error);
			
			if (!$success) {
				$this->err("<error>Error</error> '{$checker['name']}'");
			} else {
				$this->out("<ok>OK</ok> '{$checker['name']}'");
			}
			
			if (!$success && !empty($checker['emails'])) {
				$this->_sendReport($checker['id']);
			}
		}
	}

	/**
	 * Send email report
	 *
	 * @param id $checkerId
	 */
	protected function _sendReport($checkerId) {
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
		
		$success = $this->MonitoringReport->send($checker['Monitoring'], $checker['MonitoringLog']);
		if ($success) {
			$this->out("<ok>Sent mail</ok> for '{$checker['Monitoring']['name']}'");
		} else {
			$this->err("<error>Fail to sent mail</error> for '{$checker['Monitoring']['name']}'");
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
