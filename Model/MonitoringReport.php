<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 25.07.2014
 * Time: 13:00:32
 * 
 */
App::uses('CakeEmail', 'Network/Email');

/**
 * Monitoring Report
 * 
 * @property Monitoring $Monitoring Monitoring
 * @package Monitoring
 * @package Model
 */
class MonitoringReport {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->Monitoring = ClassRegistry::init('Monitoring.Monitoring');
	}

	/**
	 * Send notification
	 * 
	 * @param int $checkerId
	 * @return bool|null
	 */
	public function send($checkerId) {
		list($checker, $logs) = $this->_getCheckerData($checkerId);
		$type = $this->_getType($logs);
		$this->_sendSMS($checker, $logs, $type, Configure::read('Monitoring.sms'));
		return $this->_sendMail($checker, $logs, $type, Configure::read('Monitoring.email'));
	}

	/**
	 * Check if SMS service is enabled
	 * 
	 * @return bool
	 */
	public function isSMSEnabled() {
		return CakePlugin::loaded('SMSFlySource');
	}

	/**
	 * Send email
	 * 
	 * @param array $checker
	 * @param array $logs
	 * @param string $type
	 * @param array $mailerConfig
	 * @return bool|null
	 */
	protected function _sendMail($checker, $logs, $type, $mailerConfig) {
		if (
				!$checker['active'] || !trim($checker['emails']) || ($mailerConfig['enabled'] !== true && !$mailerConfig['enabled'][$type])
		) {
			return null;
		}

		list($plugin, $checkerName) = pluginSplit($checker['class']);

		$this->_initViewPaths($plugin);

		$subject = $this->_buildSubject($mailerConfig['subject'][$type], $checker, $logs);
		$Email = $this->_getMailer()
				->config($mailerConfig['config'])
				->to($this->_getEmails($checker))
				->subject($subject)
				->template('Monitoring/' . Inflector::underscore($checkerName), 'monitoring')
				->viewVars(compact('checker', 'logs', 'subject'))
				->emailFormat(CakeEmail::MESSAGE_HTML)
				->helpers(array('Html', 'Text'));

		try {
			return (bool)$Email->send();
		} catch (MissingViewException $Exception) {
			return (bool)$Email->template('Monitoring/default', 'monitoring')->send();
		}
	}

	/**
	 * Send SMS
	 * 
	 * @param array $checker
	 * @param array $logs
	 * @param string $type
	 * @param array $mailerConfig
	 * @return bool|null
	 */
	protected function _sendSMS($checker, $logs, $type, $mailerConfig) {
		if (
				!$this->isSMSEnabled() || !$checker['active'] || !trim($checker['sms']) || ($mailerConfig['enabled'] !== true && !$mailerConfig['enabled'][$type])
		) {
			return null;
		}

		$subject = $this->_buildSubject($mailerConfig['subject'][$type], $checker, $logs) .
				'(' . Configure::read('App.fullBaseUrl') . ')';
		$SMSMailer = $this->_getSMSMailer();
		return $SMSMailer->sendSMS($mailerConfig['source'], array(
					$subject => $this->_getSMS($checker)
						), $mailerConfig['desc']);
	}

	/**
	 * Return sms mailer
	 * 
	 * @param array $config
	 * @return \Model
	 */
	protected function _getSMSMailer() {
		return ClassRegistry::init('SMSFlySource.SMSFly');
	}

	/**
	 * Return mailer
	 * 
	 * @param array $config
	 * @return \CakeEmail
	 */
	protected function _getMailer(array $config = array()) {
		return new CakeEmail($config);
	}

	/**
	 * Return checker data
	 * 
	 * @param int $checkerId
	 * @return array checker and logs
	 * @throws Exception
	 */
	protected function _getCheckerData($checkerId) {
		$this->Monitoring->contain(array(
			'MonitoringLog' => array(
				'limit' => 10,
				'order' => array(
					'created' => 'DESC',
					'id' => 'DESC',
				)
			)
		));

		$checker = $this->Monitoring->find('first', array(
			'conditions' => array(
				'id' => $checkerId
			)
		));
		if (!$checker) {
			throw new Exception("Checker not found #");
		}
		return array($checker[$this->Monitoring->alias], $checker['MonitoringLog']);
	}

	/**
	 * Return checker emails
	 * 
	 * @param array $checker
	 * @return array
	 */
	protected function _getEmails(array $checker) {
		return array_map('trim', explode(',', $checker['emails']));
	}

	/**
	 * Return checker SMS numbers
	 * 
	 * @param array $checker
	 * @return array
	 */
	protected function _getSMS(array $checker) {
		return array_map('trim', explode(',', $checker['sms']));
	}

	/**
	 * Return report type
	 * 
	 * @param array $logs
	 * @return string
	 */
	protected function _getType(array $logs) {
		$ok = MonitoringChecker::STATUS_OK;
		$fail = MonitoringChecker::STATUS_FAIL;

		if ($logs[0]['code_string'] === $ok && isset($logs[1]) && $logs[1]['code_string'] === $fail) {
			return 'backToNormal';
		} elseif ($logs[0]['code_string'] === $ok) {
			return 'success';
		} elseif ($logs[0]['code_string'] === $fail && isset($logs[1]) && $logs[1]['code_string'] === $fail) {
			return 'stillFail';
		} else {
			return 'fail';
		}
	}

	/**
	 * Build subject string
	 * 
	 * @param string $subject
	 * @param array $checker
	 * @param array $logs
	 * @return string
	 */
	protected function _buildSubject($subject, array $checker, array $logs) {
		if (is_callable($subject)) {
			return $subject($checker, $logs);
		} else {
			return sprintf($subject, $checker['name'], substr($logs[0]['error'], 0, 50));
		}
	}

	/**
	 * Initialize paths for views
	 * 
	 * @param string $plugin
	 */
	protected function _initViewPaths($plugin) {
		$viewPaths = array();
		if ($plugin) {
			$viewPaths[] = App::pluginPath($plugin) . 'View' . DS;
		}
		$viewPaths[] = App::pluginPath('Monitoring') . 'View' . DS;
		App::build(array(
			'View' => $viewPaths
				), Configure::read('Monitoring.views.pluginFirst') ? App::PREPEND : App::APPEND);
	}

}
