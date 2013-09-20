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
/** @var $balance \Magento\CustomerBalance\Model\Balance */
$balance = \Mage::getModel('Magento\CustomerBalance\Model\Balance');
$balance->setCustomerId($customer->getId())
    ->setWebsiteId(\Mage::app()->getStore()->getWebsiteId());
$balance->save();

/** @var $history \Magento\CustomerBalance\Model\Balance\History */
$history = \Mage::getModel('Magento\CustomerBalance\Model\Balance\History');
$history->setCustomerId($customer->getId())
    ->setWebsiteId(\Mage::app()->getStore()->getWebsiteId())
    ->setBalanceModel($balance);
$history->save();
