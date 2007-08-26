<?php

$this->setConfigData('web/cookie/cookie_domain', '');
$this->setConfigData('web/cookie/cookie_path', '');

$this->addConfigField('system/smtp', 'SMTP settings (Windows server only)');
$this->addConfigField('system/smtp/host', 'Host');
$this->addConfigField('system/smtp/port', 'Port (25)');

$this->setConfigData('system/smtp/host', 'localhost');
$this->setConfigData('system/smtp/port', '25');