<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 19:33:53
 * Format: http://book.cakephp.org/2.0/en/console-and-shells.html#shell-tasks
 */
App::uses('AdvancedTask', 'AdvancedShell.Console/Command/Task');

/**
 * Task for syncronize all new monitorings
 * 
 * @package Monitoring
 * @subpackage Console.Command.Task
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
		$this->_addNew();
		$this->_removeAbsent();
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

	/**
	 * Add new checkers
	 */
	protected function _addNew() {
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
	 * Remove abscent checkers
	 */
	protected function _removeAbsent() {
		$allCheckers = $this->Monitoring->findAllCheckerClasses();
		$checkers = $this->Monitoring->find('list', array('fields' => array('class')));
		$absentCheckers = array_diff(array_values($checkers), $allCheckers);
		if (!$absentCheckers) {
			return;
		}
		$success = (bool)$this->Monitoring->deleteAll(array(
					'class' => $absentCheckers
		), true, true);
		if ($success) {
			$this->out("<ok>Removed absent checkers (" . implode(', ', $absentCheckers) . ")</ok>");
		} else {
			$this->err("<error>Can't remove absent checkers</error>");
		}
	}

}
