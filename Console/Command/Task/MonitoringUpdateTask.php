<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 19:33:53
 * Format: http://book.cakephp.org/2.0/en/console-and-shells.html#shell-tasks
 */
App::uses('AdvancedTask', 'AdvancedShell.Console/Command/Task');

/**
 * @package Monitoring.Console.Command.Task
 * 
 * @property Monitoring $Monitoring
 */
class MonitoringUpdateTask extends AdvancedTask {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'Update';

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $uses = array('Monitoring.Monitoring');

	/**
	 * Runs checker
	 * @throws Exception
	 */
	public function execute() {
		parent::execute();
		$checkers = $this->Monitoring->findNewCheckers();
		foreach ($checkers as $checker) {
			$success = $this->Monitoring->add($checker);
			if ($success) {
				$this->out("<ok>Added</ok> $checker");
			} else {
				$this->err("<error>Not added</error> $checker");
			}
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description('Update all new checkers');
		return $parser;
	}

}
