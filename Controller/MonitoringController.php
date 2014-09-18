<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 09.07.2013
 * Time: 20:20:20
 * Format: http://book.cakephp.org/2.0/en/controllers.html
 */

/**
 * Monitoring controller
 * 
 * @property Monitoring $Monitoring Monitoring model
 * @property MonitoringReport $MonitoringReport Monitoring report model
 * 
 * @package Monitoring
 * @subpackage Controller
 */
class MonitoringController extends AppController {

	/**
	 * {@inheritdoc}
	 *
	 * @var array 
	 */
	public $uses = array('Monitoring.Monitoring', 'Monitoring.MonitoringReport');
	
	/**
	 * {@inheritdoc}
	 *
	 * @var array 
	 */
	public $helpers = array('Html', 'Form', 'Time');

	/**
	 * List
	 */
	public function index() {
		$this->paginate = array(
			'Monitoring' => array(
				'limit' => Configure::read('Pagination.limit'),
				'order' => array('id' => 'desc')
			)
		);
		$data = $this->paginate("Monitoring");

		$this->set(array(
			'data' => $data
		));
	}

	/**
	 * Save
	 */
	public function save() {
		if ($this->request->is('post')) {
			if ($this->Monitoring->save($this->request->data)) {
				$this->Session->setFlash('Monitoring saved.', 'alert/simple', array('class' => 'alert-success', 'title' => 'Ok!'));
			} else {
				$this->Session->setFlash('Unable to saved monitoring.', 'alert/simple', array('class' => 'alert-error', 'title' => 'Error!'));
			}
		}
		$this->redirect($this->referer());
	}
	
	/**
	 * Edit
	 * 
	 * @param int $monitoringId
	 * @throws NotFoundException
	 */
	public function edit($monitoringId) {
		$checker = $this->Monitoring->read(null, $monitoringId);
		if (!$checker) {
			throw new NotFoundException('Monitoring not found!');
		}
		
		list(, $class) = pluginSplit($checker[$this->Monitoring->alias]['class']);
		$checker[$this->Monitoring->alias]['settings'] = 
				(array)$checker[$this->Monitoring->alias]['settings'] 
				+ (array)Configure::read("Monitoring.checkers.$class.defaults");
		
		$this->request->data = $checker;
		$this->set('settingsView', Inflector::underscore($class));
		$this->set('isSMSEnabled', $this->MonitoringReport->isSMSEnabled());
	}

	/**
	 * Logs
	 * 
	 * @param id $monitoringId
	 * @throws NotFoundException
	 */
	public function logs($monitoringId) {
		$monitoring = $this->Monitoring->read(null, $monitoringId);
		if (!$monitoring) {
			throw new NotFoundException('Monitoring not found!');
		}
		$this->paginate = array(
			'MonitoringLog' => array(
				'limit' => Configure::read('Pagination.limit'),
				'order' => array('id' => 'desc'),
				'conditions' => array(
					'monitoring_id' => $monitoringId
				)
			)
		);

		$this->set('data', $this->paginate('MonitoringLog'));
		$this->set($monitoring);
	}

}
