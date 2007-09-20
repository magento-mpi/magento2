<?php

$this->startSetup()->run("

drop table if exists core_layout;
create table core_layout (
layout_id int unsigned not null auto_increment primary key,
package varchar(255) not null,
theme varchar(255) not null,
handle varchar(255) not null,
layout_update text not null,
sort_order smallint unsigned not null,
key (handle, package, theme, sort_order)
);

")->endSetup();