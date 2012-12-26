<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $history Mage_Sales_Model_Order_Status_History */
$history = Mage::getModel('Mage_Sales_Model_Order_Status_History');
$history->setData(
    array(
        'comment' => 'Test comment',
        'status' => 'pending',
        'is_customer_notified' => 0,
        'is_visible_on_front' => 1
    )
);
return $history;
