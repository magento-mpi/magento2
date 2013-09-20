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
     * @param Magento_Reward_Helper_Data $rewardData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $session
     * @param Magento_Reward_Model_Reward $reward
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Magento_Reward_Helper_Data $rewardData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $session,
        Magento_Reward_Model_Reward $reward,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Checkout_Model_Session $checkoutSession,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($rewardData, $coreData, $context, $session, $reward, $storeManager, $data);
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
