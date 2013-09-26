<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default rss helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rss_Helper_Order extends Magento_Core_Helper_Abstract
{
    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Check whether status notification is allowed
     *
     * @return bool
     */
    public function isStatusNotificationAllow()
    {
        if ($this->_coreStoreConfig->getConfig('rss/order/status_notified')) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve order status history url
     *
     * @param Magento_Sales_Model_Order $order
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
     * @param Magento_Sales_Model_Order $order
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
     * @return Magento_Sales_Model_Order|null
     */
    public function getOrderByStatusUrlKey($key)
    {
        $data = json_decode(base64_decode($key), true);
        if (!is_array($data) || !isset($data['order_id']) || !isset($data['increment_id'])
            || !isset($data['customer_id'])
        ) {
            return null;
        }

        /** @var $order Magento_Sales_Model_Order */
        $order = Mage::getModel('Magento_Sales_Model_Order')->load($data['order_id']);
        if ($order->getId()
            && $order->getIncrementId() == $data['increment_id']
            && $order->getCustomerId() == $data['customer_id']
        ) {
            return $order;
        }

        return null;
    }
}
