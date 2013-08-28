<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CustomerBalance
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Customer/_files/customer.php';
/** @var $balance Enterprise_CustomerBalance_Model_Balance */
$balance = Mage::getModel('Enterprise_CustomerBalance_Model_Balance');
$balance->setCustomerId($customer->getId())
    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$balance->save();

/** @var $history Enterprise_CustomerBalance_Model_Balance_History */
$history = Mage::getModel('Enterprise_CustomerBalance_Model_Balance_History');
$history->setCustomerId($customer->getId())
    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
    ->setBalanceModel($balance);
$history->save();
