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
 * Checkout Tooltip block to show checkout cart message for gaining reward points
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Tooltip_Checkout extends Magento_Reward_Block_Tooltip
{
    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Reward_Helper_Data $rewardHelper
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Reward_Model_Reward $rewardInstance
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Reward_Helper_Data $rewardHelper,
        Magento_Customer_Model_Session $customerSession,
        Magento_Reward_Model_Reward $rewardInstance,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Checkout_Model_Session $checkoutSession,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $rewardHelper,
            $customerSession,
            $rewardInstance,
            $storeManager,
            $data
        );
        $this->_checkoutSession = $checkoutSession;
    }


    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->_actionInstance) {
            $this->_actionInstance->setQuote($this->_checkoutSession->getQuote());
        }
        return $this;
    }
}
