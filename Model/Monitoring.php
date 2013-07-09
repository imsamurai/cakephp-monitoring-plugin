<?

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:30:00
 * Format: http://book.cakephp.org/2.0/en/models.html
 */

/**
 * @package Monitoring.Model
 */
class Monitoring extends AppModel {

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
	public $virtualFields = array(
		'next_run_date' => 'last_check + INTERVAL frequency MINUTE'
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
	public function saveCheckResults($checkerId, $code = 0, $codeString = 'OK', $stderr = '', $stdout = '') {
		$data = array(
			$this->alias => array(
				'id' => $checkerId,
				'last_code_string' => $codeString,
				'last_check' => date('Y-m-d H:i:s')
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
				'next_run_date <=' => date('Y-m-d H:i:s')
			),
			'order' => array(
				'priority' => 'DESC'
			),
			'recursive' => -1
		));

		return (array) Hash::extract($checkers, '{n}.'.$this->alias);
	}

}