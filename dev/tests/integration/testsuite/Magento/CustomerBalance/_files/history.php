<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Customer/_files/customer.php';
/** @var $balance Magento_CustomerBalance_Model_Balance */
$balance = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_CustomerBalance_Model_Balance');
$balance->setCustomerId($customer->getId())
    ->setWebsiteId(
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->getWebsiteId()
    );
$balance->save();

/** @var $history Magento_CustomerBalance_Model_Balance_History */
$history = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_CustomerBalance_Model_Balance_History');
$history->setCustomerId($customer->getId())
    ->setWebsiteId(
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->getWebsiteId()
    )
    ->setBalanceModel($balance);
$history->save();
