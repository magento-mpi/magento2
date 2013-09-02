<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward Helper for operations with customer
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Helper_Customer extends Magento_Core_Helper_Abstract
{
    /**
     * Return Unsubscribe notification URL
     *
     * @param string|boolean $notification Notification type
     * @param int|string|Magento_Core_Model_Store $storeId
     * @return string
     */
    public function getUnsubscribeUrl($notification = false, $storeId = null)
    {
        $params = array();

        if ($notification) {
            $params['notification'] = $notification;
        }
        if (!is_null($storeId)) {
            $params['store_id'] = $storeId;
        }
        return Mage::app()->getStore($storeId)->getUrl('magento_reward/customer/unsubscribe/', $params);
    }
}
