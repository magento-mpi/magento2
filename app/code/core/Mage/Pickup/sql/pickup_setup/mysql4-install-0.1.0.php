<?php
$code = 'pickup';

$this->setConfigData('carriers/' . $code . '/model', 'pickup/carrier_pickup');

$this->addConfigField('carriers/' . $code, 'Store Pickup', array(
	'sort_order' => 110,
	'show_in_default' => 1,
	'show_in_website' => 1,
	'show_in_store' => 1
));

$this->addConfigField('carriers/' . $code . '/active', 'Enabled', array(
   'frontend_type'=>'select',
   'source_model'=>'adminhtml/system_config_source_yesno'
));

$this->addConfigField('carriers/' . $code . '/title', 'Title');
$this->setConfigData('carriers/' . $code . '/title', 'Store Pickup');

$this->addConfigField('carriers/' . $code . '/name', 'Method name');
$this->setConfigData('carriers/' . $code . '/name', 'Pickup');

$this->addConfigField('carriers/' . $code . '/address_info', 'Pickup Address & Information', array(
   'frontend_type'=>'textarea',
   'sort_order'=>2,
	'show_in_default' => 1,
	'show_in_website' => 1,
	'show_in_store' => 1
));

$this->addConfigField('carriers/' . $code . '/country_id', 'Country', array(
   'frontend_type'=>'select',
   'sort_order'=>3,
	'show_in_default' => 1,
	'show_in_website' => 1,
	'show_in_store' => 1
));

$this->addConfigField('carriers/' . $code . '/region_id', 'Region/State', array(
   'frontend_type'=>'text',
   'sort_order'=>4,
	'show_in_default' => 1,
	'show_in_website' => 1,
	'show_in_store' => 1
));

$this->addConfigField('carriers/' . $code . '/postcode', 'ZIP/Postal Code', array(
   'frontend_type'=>'text',
   'sort_order'=>5,
	'show_in_default' => 1,
	'show_in_website' => 1,
	'show_in_store' => 1
));

$this->addConfigField('carriers/' . $code. '/sort_order', 'Sort Order');

?>
