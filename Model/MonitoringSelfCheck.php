<?

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.07.2013
 * Time: 16:33:32
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
App::uses('MonitoringChecker', 'Monitoring.Model');

/**
 * @package Monitoring.Model
 */
class MonitoringSelfCheck extends MonitoringChecker {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'MonitoringSelfCheck';

	/**
	 * Check monitoring plugin
	 */
	public function check() {
		throw new Exception('lalal');
		return true;
	}

}