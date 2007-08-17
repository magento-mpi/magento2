<?php

$this->addConfigField('web/cookie', 'Cookie management');
$this->addConfigField('web/cookie/cookie_domain', 'Cookie Domain');
$this->addConfigField('web/cookie/cookie_path', 'Cookie Path');
$this->addConfigField('web/cookie/cookie_lifetime', 'Cookie Lifetime');

$identity = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_identity');
$template = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_template');

$this->addConfigField('trans_email/trans_new_subscription', 'Transactional email - Newsletter subscription');
$this->addConfigField('trans_email/trans_new_subscription/identity', 'Sender', $identity);
$this->addConfigField('trans_email/trans_new_subscription/template', 'Template', $template);