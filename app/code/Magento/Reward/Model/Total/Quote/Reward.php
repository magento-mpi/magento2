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
 * Reward sales quote total model
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Total\Quote;

class Reward extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData
    ) {
        $this->_rewardData = $rewardData;
        $this->setCode('reward');
    }

    /**
     * Collect reward totals
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return \Magento\Reward\Model\Total\Quote\Reward
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        /* @var $quote \Magento\Sales\Model\Quote */
        $quote = $address->getQuote();
        if (!$this->_rewardData->isEnabledOnFront($quote->getStore()->getWebsiteId())) {
            return $this;
        }

        if (!$quote->getRewardPointsTotalReseted() && $address->getBaseGrandTotal() > 0) {
            $quote->setRewardPointsBalance(0)
                ->setRewardCurrencyAmount(0)
                ->setBaseRewardCurrencyAmount(0);
            $address->setRewardPointsBalance(0)
                ->setRewardCurrencyAmount(0)
                ->setBaseRewardCurrencyAmount(0);
            $quote->setRewardPointsTotalReseted(true);
        }

        if ($address->getBaseGrandTotal() >= 0 && $quote->getCustomer()->getId() && $quote->getUseRewardPoints()) {
            /* @var $reward \Magento\Reward\Model\Reward */
            $reward = $quote->getRewardInstance();
            if (!$reward || !$reward->getId()) {
                $reward = \Mage::getModel('Magento\Reward\Model\Reward')
                    ->setCustomer($quote->getCustomer())
                    ->setCustomerId($quote->getCustomer()->getId())
                    ->setWebsiteId($quote->getStore()->getWebsiteId())
                    ->loadByCustomer();
            }
            $pointsLeft = $reward->getPointsBalance() - $quote->getRewardPointsBalance();
            $rewardCurrencyAmountLeft = ($quote->getStore()->convertPrice($reward->getCurrencyAmount())) - $quote->getRewardCurrencyAmount();
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
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return \Magento\Reward\Model\Total\Quote\Reward
     */
    public function fetch(\Magento\Sales\Model\Quote\Address $address)
    {
        $websiteId = $address->getQuote()->getStore()->getWebsiteId();
        if (!$this->_rewardData->isEnabledOnFront($websiteId)) {
            return $this;
        }
        if ($address->getRewardCurrencyAmount()) {
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => $this->_rewardData->formatReward($address->getRewardPointsBalance()),
                'value' => -$address->getRewardCurrencyAmount()
            ));
        }
        return $this;
    }
}
