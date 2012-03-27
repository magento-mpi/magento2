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

include __DIR__ . '/../../../Mage/Customer/_files/customer.php';
$balance = new Enterprise_CustomerBalance_Model_Balance;
$balance->setCustomerId(1)->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
$balance->save();

$history = new Enterprise_CustomerBalance_Model_Balance_History;
$history->setCustomerId(1)->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->setBalanceModel($balance);
$history->save();
