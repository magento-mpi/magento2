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
 * Reward Points Payment block in admin order creating process
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Adminhtml\Sales\Order\Create;

class Payment extends \Magento\Backend\Block\Template
{
    /**
     * Getter
     *
     * @return \Magento\Adminhtml\Model\Sales\Order\Create
     */
    protected function _getOrderCreateModel()
    {
        return \Mage::getSingleton('Magento\Adminhtml\Model\Sales\Order\Create');
    }

    /**
     * Getter
     *
     * @return \Magento\Sales\Model\Quote
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
        $websiteId = \Mage::app()->getStore($this->getQuote()->getStoreId())->getWebsiteId();
        $minPointsBalance = (int)\Mage::getStoreConfig(
            \Magento\Reward\Model\Reward::XML_PATH_MIN_POINTS_BALANCE,
            $this->getQuote()->getStoreId()
        );

        return $this->getReward()->getPointsBalance() >= $minPointsBalance
            && \Mage::helper('Magento\Reward\Helper\Data')->isEnabledOnFront($websiteId)
            && $this->_authorization->isAllowed(\Magento\Reward\Helper\Data::XML_PATH_PERMISSION_AFFECT)
            && (float)$this->getCurrencyAmount()
            && $this->getQuote()->getBaseGrandTotal() + $this->getQuote()->getBaseRewardCurrencyAmount() > 0;
    }

    /**
     * Getter.
     * Retrieve reward points model
     *
     * @return \Magento\Reward\Model\Reward
     */
    public function getReward()
    {
        if (!$this->_getData('reward')) {
            /* @var $reward \Magento\Reward\Model\Reward */
            $reward = \Mage::getModel('\Magento\Reward\Model\Reward')
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
        $rewardFormatted = \Mage::helper('Magento\Reward\Helper\Data')
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
