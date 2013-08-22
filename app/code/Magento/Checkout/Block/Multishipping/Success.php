<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping checkout success information
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Multishipping_Success extends Magento_Checkout_Block_Multishipping_Abstract
{
    public function getOrderIds()
    {
        $ids = Mage::getSingleton('Magento_Core_Model_Session')->getOrderIds(true);
//        Zend_Debug::dump(Mage::getSingleton('Magento_Core_Model_Session')->getOrderIds());
        if ($ids && is_array($ids)) {
            return $ids;
            return implode(', ', $ids);
        }
        return false;
    }

    public function getViewOrderUrl($orderId)
    {
        return $this->getUrl('sales/order/view/', array('order_id' => $orderId, '_secure' => true));
    }

    public function getContinueUrl()
    {
        return Mage::getBaseUrl();
    }
}
