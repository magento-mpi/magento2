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
            && Mage::helper('Enterprise_Reward_Helper_Data')->isEnabledOnFront($websiteId)
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
        $rewardFormatted = Mage::helper('Enterprise_Reward_Helper_Data')
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
