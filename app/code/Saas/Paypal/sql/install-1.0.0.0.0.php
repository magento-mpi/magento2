<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$keysToRemove = array(
    'payment/paypal_express/allow_ba_signup',
    'payment/paypal_express_boarding/allow_ba_signup'
);

$connection             = $installer->getConnection();
$confTable              = $installer->getTable('core_config_data');
/** @var Saas_Paypal_Model_Boarding_Config $permConfigModel */
$permConfigModel        = Mage::getModel('Saas_Paypal_Model_Boarding_Config');
$authPermOpt            = Saas_Paypal_Model_System_Config_Source_AuthenticationMethod::TYPE_PERMISSIONS;
$ecPermCode             = Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING;
$ecCredActivePath       = 'payment/'. Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS .'/active';
$ecPermActivePath       = 'payment/'. $ecPermCode .'/active';
$ecAuthPath             = 'payment/'. $ecPermCode .'/authentification_method';
$ecCredAccountPath      = 'paypal/general/business_account';
$ecBoardingAccountPath  = 'paypal/onboarding/boarding_account';
$wppAuthPath            = 'payment/'. Mage_Paypal_Model_Config::METHOD_WPP_DIRECT .'/authentication_method';
$wppCredActivePath      = 'payment/'. Mage_Paypal_Model_Config::METHOD_WPP_DIRECT .'/active';
$wppPermCode            =  Saas_Paypal_Model_Boarding_Config::METHOD_DIRECT_BOARDING;
$wppPermActivePath      = 'payment/'. $wppPermCode .'/active';

$connection->update($installer->getTable('core_config_data'),
    array('value' => 0),
    array('path = ?' => 'payment/paypal_billing_agreement/active'));

foreach ($keysToRemove as $key) {
    $connection->delete(
        $installer->getTable('core_config_data'),
        $connection->quoteInto('path = ?', $key)
    );
}

$isEcPermActive = $connection->fetchOne(
    $connection->select()
        ->from($confTable, 'count(*)')
        ->where('path = ?', $ecPermActivePath)
        ->where('value = ?', $authPermOpt)
);
$isWppPermActive = $connection->fetchOne(
    $connection->select()
        ->from($confTable, 'count(*)')
        ->where('path = ?', $wppPermActivePath)
        ->where('value = ?', $authPermOpt)
);
$ecBoardingAccount = $connection->fetchOne(
    $connection->select()
        ->from($confTable, 'value')
        ->where('path = ?', $ecBoardingAccountPath)
);

/**
 * Force using EC permissions depending on some conditions
 */
$forceEcPerm = true;
if (!(string)Mage::getConfig()->getNode($ecCredActivePath, 'default')) {
    $connection->insertOnDuplicate($confTable, array(
        'path'  => $ecAuthPath,
        'value' => $authPermOpt,
        'scope' => 'default',
        'scope_id' => 0,
    ));
    $forceEcPerm = false;
} else {
    foreach (Mage::app()->getWebsites() as $website) {
        if (!(string)Mage::getConfig()->getNode($ecCredActivePath, 'website', (int)$website->getId())) {
            $connection->insertOnDuplicate($confTable, array(
                'path'  => $ecAuthPath,
                'value' => $authPermOpt,
                'scope' => 'websites',
                'scope_id' => $website->getId(),
            ));
            $forceEcPerm = false;
        }
    }
}
if ($isEcPermActive || $ecBoardingAccount || $forceEcPerm) {
    $permConfigModel->setWasActivated($ecPermCode, 1);
    foreach (Mage::app()->getWebsites() as $website) {
        $permConfigModel->setWasActivated($ecPermCode, 1, 'websites', $website->getId());
    }
}

/**
 * Force using WPP permissions when WPP API credentials inactive
 */
$forceWppPerm = false;
if (!(string)Mage::getConfig()->getNode($wppCredActivePath, 'default')) {
    $connection->insertOnDuplicate($confTable, array(
        'path'  => $wppAuthPath,
        'value' => $authPermOpt,
        'scope' => 'default',
        'scope_id' => 0,
    ));
    $forceWppPerm = true;
} else {
    foreach (Mage::app()->getWebsites() as $website) {
        if (!(string)Mage::getConfig()->getNode($wppCredActivePath, 'website', (int)$website->getId())) {
            $connection->insertOnDuplicate($confTable, array(
                'path'  => $wppAuthPath,
                'value' => $authPermOpt,
                'scope' => 'websites',
                'scope_id' => $website->getId(),
            ));
            $forceWppPerm = true;
        }
    }
}
if ($isWppPermActive || $forceWppPerm) {
    $permConfigModel->setWasActivated($wppPermCode, 1);
    foreach (Mage::app()->getWebsites() as $website) {
        $permConfigModel->setWasActivated($wppPermCode, 1, 'websites', $website->getId());
    }
}

/**
 * Copy account email
 */
if (!(string)Mage::getConfig()->getNode($ecBoardingAccountPath, 'default')) {
    if ((string)Mage::getConfig()->getNode($ecCredAccountPath, 'default')) {
        $connection->insertOnDuplicate($confTable, array(
            'path'  => $ecBoardingAccountPath,
            'value' => (string)Mage::getConfig()->getNode($ecCredAccountPath, 'default'),
            'scope' => 'default',
            'scope_id' => 0,
        ));
    } else {
        $tenantData = Mage::app()->getConfig()->getFastStorage()->getDataByTenantId(
            Mage::app()->getConfig()->getOptions()->getTenantId()
        );
        $connection->insertOnDuplicate($confTable, array(
            'path'  => $ecBoardingAccountPath,
            'value' => $tenantData['email'],
            'scope' => 'default',
            'scope_id' => 0,
        ));
    }
}

/**
 * Set default value for "skip order review page" option for Express Checkout for existing tenants
 */
$connection->insertOnDuplicate($installer->getTable('core_config_data'), array(
    'path'  => 'payment/paypal_express/skip_order_review_step',
    'value' => 0,
    'scope' => 'default',
    'scope_id' => 0,
));

$installer->endSetup();
