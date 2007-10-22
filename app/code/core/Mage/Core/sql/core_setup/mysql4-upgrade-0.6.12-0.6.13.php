<?php

$this->startSetup()->run("

drop table if exists `core_url_rewrite`;
create table `core_url_rewrite` (
    `url_rewrite_id` int unsigned not null auto_increment primary key,
    `store_id` smallint unsigned not null,
    `id_path` varchar(255) not null,
    `request_path` varchar(255) not null,
    `target_path` varchar(255) not null,
    `options` varchar(255) not null,
    unique (`id_path`, `store_id`),
    unique (`request_path`, `store_id`),
    key (`target_path`, `store_id`),
    foreign key (`store_id`) references `core_store` (`store_id`) on delete cascade on update cascade
) engine=InnoDB default charset=utf8;

drop table if exists `core_url_rewrite_tag`;
create table `core_url_rewrite_tag` (
    `url_rewrite_tag_id` int unsigned not null auto_increment primary key,
    `url_rewrite_id` int unsigned not null,
    `tag` varchar(255),
    unique (`tag`, `url_rewrite_id`),
    key (`url_rewrite_id`),
    foreign key (`url_rewrite_id`) references `core_url_rewrite` (`url_rewrite_id`) on delete cascade on update cascade
) engine=InnoDB default charset=utf8;

")->endSetup();