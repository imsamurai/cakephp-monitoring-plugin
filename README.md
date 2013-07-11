Cakephp Monitoring Plugin
=========================

Coordinator for any checker scripts.
With this plugin you can unify periodic checkers for some of your services/data/etc,
get mail in case of failure, store checker logs in DB. 

## Installation

	cd my_cake_app/app
	git clone git://github.com/imsamurai/cakephp-monitoring-plugin.git Plugin/Monitoring

or if you use git add as submodule:

	cd my_cake_app
	git submodule add "git://github.com/imsamurai/cakephp-monitoring-plugin.git" "app/Plugin/Monitoring"

then add plugin loading in Config/bootstrap.php

	CakePlugin::load('Monitoring');

add tables from `Config/Schema/monitoring.sql`

include https://github.com/symfony/Process in your project, for ex with composer (tested with 2.3 version)

## Configuration

Write global config if you need to change plugin config:

	Configure::write('Monitoring', array(
      'Email' => array(
	    'send' => true, //default true
	    'config' => '<config_name_or_config_array>', // default 'default'
		/*
		    Can be callable with 1 argument, for ex:
		 	array(
				'Monitoring' => array(
					'id' => '1',
					'name' => 'Test1',
					'description' => '',
					'frequency' => '5',
					'timeout' => '600',
					'last_code_string' => 'BAD',
					'last_check' => '2013-07-09 14:03:22',
					'active' => '0',
					'priority' => '0',
					'emails' => '',
					'created' => '0000-00-00 00:00:00',
					'modified' => '2013-07-09 14:03:22',
					'next_run_date' => '2013-07-09 14:08:22'
				),
				'MonitoringLog' => array(
					(int) 0 => array(
						'id' => '1',
						'monitoring_id' => '1',
						'code' => '0',
						'code_string' => 'OK',
						'stderr' => '',
						'stdout' => '',
						'created' => '2012-05-05 12:22:22'
					)
				)
			)
		   or text for sprintf with 2 arguments: name and last_code_string

		   default 'Monitoring alert caused by %s returned code: %s!'
		 */
        'subject' => 'string_for_sprintf_or_callable'
      )
	));

## Usage

Use `Monitoring` model for manage checkers in DB.
Each checker name in DB must be a model that extends `MonitoringChecker` and implements `check` method.
Coordinator will run this method and store result in DB log.
In case of error your checker may throw exception.
Put `Console/cake Monitoring.monitoring checkers` in the cron, for ex each 1-5 minutes (depends on your needs)

Also check out [wiki](https://github.com/imsamurai/cakephp-monitoring-plugin/wiki).
