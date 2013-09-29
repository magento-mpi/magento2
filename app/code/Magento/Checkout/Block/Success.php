<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Checkout\Block;

class Success extends \Magento\Core\Block\Template
{
    public function getRealOrderId()
    {
        $order = \Mage::getModel('Magento\Sales\Model\Order')->load($this->getLastOrderId());
        #print_r($order->getData());
        return $order->getIncrementId();
    }
}
