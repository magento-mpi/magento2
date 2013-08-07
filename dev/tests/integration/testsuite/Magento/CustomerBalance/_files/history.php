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
$balance = Mage::getModel('Magento_CustomerBalance_Model_Balance');
$balance->setCustomerId($customer->getId())
    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$balance->save();

/** @var $history Magento_CustomerBalance_Model_Balance_History */
$history = Mage::getModel('Magento_CustomerBalance_Model_Balance_History');
$history->setCustomerId($customer->getId())
    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
    ->setBalanceModel($balance);
$history->save();
