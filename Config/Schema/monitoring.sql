CREATE TABLE `monitorings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `class` varchar(500) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `cron` varchar(100) NOT NULL DEFAULT '*/5 * * * *',
  `timeout` int(10) NOT NULL DEFAULT '600',
  `last_code_string` varchar(100) NOT NULL DEFAULT '',
  `last_check` datetime NOT NULL,
  `next_check` datetime NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `priority` int(10) NOT NULL DEFAULT '0',
  `emails` text NOT NULL,
  `sms` text NOT NULL,
  `settings` longtext,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `monitoring_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `monitoring_id` bigint(20) unsigned NOT NULL,
  `code_string` varchar(100) NOT NULL DEFAULT 'OK',
  `error` longtext NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `monitoring_id` (`monitoring_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
