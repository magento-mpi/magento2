<?php

$this->addConfigField('web_track', 'Web tracking');
$this->addConfigField('web_track/google', 'Google analytics');
$this->addConfigField('web_track/google/urchin_enable', 'Enable', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
));
$this->addConfigField('web_track/google/urchin_account', 'Account number');