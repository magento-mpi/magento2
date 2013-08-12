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
 * Reward Points Settings form
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Customer_Reward_Subscription extends Magento_Core_Block_Template
{
    /**
     * Getter for RewardUpdateNotification
     *
     * @return bool
     */
    public function isSubscribedForUpdates()
    {
        return (bool)$this->_getCustomer()->getRewardUpdateNotification();
    }

    /**
     * Getter for RewardWarningNotification
     *
     * @return bool
     */
    public function isSubscribedForWarnings()
    {
        return (bool)$this->_getCustomer()->getRewardWarningNotification();
    }

    /**
     * Retrieve customer model
     *
     * @return Magento_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer();
    }
}
