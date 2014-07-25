<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 25.07.2014
 * Time: 13:00:32
 * 
 */

/**
 * Monitoring Report
 */
class MonitoringReport {

	/**
	 * Send notification
	 * 
	 * @param array $checker
	 * @param array $logs
	 * @return bool
	 */
	public function send(array $checker, array $logs) {
		$emailConfig = Configure::read('Monitoring.email');
		if (!$emailConfig['enabled']) {
			return false;
		}

		list($plugin, $checkerName) = pluginSplit($checker['class']);

		$this->_initViewPaths($plugin);

		$Email = new CakeEmail();
		$Email->config($emailConfig['config'])
				->to($this->_getEmails($checker))
				->subject($this->_buildSubject($emailConfig['subject'], $checker, $logs))
				->template('Monitoring/' . Inflector::underscore($checkerName), 'monitoring')
				->viewVars(compact('checker', 'logs'))
				->emailFormat(CakeEmail::MESSAGE_HTML)
				->helpers(array('Html', 'Text'));

		try {
			return $Email->send();
		} catch (MissingViewException $Exception) {
			return $Email->template('Monitoring/default', 'monitoring')->send();
		}
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
			return sprintf($subject, $checker['name']);
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
		App::build(array('View' => $viewPaths), App::APPEND);
	}

}
