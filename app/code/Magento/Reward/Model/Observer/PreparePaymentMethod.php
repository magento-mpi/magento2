<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class PreparePaymentMethod
{
    /**
     * Reward helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData
    ) {
        $this->_rewardData = $rewardData;
    }

    /**
     * Enable Zero Subtotal Checkout payment method
     * if customer has enough points to cover grand total
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute($observer)
    {
        if (!$this->_rewardData->isEnabledOnFront()) {
            return $this;
        }

        $quote = $observer->getEvent()->getQuote();
        if (!is_object($quote) || !$quote->getId()) {
            return $this;
        }

        /* @var $reward \Magento\Reward\Model\Reward */
        $reward = $quote->getRewardInstance();
        if (!$reward || !$reward->getId()) {
            return $this;
        }

        $baseQuoteGrandTotal = $quote->getBaseGrandTotal() + $quote->getBaseRewardCurrencyAmount();
        if ($reward->isEnoughPointsToCoverAmount($baseQuoteGrandTotal)) {
            $paymentCode = $observer->getEvent()->getMethodInstance()->getCode();
            $result = $observer->getEvent()->getResult();
            $result->isAvailable = $paymentCode === 'free' && empty($result->isDeniedInConfig);
        }
        return $this;
    }
}
