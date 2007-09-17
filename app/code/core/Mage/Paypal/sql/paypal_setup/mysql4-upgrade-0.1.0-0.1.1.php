<?php

$this->run("
delete from `core_config_data` where path like 'payment/paypal/%';
");