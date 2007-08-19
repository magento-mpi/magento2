<?php

$this->addConfigField('sales', 'Sales');

$identity = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_identity');
$template = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_template');


$this->addConfigField('sales/new_order', 'New order options');
$this->addConfigField('sales/new_order/email_identity', 'Confirmation Email Sender', $identity);
$this->addConfigField('sales/new_order/email_template', 'Confirmation Template', $template);

$this->setConfigData('sales/new_order/email_identity', 'sales');
$this->setConfigData('sales/new_order/email_template', '2');