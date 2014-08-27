<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class PaymentDataImport
{
    /**
     * Core model store configuration
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * Reward helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_rewardData = $rewardData;
        $this->_scopeConfig = $scopeConfig;
        $this->_rewardFactory = $rewardFactory;
    }

    /**
     * Payment data import in checkout process
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_rewardData->isEnabledOnFront()) {
            return $this;
        }
        $input = $observer->getEvent()->getInput();
        /* @var $quote \Magento\Sales\Model\Quote */
        $quote = $observer->getEvent()->getPayment()->getQuote();
        $this->_paymentDataImport($quote, $input, $input->getUseRewardPoints());
        return $this;
    }

    /**
     * Prepare and set to quote reward balance instance,
     * set zero subtotal checkout payment if need
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @param \Magento\Framework\Object $payment
     * @param bool $useRewardPoints
     * @return $this
     */
    protected function _paymentDataImport($quote, $payment, $useRewardPoints)
    {
        if (!$quote ||
            !$quote->getCustomerId() ||
            $quote->getBaseGrandTotal() + $quote->getBaseRewardCurrencyAmount() <= 0
        ) {
            return $this;
        }
        $quote->setUseRewardPoints((bool)$useRewardPoints);
        if ($quote->getUseRewardPoints()) {
            /* @var $reward \Magento\Reward\Model\Reward */
            $reward = $this->_rewardFactory->create()->setCustomer(
                $quote->getCustomer()
            )->setWebsiteId(
                    $quote->getStore()->getWebsiteId()
                )->loadByCustomer();
            $minPointsBalance = (int)$this->_scopeConfig->getValue(
                \Magento\Reward\Model\Reward::XML_PATH_MIN_POINTS_BALANCE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $quote->getStoreId()
            );

            if ($reward->getId() && $reward->getPointsBalance() >= $minPointsBalance) {
                $quote->setRewardInstance($reward);
                if (!$payment->getMethod()) {
                    $payment->setMethod('free');
                }
            } else {
                $quote->setUseRewardPoints(false);
            }
        }
        return $this;
    }

}
