<?

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
	public function saveCheckResults($checkerId, $code = 0, $codeString = 'OK', $stderr = '', $stdout = '') {
		$data = array(
			$this->alias => array(
				'id' => $checkerId,
				'last_code_string' => $codeString,
				'last_check' => date(static::$DBDateTimeFormat)
			),
			'MonitoringLog' => array(
				array(
					'code' => $code,
					'code_string' => $codeString,
					'stderr' => $stderr,
					'stdout' => $stdout
				)
			)
		);

		return $this->saveAssociated($data);
	}

	/**
	 * Returns active checkers that can be runned at this time
	 *
	 * @return array
	 */
	public function getActiveCheckers() {
		$checkers = $this->find('all', array(
			'conditions' => array(
				'active' => 1,
				'next_check <=' => date(static::$DBDateTimeFormat)
			),
			'order' => array(
				'priority' => 'DESC'
			)
		));

		return (array) Hash::extract($checkers, '{n}.' . $this->alias);
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
		}
		else if (!empty($this->data[$this->alias]['id'])) {
			$cron = $this->field('cron', array('id' => $this->data[$this->alias]['id']));
		}
		if (!$cron) {
			$cron = static::$defaultCron;
		}

		$this->data[$this->alias]['cron'] = $cron;
		$this->data[$this->alias]['next_check'] = Cron\CronExpression::factory($cron)->getNextRunDate('now', 0, true)->format(static::$DBDateTimeFormat);
		return parent::beforeSave($options);
	}

}