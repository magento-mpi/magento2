<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Total\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Quote\Address;

/**
 * Reward sales quote total model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Reward extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->_rewardData = $rewardData;
        $this->_rewardFactory = $rewardFactory;
        $this->setCode('reward');
    }

    /**
     * Collect reward totals
     *
     * @param Address $address
     * @return $this
     */
    public function collect(Address $address)
    {
        /* @var $quote \Magento\Sales\Model\Quote */
        $quote = $address->getQuote();
        if (!$this->_rewardData->isEnabledOnFront($quote->getStore()->getWebsiteId())) {
            return $this;
        }

        if (!$quote->getRewardPointsTotalReseted() && $address->getBaseGrandTotal() > 0) {
            $quote->setRewardPointsBalance(0)->setRewardCurrencyAmount(0)->setBaseRewardCurrencyAmount(0);
            $address->setRewardPointsBalance(0)->setRewardCurrencyAmount(0)->setBaseRewardCurrencyAmount(0);
            $quote->setRewardPointsTotalReseted(true);
        }

        if ($address->getBaseGrandTotal() >= 0 && $quote->getCustomer()->getId() && $quote->getUseRewardPoints()) {
            /* @var $reward \Magento\Reward\Model\Reward */
            $reward = $quote->getRewardInstance();
            if (!$reward || !$reward->getId()) {
                $reward = $this->_rewardFactory->create()->setCustomer(
                    $quote->getCustomer()
                )->setCustomerId(
                    $quote->getCustomer()->getId()
                )->setWebsiteId(
                    $quote->getStore()->getWebsiteId()
                )->loadByCustomer();
            }
            $pointsLeft = $reward->getPointsBalance() - $quote->getRewardPointsBalance();
            $rewardCurrencyAmountLeft = $this->priceCurrency->convert(
                $reward->getCurrencyAmount(),
                $quote->getStore()
            ) - $quote->getRewardCurrencyAmount();
            $baseRewardCurrencyAmountLeft = $reward->getCurrencyAmount() - $quote->getBaseRewardCurrencyAmount();
            if ($baseRewardCurrencyAmountLeft >= $address->getBaseGrandTotal()) {
                $pointsBalanceUsed = $reward->getPointsEquivalent($address->getBaseGrandTotal());
                $pointsCurrencyAmountUsed = $address->getGrandTotal();
                $basePointsCurrencyAmountUsed = $address->getBaseGrandTotal();

                $address->setGrandTotal(0);
                $address->setBaseGrandTotal(0);
            } else {
                $pointsBalanceUsed = $reward->getPointsEquivalent($baseRewardCurrencyAmountLeft);
                if ($pointsBalanceUsed > $pointsLeft) {
                    $pointsBalanceUsed = $pointsLeft;
                }
                $pointsCurrencyAmountUsed = $rewardCurrencyAmountLeft;
                $basePointsCurrencyAmountUsed = $baseRewardCurrencyAmountLeft;

                $address->setGrandTotal($address->getGrandTotal() - $pointsCurrencyAmountUsed);
                $address->setBaseGrandTotal($address->getBaseGrandTotal() - $basePointsCurrencyAmountUsed);
            }
            $quote->setRewardPointsBalance($quote->getRewardPointsBalance() + $pointsBalanceUsed);
            $quote->setRewardCurrencyAmount($quote->getRewardCurrencyAmount() + $pointsCurrencyAmountUsed);
            $quote->setBaseRewardCurrencyAmount($quote->getBaseRewardCurrencyAmount() + $basePointsCurrencyAmountUsed);

            $address->setRewardPointsBalance($pointsBalanceUsed);
            $address->setRewardCurrencyAmount($pointsCurrencyAmountUsed);
            $address->setBaseRewardCurrencyAmount($basePointsCurrencyAmountUsed);
        }
        return $this;
    }

    /**
     * Retrieve reward total data and set it to quote address
     *
     * @param Address $address
     * @return $this
     */
    public function fetch(Address $address)
    {
        $websiteId = $address->getQuote()->getStore()->getWebsiteId();
        if (!$this->_rewardData->isEnabledOnFront($websiteId)) {
            return $this;
        }
        if ($address->getRewardCurrencyAmount()) {
            $address->addTotal(
                array(
                    'code' => $this->getCode(),
                    'title' => $this->_rewardData->formatReward($address->getRewardPointsBalance()),
                    'value' => -$address->getRewardCurrencyAmount()
                )
            );
        }
        return $this;
    }
}
