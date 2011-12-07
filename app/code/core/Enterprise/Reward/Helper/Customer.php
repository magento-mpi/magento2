<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward Helper for operations with customer
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Helper_Customer extends Mage_Core_Helper_Abstract
{
    /**
     * Return Unsubscribe notification URL
     *
     * @param string $notification Notification type
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
        return Mage::getUrl('enterprise_reward/customer/unsubscribe/', $params);
    }
}
