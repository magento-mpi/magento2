drop table if exists cron_schedule;
drop table if exists cron_task;

CREATE TABLE `cron_task` (
  `task_id` int(10) unsigned NOT NULL auto_increment,
  `cron_min` varchar(255) NOT NULL default '*',
  `cron_hour` varchar(255) NOT NULL default '*',
  `cron_day` varchar(255) NOT NULL default '*',
  `cron_mon` varchar(255) NOT NULL default '*',
  `cron_dow` varchar(255) NOT NULL default '*',
  `task_status` tinyint(4) NOT NULL default '0',
  `task_type` tinyint(4) NOT NULL default '0',
  `task_cmd` text NOT NULL,
  `task_comments` text NOT NULL,
  PRIMARY KEY  (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cron_schedule` (
  `schedule_id` int(10) unsigned NOT NULL auto_increment,
  `task_id` int(10) unsigned NOT NULL default '0',
  `schedule_status` tinyint(4) NOT NULL default '0',
  `schedule_type` tinyint(4) NOT NULL default '0',
  `schedule_cmd` text NOT NULL,
  `schedule_comments` text NOT NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `scheduled_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `executed_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `finished_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`schedule_id`),
  constraint `FK_cron_schedule` foreign key(`task_id`) references `cron_task` (`task_id`) on delete cascade  on update cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8;