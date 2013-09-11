<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Block of links in Order view page
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Order\Info;

class Buttons extends \Magento\Core\Block\Template
{

    protected $_template = 'order/info/buttons.phtml';

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return \Mage::registry('current_order');
    }

    /**
     * Get url for printing order
     *
     * @param Magento_Sales_Order $order
     * @return string
     */
    public function getPrintUrl($order)
    {
        if (!\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            return $this->getUrl('sales/guest/print', array('order_id' => $order->getId()));
        }
        return $this->getUrl('sales/order/print', array('order_id' => $order->getId()));
    }

    /**
     * Get url for reorder action
     *
     * @param Magento_Sales_Order $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        if (!\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            return $this->getUrl('sales/guest/reorder', array('order_id' => $order->getId()));
        }
        return $this->getUrl('sales/order/reorder', array('order_id' => $order->getId()));
    }
}
