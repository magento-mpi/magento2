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
 * Reward Points Settings form
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Customer_Reward_Subscription extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Getter for RewardUpdateNotification
     *
     * @return bool
     */
    public function isSubscribedForUpdates()
    {
        return (bool)$this->_customerSession->getCustomer()->getRewardUpdateNotification();
    }

    /**
     * Getter for RewardWarningNotification
     *
     * @return bool
     */
    public function isSubscribedForWarnings()
    {
        return (bool)$this->_customerSession->getCustomer()->getRewardWarningNotification();
    }
}
