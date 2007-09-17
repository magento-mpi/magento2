<?php

$this->run("
delete from `core_config_field` where `path` like 'payment/paypal/%';
delete from `core_config_data` where `path` like 'payment/paypal/%';
");