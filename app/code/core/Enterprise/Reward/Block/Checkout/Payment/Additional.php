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
 * Checkout reward payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Checkout_Payment_Additional extends Mage_Core_Block_Template
{
    /**
     * Getter
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer();
    }

    /**
     * Getter
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session')->getQuote();
    }

    /**
     * Getter
     *
     * @return Enterprise_Reward_Model_Reward
     */
    public function getReward()
    {
        if (!$this->getData('reward')) {
            $reward = Mage::getModel('Enterprise_Reward_Model_Reward')
                ->setCustomer($this->getCustomer())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByCustomer();
            $this->setData('reward', $reward);
        }
        return $this->getData('reward');
    }

    /**
     * Return flag from quote to use reward points or not
     *
     * @return boolean
     */
    public function useRewardPoints()
    {
        return (bool)$this->getQuote()->getUseRewardPoints();
    }

    /**
     * Return true if customer can use his reward points.
     * In case if currency amount of his points more then 0 and has minimum limit of points
     *
     * @return boolean
     */
    public function getCanUseRewardPoints()
    {
        if (!Mage::helper('Enterprise_Reward_Helper_Data')->getHasRates()
            || !Mage::helper('Enterprise_Reward_Helper_Data')->isEnabledOnFront()) {
            return false;
        }
        $minPointsToUse = Mage::helper('Enterprise_Reward_Helper_Data')
            ->getGeneralConfig('min_points_balance', (int)Mage::app()->getWebsite()->getId());
        $canUseRewadPoints = ($this->getPointsBalance() >= $minPointsToUse) ? true : false;
        return (boolean)(((float)$this->getCurrencyAmount() > 0) && $canUseRewadPoints);
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getPointsBalance()
    {
        return $this->getReward()->getPointsBalance();
    }

    /**
     * Getter
     *
     * @return float
     */
    public function getCurrencyAmount()
    {
        return $this->getReward()->getCurrencyAmount();
    }

    /**
     * Check if customer has enough points to cover total
     *
     * @return boolean
     */
    public function isEnoughPoints()
    {
        $baseGrandTotal = $this->getQuote()->getBaseGrandTotal()+$this->getQuote()->getBaseRewardCurrencyAmount();
        return $this->getReward()->isEnoughPointsToCoverAmount($baseGrandTotal);
    }
}
