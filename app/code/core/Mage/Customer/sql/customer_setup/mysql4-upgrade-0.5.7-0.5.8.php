<?php
$conn->multi_query(<<<EOT

delete from core_config_field where path like 'customer%';

EOT
);


$identity = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_identity');
$template = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_template');


$this->addConfigField('customer', 'Customers');

$this->addConfigField('customer/create_account', 'Create New Account Options');
$this->addConfigField('customer/create_account/default_group', 'Default Group', array(
    'frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_customer_group'
));
$this->addConfigField('customer/create_account/confirm', 'Need Confirmation', array(
    'frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_yesno'
));
$this->addConfigField('customer/create_account/email_identity', 'Email Sender', $identity);
$this->addConfigField('customer/create_account/email_template', 'Email Template', $template);


$this->addConfigField('customer/password', 'Password Options');
$this->addConfigField('customer/password/forgot_email_identity', 'Forgot Email Sender', $identity);
$this->addConfigField('customer/password/forgot_email_template', 'Forgot Email Template', $template);
