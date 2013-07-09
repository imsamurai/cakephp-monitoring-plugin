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
class MonitoringLog extends AppMonitoringModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'MonitoringLog';

}