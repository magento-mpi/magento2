<?php

$this->addConfigField('dev', 'Developer');
$this->addConfigField('dev/debug', 'Debug');
$this->addConfigField('dev/debug/profiler', 'Profiler', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
));