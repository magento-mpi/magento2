<?php

$this->addConfigField('payment/paypal_direct', 'PayPal Direct');

$this->addConfigField('payment/paypal_direct/active', 'Enabled', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_yesno',
));