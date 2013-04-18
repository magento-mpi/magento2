<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$conn                   = $installer->getConnection();
$confTable              = $installer->getTable('core_config_data');
$permConfigModel        = Mage::getModel('saas_paypal/boarding_config');
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

$isEcPermActive = $conn->fetchOne(
    $conn->select()
        ->from($confTable, 'count(*)')
        ->where('path = ?', $ecPermActivePath)
        ->where('value = ?', $authPermOpt)
);
$isWppPermActive = $conn->fetchOne(
    $conn->select()
        ->from($confTable, 'count(*)')
        ->where('path = ?', $wppPermActivePath)
        ->where('value = ?', $authPermOpt)
);
$ecBoardingAccount = $conn->fetchOne(
    $conn->select()
        ->from($confTable, 'value')
        ->where('path = ?', $ecBoardingAccountPath)
);

/**
 * Force using EC permissions depending on some conditions
 */
$forceEcPerm = true;
if (!(string)Mage::getConfig()->getNode($ecCredActivePath, 'default')) {
    Mage::getConfig()->saveConfig(
        $ecAuthPath,
        $authPermOpt
    );
    $forceEcPerm = false;
} else {
    foreach (Mage::app()->getWebsites() as $website) {
        if (!(string)Mage::getConfig()->getNode($ecCredActivePath, 'website', (int)$website->getId())) {
            Mage::getConfig()->saveConfig(
                $ecAuthPath,
                $authPermOpt,
                'websites',
                $website->getId()
            );
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
    Mage::getConfig()->saveConfig(
        $wppAuthPath,
        $authPermOpt
    );
    $forceWppPerm = true;
} else {
    foreach (Mage::app()->getWebsites() as $website) {
        if (!(string)Mage::getConfig()->getNode($wppCredActivePath, 'website', (int)$website->getId())) {
            Mage::getConfig()->saveConfig(
                $wppAuthPath,
                $authPermOpt,
                'websites',
                $website->getId()
            );
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
        Mage::getConfig()->saveConfig(
            $ecBoardingAccountPath,
            (string)Mage::getConfig()->getNode($ecCredAccountPath, 'default')
        );
    } else {
        $tenantData = Mage::app()->getConfig()->getFastStorage()->getDataByTenantId(
            Mage::app()->getConfig()->getOptions()->getTenantId()
        );
        Mage::getConfig()->saveConfig(
            $ecBoardingAccountPath,
            $tenantData['email']
        );
    }
}
