<?php

$conn->delete($this->getTable('core/config_field'), "path like 'advanced/datashare/%'");
$conn->delete($this->getTable('core/config_data'), "path like 'advanced/datashare/%'");

$this->addConfigField('advanced/datashare', 'Datasharing', array(
	'show_in_store'=>0,
));

$this->addConfigField('advanced/datashare/default', 'Default', array(
	'frontend_type'=>'multiselect',
	'backend_model'=>'adminhtml/system_config_backend_datashare',
	'source_model'=>'adminhtml/system_config_source_store',
));

#Mage::getSingleton('core/store')->updateDatasharing();

$this->addConfigField('dev/mode', 'Operating mode');
$this->addConfigField('dev/mode/checksum', 'Validate config checksums', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
));

$this->setConfigData('dev/mode/checksum', 1);