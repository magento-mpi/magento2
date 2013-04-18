<?php

$installer = $this;

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$keysToRemove = array(
    'payment/paypal_express/allow_ba_signup',
    'payment/paypal_express_boarding/allow_ba_signup'
);

$installer->run(
    'UPDATE '.$installer->getTable('core_config_data').' SET value=0 WHERE path=\'payment/paypal_billing_agreement/active\';'
);

foreach ($keysToRemove as $key) {
    $installer->getConnection()->delete(
        $installer->getTable('core_config_data'),
        $installer->getConnection()->quoteInto('path = ?', $key)
    );
}

$installer->endSetup();
