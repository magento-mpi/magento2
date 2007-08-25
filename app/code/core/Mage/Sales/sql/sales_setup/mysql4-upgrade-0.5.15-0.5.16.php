<?php

$identity = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_identity');
$template = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_template');


$this->addConfigField('sales/order_update', 'Order update options');
$this->addConfigField('sales/order_update/email_identity', 'Email Sender', $identity);
$this->addConfigField('sales/order_update/email_template', 'Template', $template);

$this->setConfigData('sales/order_update/email_identity', 'sales');
$this->setConfigData('sales/order_update/email_template', '4');