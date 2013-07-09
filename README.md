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

## Usage

Use `Monitoring` model for manage checkers in DB.
Each checker name in DB must be a model that extends `MonitoringChecker` and implements `check` method.
Coordinator will run this method and store result in DB log.
In case of error your checker may throw exception.
Put `Console/cake Monitoring.monitoring checkers` in the cron, for ex each 1-5 minutes (depends on your needs)
