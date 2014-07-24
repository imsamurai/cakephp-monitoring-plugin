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
		parent::execute();
		$checkers = $this->Monitoring->getActiveCheckers();

		foreach ($checkers as $checker) {
			$this->out("Check '{$checker['name']}'");

			try {
				list($plugin, $class) = pluginSplit($checker['class']);
				App::uses($class, $plugin . '.' . Configure::read('Monitoring.checkersPath'));
				$Checker = new $class;
				$success = $Checker->check();
				$error = $Checker->error();
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
	 * @param int $checkerId
	 */
	protected function _sendReport($checkerId) {
		$emailConfig = (array)Configure::read('Monitoring.Email') + array(
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

		list(, $checkerName) = pluginSplit($checker['Monitoring']['name']);

		$emails = explode(',', $checker['Monitoring']['emails']);
		$emails = array_map('trim', $emails);
		$Email = new CakeEmail();
		$Email->config($emailConfig['config'])
				->to($emails)
				->subject($subject)
				->template('Monitoring/' . Inflector::underscore($checkerName), 'monitoring')
				->viewVars(compact('checker'))
				->emailFormat(CakeEmail::MESSAGE_HTML)
				->helpers(array('Html', 'Text'));
		
		App::build(array(
			'View' => array(
				App::pluginPath('Monitoring') . 'View' . DS
			)
				), App::APPEND);
		
		try {
			$Email->send();
		} catch (MissingViewException $Exception) {
			$Email->template('Monitoring/default', 'monitoring')->send();
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
		$parser->description('Runs active checkers');
		return $parser;
	}

}
