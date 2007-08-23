<?php

$this->startSetup();

$this->run(<<<EOT

replace into `customer_group` (`customer_group_id`, `customer_group_code`, `tax_class_id`) 
	values (0, 'NOT LOGGED IN', 1);

EOT
);

$this->endSetup();