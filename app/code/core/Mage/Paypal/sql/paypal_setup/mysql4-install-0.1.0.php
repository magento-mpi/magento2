<?php

$this->run("
drop table if exists paypal_api_debug;
create table paypal_api_debug (
debug_id int unsigned not null auto_increment primary key,
debug_at timestamp,
request_body text,
response_body text,
index (debug_at)
);
");

$this->addConfigField('paypal', 'PayPal');


// PAYPAL WPP
$this->addConfigField('paypal/wpp', 'Website Payments Pro');
$this->addConfigField('paypal/wpp/sandbox_flag', 'Sandbox Flag', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_yesno',
));

$this->addConfigField('paypal/wpp/api_username', 'API User Name');
$this->addConfigField('paypal/wpp/api_password', 'API Password');
$this->addConfigField('paypal/wpp/api_signature', 'API Signature');

$this->addConfigField('paypal/wpp/use_proxy', 'Use Proxy', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_yesno',
));
$this->addConfigField('paypal/wpp/proxy_host', 'Proxy Host');
$this->addConfigField('paypal/wpp/proxy_port', 'Proxy Port');


// PAYPAL EXPRESS
$this->addConfigField('payment/paypal_express', 'Paypal Express');

$this->setConfigData('payment/paypal_express/model', 'paypal/express');

$this->addConfigField('payment/paypal_express/active', 'Enabled', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_yesno',
));

$this->addConfigField('payment/paypal_express/title', 'Title');

$this->addConfigField('payment/paypal_express/order_status', 'New order status', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_order_status',
));

$this->addConfigField('payment/paypal_express/sort_order', 'Sort order');

// PAYPAL DIRECT
$this->addConfigField('payment/paypal_direct', 'PayPal Direct');

$this->setConfigData('payment/paypal_direct/model', 'paypal/direct');

$this->addConfigField('payment/paypal_direct/active', 'Enabled', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_yesno',
));

$this->addConfigField('payment/paypal_direct/title', 'Title');

$this->addConfigField('payment/paypal_direct/order_status', 'New order status', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_order_status',
));

$this->addConfigField('payment/paypal_direct/sort_order', 'Sort order');

$this->installEntities($this->getDefaultEntities());