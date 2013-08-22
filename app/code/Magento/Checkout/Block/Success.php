<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Checkout_Block_Success extends Magento_Core_Block_Template
{
    public function getRealOrderId()
    {
        $order = Mage::getModel('Magento_Sales_Model_Order')->load($this->getLastOrderId());
        #print_r($order->getData());
        return $order->getIncrementId();
    }
}
