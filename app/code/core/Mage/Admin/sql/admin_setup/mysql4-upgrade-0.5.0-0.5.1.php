<?php

$conn->dropForeignKey('admin_rule', 'FK_admin_rule');

$conn->multi_query(<<<EOT

alter table `admin_rule` 
    ,add constraint `FK_admin_rule` foreign key(`role_id`)references `admin_role` (`role_id`) on delete cascade  on update cascade 

EOT
);