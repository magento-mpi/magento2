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
namespace Magento\Rss\Helper;

class Order extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->_storeConfig = $storeConfig;
        $this->_orderFactory = $orderFactory;
        parent::__construct($context);
    }

    /**
     * Check whether status notification is allowed
     *
     * @return bool
     */
    public function isStatusNotificationAllow()
    {
        if ($this->_storeConfig->getConfig('rss/order/status_notified')) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve order status history url
     *
     * @param \Magento\Sales\Model\Order $order
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
     * @param \Magento\Sales\Model\Order $order
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
     * @return \Magento\Sales\Model\Order|null
     */
    public function getOrderByStatusUrlKey($key)
    {
        $data = json_decode(base64_decode($key), true);
        if (!is_array($data) || !isset($data['order_id']) || !isset($data['increment_id'])
            || !isset($data['customer_id'])
        ) {
            return null;
        }

        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_orderFactory->create();
        $order->load($data['order_id']);
        if ($order->getId()
            && $order->getIncrementId() == $data['increment_id']
            && $order->getCustomerId() == $data['customer_id']
        ) {
            return $order;
        }

        return null;
    }
}
