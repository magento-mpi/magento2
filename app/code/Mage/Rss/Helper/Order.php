<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default rss helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Helper_Order extends Magento_Core_Helper_Abstract
{
    /**
     * Check whether status notification is allowed
     *
     * @return bool
     */
    public function isStatusNotificationAllow()
    {
        if (Mage::getStoreConfig('rss/order/status_notified')) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve order status history url
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getStatusHistoryRssUrl($order)
    {
        return $this->_getUrl('rss/order/status',
            array('_secure' => true, '_query' => array('data' => $this->getStatusUrlKey($order)))
        );
    }

    /**
     * Retrieve order status url key
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getStatusUrlKey($order)
    {
        $data = array(
            'order_id' => $order->getId(),
            'increment_id' => $order->getIncrementId(),
            'customer_id' => $order->getCustomerId()
        );
        return base64_encode(json_encode($data));

    }

    /**
     * Retrieve order instance by specified status url key
     *
     * @param string $key
     * @return Mage_Sales_Model_Order|null
     */
    public function getOrderByStatusUrlKey($key)
    {
        $data = json_decode(base64_decode($key), true);
        if (!is_array($data) || !isset($data['order_id']) || !isset($data['increment_id'])
            || !isset($data['customer_id'])
        ) {
            return null;
        }

        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('Mage_Sales_Model_Order')->load($data['order_id']);
        if ($order->getId()
            && $order->getIncrementId() == $data['increment_id']
            && $order->getCustomerId() == $data['customer_id']
        ) {
            return $order;
        }

        return null;
    }
}
