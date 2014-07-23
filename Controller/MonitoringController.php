<?

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 09.07.2013
 * Time: 20:20:20
 * Format: http://book.cakephp.org/2.0/en/controllers.html
 */

/**
 * @spackage Monitoring.Controller
 */
class MonitoringController extends AppController {

	/**
	 * {@inheritdoc}
	 *
	 * @var array 
	 */
	public $uses = array('Monitoring.Monitoring');
	
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
	 * Create
	 */
	public function create() {
		if ($this->request->is('post')) {
			if ($this->Monitoring->save($this->request->data)) {
				$this->Session->setFlash('Monitoring saved.', 'alerts/alert_simple', array('class' => 'alert-success', 'title' => 'Ok!'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Unable to saved monitoring.', 'alerts/alert_simple', array('class' => 'alert-error', 'title' => 'Error!'));
			}
		}
	}

	/**
	 * Edit
	 * 
	 * @param int $monitoringId
	 * @throws NotFoundException
	 */
	public function edit($monitoringId) {
		$this->data = $this->Monitoring->read(null, $monitoringId);
		if (!$this->data) {
			throw new NotFoundException('Monitoring not found!');
		}
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
