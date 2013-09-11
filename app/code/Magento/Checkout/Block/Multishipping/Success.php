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
namespace Magento\Checkout\Block\Multishipping;

class Success extends \Magento\Checkout\Block\Multishipping\AbstractMultishipping
{
    public function getOrderIds()
    {
        $ids = \Mage::getSingleton('Magento\Core\Model\Session')->getOrderIds(true);
//        \Zend_Debug::dump(\Mage::getSingleton('Magento\Core\Model\Session')->getOrderIds());
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
        return \Mage::getBaseUrl();
    }
}
