<?php
$this->addConfigField('wishlist/email', 'Share options');

$this->addConfigField('wishlist/email/email_identity', 'Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
));

$this->addConfigField('wishlist/email/email_template', 'Email Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
));

$this->run(
<<<EOT
INSERT INTO `core_email_template` (`template_id`, `template_code`, `template_text`, `template_type`, `template_subject`, `template_sender_name`, `template_sender_email`, `added_at`, `modified_at`) VALUES 
(NULL, 'Share Wishlist', '{{var message}}<br>\r\n\r\n{{var items}}<br>\r\n\r\n<a href="{{var addAllLink}}">Add all items to cart</a><br>\r\n<a href="{{var viewOnSiteLink}}">View this items on site</a>', 2, 'Share Wishlist Subject', NULL, NULL, '2007-08-25 19:27:49', '2007-08-26 15:58:43');
EOT
);
