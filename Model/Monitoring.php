<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:00
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
App::uses('AppMonitoringModel', 'Monitoring.Model');
App::uses('MonitoringChecker', 'Monitoring.Lib/Monitoring');

/**
 * @package Monitoring.Model
 */
class Monitoring extends AppMonitoringModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'Monitoring';

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $hasMany = array('Monitoring.MonitoringLog');
	
	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Serializable.Serializable' => array(
			'fields' => array('settings')
		)
	);

	/**
	 * Saves checker results
	 *
	 * @param int $checkerId
	 * @param int $code
	 * @param string $codeString
	 * @param string $stderr
	 * @param string $stdout
	 *
	 * @return bool True if ok
	 */
	public function saveCheckResults($checkerId, $codeString = MonitoringChecker::STATUS_OK, $error = '') {
		$data = array(
			$this->alias => array(
				'id' => (int)$checkerId,
				'last_code_string' => (string)$codeString,
				'last_check' => date(Configure::read('Monitoring.dbDateFormat'))
			),
			'MonitoringLog' => array(
				array(
					'code_string' => (string)$codeString,
					'error' => (string)$error,
				)
			)
		);

		return $this->saveAssociated($data);
	}

	/**
	 * Returns active checkers
	 *
	 * @param bool $checkNext
	 * @return array
	 */
	public function getActiveCheckers($checkNext = true) {
		$conditions = array(
			'conditions' => array(
				'active' => 1
			),
			'order' => array(
				'priority' => 'DESC'
			)
		);

		if ($checkNext) {
			$conditions['conditions']['next_check <='] = date(Configure::read('Monitoring.dbDateFormat'));
		}

		$checkers = $this->find('all', $conditions);

		return (array)Hash::extract($checkers, '{n}.' . $this->alias);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array $options
	 * @return dool
	 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['cron'])) {
			$cron = $this->data[$this->alias]['cron'];
		} elseif (!empty($this->data[$this->alias]['id'])) {
			$cron = $this->field('cron', array('id' => $this->data[$this->alias]['id']));
		}
		if (!$cron) {
			$cron = Configure::read('Monitoring.defaults.cron');
		}

		$this->data[$this->alias]['cron'] = $cron;
		$this->data[$this->alias]['next_check'] = Cron\CronExpression::factory($cron)
				->getNextRunDate('now')
				->format(Configure::read('Monitoring.dbDateFormat'));
		return parent::beforeSave($options);
	}

	/**
	 * Search for all checker classes
	 * 
	 * @return array
	 */
	public function findAllCheckerClasses() {
		$checkers = array();
		$path = Configure::read('Monitoring.checkersPath');
		foreach (CakePlugin::loaded() as $plugin) {
			$fullPath = CakePlugin::path($plugin) . $path . '/*Check.php';
			foreach (glob($fullPath) as $FileInfo) {
				$checkers[] = $plugin . '.' . basename($FileInfo, '.php');
			}
		}

		return $checkers;
	}

	/**
	 * Search for all new checker classes
	 * 
	 * @return array
	 */
	public function findNewCheckers() {
		$checkers = $this->findAllCheckerClasses();
		$checkersExisted = $this->find('list', array(
			'values' => array('class', 'class'),
			'conditions' => array(
				'class' => $checkers
			)
		));
		return array_diff($checkers, $checkersExisted);
	}

	/**
	 * Add new checker
	 * 
	 * @param name $checker
	 * @param array $options
	 * @return mixed
	 */
	public function add($checker, array $options = array()) {
		$this->create();
		$data = array(
			'class' => $checker
				) + $options + array(
			'name' => $checker
				) + Configure::read('Monitoring.defaults');
		return $this->save($data);
	}

}
