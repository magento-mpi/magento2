<?php

$conn->multi_query(<<<EOT

delete from core_config_field where path like 'design/package/%';

update core_config_data set value='{{root_dir}}/skin' where path='system/filesystem/skin';

EOT
);

$this->addConfigField('design/package/name', 'Current package name');

$this->addConfigField('design/theme', 'Themes');
$this->addConfigField('design/theme/default', 'Default');
$this->addConfigField('design/theme/layout', 'Layout');
$this->addConfigField('design/theme/template', 'Templates');
$this->addConfigField('design/theme/skin', 'Skin (Images / CSS)');
$this->addConfigField('design/theme/translate', 'Translations');
