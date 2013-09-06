<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento_Sales_Model_Order_Status $status */
$status = Mage::getModel('Magento_Sales_Model_Order_Status');
//status for state new
$status->setData('status', 'custom_new_status')
    ->setData('label', 'Test Status')
    ->save();
$status->assignState(Magento_Sales_Model_Order::STATE_NEW, true);
//status for state canceled
$status->setData('status', 'custom_canceled_status')
    ->setData('label', 'Test Status')
    ->unsetData('id')
    ->save();
$status->assignState(Magento_Sales_Model_Order::STATE_CANCELED, true);
