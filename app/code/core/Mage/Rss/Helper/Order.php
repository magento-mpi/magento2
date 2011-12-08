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
class Mage_Rss_Helper_Order extends Mage_Core_Helper_Abstract
{
    public function isStatusNotificationAllow()
    {
        if (Mage::getStoreConfig('rss/order/status_notified')) {
            return true;
        }
        return false;
    }

    public function getStatusHistoryRssUrl($order)
    {
        $key = $order->getId().":".$order->getIncrementId().":".$order->getCustomerId();
        return $this->_getUrl('rss/order/status', array(
            '_secure' => true,
            '_query' => array(
                'data' => Mage::helper('Mage_Core_Helper_Data')->encrypt($key))
            )
        );
    }

}
