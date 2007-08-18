<?php

$conn->multi_query(<<<EOT

delete from core_config_field where path like 'email%';
delete from core_config_field where path like 'trans_email/trans%';
delete from core_config_field where path like 'newsletter%';

EOT
);

$identity = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_identity');
$template = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_template');

$this->addConfigField('newsletter', 'Newsletter');
$this->addConfigField('newsletter/subscription', 'Subscription Options');
$this->addConfigField('newsletter/subscription/confirm', 'Need Confirmation', array(
    'frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_yesno'
));
$this->addConfigField('newsletter/subscription/confirm_email_identity', 'Confirmation Email Sender', $identity);
$this->addConfigField('newsletter/subscription/confirm_email_template', 'Confirmation Email Template', $template);
$this->addConfigField('newsletter/subscription/success_email_identity', 'Success Email Sender', $identity);
$this->addConfigField('newsletter/subscription/success_email_template', 'Success Email Template', $template);
$this->addConfigField('newsletter/subscription/un_email_identity', 'Unsubscription Email Sender', $identity);
$this->addConfigField('newsletter/subscription/un_email_template', 'Unsubscription Email Template', $template);
