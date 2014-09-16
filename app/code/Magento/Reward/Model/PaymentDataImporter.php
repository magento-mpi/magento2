<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model;

class PaymentDataImporter
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
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_rewardFactory = $rewardFactory;
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
    public function import($quote, $payment, $useRewardPoints)
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
