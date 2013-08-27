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
 * Reward Points Payment block in admin order creating process
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Adminhtml_Sales_Order_Create_Payment extends Magento_Backend_Block_Template
{
    /**
     * Reward data
     *
     * @var Enterprise_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * @param Enterprise_Reward_Helper_Data $rewardData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Reward_Helper_Data $rewardData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Getter
     *
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('Magento_Adminhtml_Model_Sales_Order_Create');
    }

    /**
     * Getter
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_getOrderCreateModel()->getQuote();
    }

    /**
     * Check whether can use customer reward points
     *
     * @return boolean
     */
    public function canUseRewardPoints()
    {
        $websiteId = Mage::app()->getStore($this->getQuote()->getStoreId())->getWebsiteId();
        $minPointsBalance = (int)Mage::getStoreConfig(
            Enterprise_Reward_Model_Reward::XML_PATH_MIN_POINTS_BALANCE,
            $this->getQuote()->getStoreId()
        );

        return $this->getReward()->getPointsBalance() >= $minPointsBalance
            && $this->_rewardData->isEnabledOnFront($websiteId)
            && $this->_authorization->isAllowed(Enterprise_Reward_Helper_Data::XML_PATH_PERMISSION_AFFECT)
            && (float)$this->getCurrencyAmount()
            && $this->getQuote()->getBaseGrandTotal() + $this->getQuote()->getBaseRewardCurrencyAmount() > 0;
    }

    /**
     * Getter.
     * Retrieve reward points model
     *
     * @return Enterprise_Reward_Model_Reward
     */
    public function getReward()
    {
        if (!$this->_getData('reward')) {
            /* @var $reward Enterprise_Reward_Model_Reward */
            $reward = Mage::getModel('Enterprise_Reward_Model_Reward')
                ->setCustomer($this->getQuote()->getCustomer())
                ->setStore($this->getQuote()->getStore())
                ->loadByCustomer();
            $this->setData('reward', $reward);
        }
        return $this->_getData('reward');
    }

    /**
     * Prepare some template data
     *
     * @return string
     */
    protected function _toHtml()
    {
        $points = $this->getReward()->getPointsBalance();
        $amount = $this->getReward()->getCurrencyAmount();
        $rewardFormatted = $this->_rewardData
            ->formatReward($points, $amount, $this->getQuote()->getStore()->getId());
        $this->setPointsBalance($points)->setCurrencyAmount($amount)
            ->setUseLabel(__('Use my reward points; %1 are available.', $rewardFormatted))
        ;
        return parent::_toHtml();
    }

    /**
     * Check if reward points applied in quote
     *
     * @return boolean
     */
    public function useRewardPoints()
    {
        return (bool)$this->_getOrderCreateModel()->getQuote()->getUseRewardPoints();
    }
}
