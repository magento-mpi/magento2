<?php

$this->addConfigField('customer', 'Customers');
$this->addConfigField('customer/default', 'Defaults');
$this->addConfigField('customer/default/group', 'Customer Group', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_customer_group',
));

$this->setConfigData('customer/default/group', 1);