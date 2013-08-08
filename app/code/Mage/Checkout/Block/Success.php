<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Checkout_Block_Success extends Magento_Core_Block_Template
{
    public function getRealOrderId()
    {
        $order = Mage::getModel('Mage_Sales_Model_Order')->load($this->getLastOrderId());
        #print_r($order->getData());
        return $order->getIncrementId();
    }
}
