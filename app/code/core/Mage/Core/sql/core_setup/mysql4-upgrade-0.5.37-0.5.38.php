<?php

$identity = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_identity');
$template = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_template');

$this->addConfigField('trans_email/trans_subscription_success', 'Transactional email - Newsletter subscription success');
$this->addConfigField('trans_email/trans_subscription_success/identity', 'Sender', $identity);
$this->addConfigField('trans_email/trans_subscription_success/template', 'Template', $template);


$this->addConfigField('customer/account', 'Account options');
$this->addConfigField('customer/account/confirm', 'Request new account confirmation', array(
	'frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_yesno'
));

$this->addConfigField('customer/newsletter', 'Newsletter options');
$this->addConfigField('customer/newsletter/confirm', 'Request new subscription confirmation', array(
	'frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_yesno'
));

$conn->multi_query(<<<EOT

update core_config_field set frontend_type='select', source_model='adminhtml/system_config_source_web_protocol' where path like 'web/%/protocol';

EOT
);