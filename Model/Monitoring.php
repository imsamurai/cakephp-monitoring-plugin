<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:00
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
App::uses('AppMonitoringModel', 'Monitoring.Model');

/**
 * @package Monitoring.Model
 */
class Monitoring extends AppMonitoringModel {

	/**
	 * Default cron value
	 *
	 * @var string
	 */
	public static $defaultCron = '*/5 * * * *';

	/**
	 * Default date time format for database
	 *
	 * @var string
	 */
	public static $DBDateTimeFormat = 'Y-m-d H:i:s';

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
	public function saveCheckResults($checkerId, $codeString = 'OK', $error = '') {
		$data = array(
			$this->alias => array(
				'id' => (int)$checkerId,
				'last_code_string' => (string)$codeString,
				'last_check' => date(static::$DBDateTimeFormat)
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
	 * @param bool $nextCheck
	 * @return array
	 */
	public function getActiveCheckers($nextCheck = true) {
		$conditions = array(
			'conditions' => array(
				'active' => 1,
			),
			'order' => array(
				'priority' => 'DESC'
			)
		);

		if ($nextCheck) {
			$conditions['conditions']['next_check <='] = date(static::$DBDateTimeFormat);
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
			$cron = static::$defaultCron;
		}

		$this->data[$this->alias]['cron'] = $cron;
		$this->data[$this->alias]['next_check'] = Cron\CronExpression::factory($cron)->getNextRunDate('now')->format(static::$DBDateTimeFormat);
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
			foreach (new GlobIterator($fullPath) as $FileInfo) {
				$checkers[] = $plugin . '.' . $FileInfo->getBasename('.' . $FileInfo->getExtension());
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
