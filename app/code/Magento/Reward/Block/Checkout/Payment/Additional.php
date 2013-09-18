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
 * Checkout reward payment block
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Checkout\Payment;

class Additional extends \Magento\Core\Block\Template
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Getter
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer();
    }

    /**
     * Getter
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
    }

    /**
     * Getter
     *
     * @return \Magento\Reward\Model\Reward
     */
    public function getReward()
    {
        if (!$this->getData('reward')) {
            $reward = \Mage::getModel('Magento\Reward\Model\Reward')
                ->setCustomer($this->getCustomer())
                ->setWebsiteId(\Mage::app()->getStore()->getWebsiteId())
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
     * In case if currency amount of his points is more than zero and
     * is not contrary to the Minimum Reward Points Balance to Be Able to Redeem limit.
     *
     * @return bool
     */
    public function getCanUseRewardPoints()
    {
        /** @var $helper \Magento\Reward\Helper\Data */
        $helper = $this->_rewardData;
        if (!$helper->getHasRates() || !$helper->isEnabledOnFront()) {
            return false;
        }

        $minPointsToUse = $helper->getGeneralConfig('min_points_balance', (int)\Mage::app()->getWebsite()->getId());
        return (float)$this->getCurrencyAmount() > 0 && $this->getPointsBalance() >= $minPointsToUse;
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
     * @return bool
     */
    public function isEnoughPoints()
    {
        $baseGrandTotal = $this->getQuote()->getBaseGrandTotal() + $this->getQuote()->getBaseRewardCurrencyAmount();
        return $this->getReward()->isEnoughPointsToCoverAmount($baseGrandTotal);
    }
}
