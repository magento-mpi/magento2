<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Checkout reward payment block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Checkout\Payment;

class Additional extends \Magento\Framework\View\Element\Template
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_rewardData = $rewardData;
        $this->_rewardFactory = $rewardFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Getter
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    /**
     * Getter
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Getter
     *
     * @return \Magento\Reward\Model\Reward
     */
    public function getReward()
    {
        if (!$this->getData('reward')) {
            $reward = $this->_rewardFactory->create()->setCustomer(
                $this->getCustomer()
            )->setWebsiteId(
                $this->_storeManager->getStore()->getWebsiteId()
            )->loadByCustomer();
            $this->setData('reward', $reward);
        }
        return $this->getData('reward');
    }

    /**
     * Return flag from quote to use reward points or not
     *
     * @return bool
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

        $minPointsToUse = $helper->getGeneralConfig(
            'min_points_balance',
            (int)$this->_storeManager->getWebsite()->getId()
        );
        return (double)$this->getCurrencyAmount() > 0 && $this->getPointsBalance() >= $minPointsToUse;
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
