delete from `core_config_data` where `scope` in ('website','store');

alter table `core_config_data` ,change `scope` `scope` enum ('default','websites','stores','config') DEFAULT 'default' NOT NULL  COLLATE utf8_general_ci ;
