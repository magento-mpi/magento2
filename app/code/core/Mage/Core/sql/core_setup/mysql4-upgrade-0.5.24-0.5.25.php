<?php

$conn->delete('core_config_field', "path like 'email%'");

$this->addConfigField('trans_email', 'Transactional emails');

$this->addConfigField('trans_email/ident_general', 'General contact');
$this->addConfigField('trans_email/ident_general/name', 'Sender name', array(), 'General');
$this->addConfigField('trans_email/ident_general/email', 'Sender email', array(), 'owner@magento.varien.com');

$this->addConfigField('trans_email/ident_sales', 'Sales representative');
$this->addConfigField('trans_email/ident_sales/name', 'Sender name', array(), 'Sales');
$this->addConfigField('trans_email/ident_sales/email', 'Sender email', array(), 'sales@magento.varien.com');

$this->addConfigField('trans_email/ident_support', 'Customer support');
$this->addConfigField('trans_email/ident_support/name', 'Sender name', array(), 'Customer support');
$this->addConfigField('trans_email/ident_support/email', 'Sender email', array(), 'support@magento.varien.com');

$this->addConfigField('trans_email/ident_custom1', 'Custom email 1');
$this->addConfigField('trans_email/ident_custom1/name', 'Sender name', array(), 'Custom 1');
$this->addConfigField('trans_email/ident_custom1/email', 'Sender email', array(), 'custom1@magento.varien.com');

$this->addConfigField('trans_email/ident_custom2', 'Custom email 2');
$this->addConfigField('trans_email/ident_custom2/name', 'Sender name', array(), 'Custom 2');
$this->addConfigField('trans_email/ident_custom2/email', 'Sender email', array(), 'custom2@magento.varien.com');

$identity = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_identity');
$template = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_template');

$this->addConfigField('trans_email/trans_new_account', 'Transactional email - New Account');
$this->addConfigField('trans_email/trans_new_account/identity', 'Sender', $identity);
$this->addConfigField('trans_email/trans_new_account/template', 'Template', $template);

$this->addConfigField('trans_email/trans_new_order', 'Transactional email - New Order');
$this->addConfigField('trans_email/trans_new_order/identity', 'Sender', $identity);
$this->addConfigField('trans_email/trans_new_order/template', 'Template', $template);

$this->addConfigField('trans_email/trans_new_password', 'Transactional email - New Password');
$this->addConfigField('trans_email/trans_new_password/identity', 'Sender', $identity);
$this->addConfigField('trans_email/trans_new_password/template', 'Template', $template);

$this->addConfigField('trans_email/trans_order_update', 'Transactional email - Order Update');
$this->addConfigField('trans_email/trans_order_update/identity', 'Sender', $identity);
$this->addConfigField('trans_email/trans_order_update/template', 'Template', $template);
