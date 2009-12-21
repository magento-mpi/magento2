<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Reward sales quote total model
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Total_Quote_Reward extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('reward');
    }

    /**
     * Collect reward totals
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Enterprise_Reward_Model_Total_Quote_Reward
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $address->getQuote();

        $pointsBalance = 0;
        $pointsCurrencyAmount = 0;
        $basePointsCurrencyAmount = 0;

        $quote->setRewardPointsBalance($pointsBalance);
        $quote->setRewardCurrencyAmount($pointsCurrencyAmount);
        $quote->setBaseRewardCurrencyAmount($basePointsCurrencyAmount);

        $address->setRewardPointsBalance($pointsBalance);
        $address->setRewardCurrencyAmount($pointsCurrencyAmount);
        $address->setBaseRewardCurrencyAmount($basePointsCurrencyAmount);

        if ($address->getBaseGrandTotal() && $quote->getCustomer()->getId() && $quote->getUseRewardPoints()) {

            /* @var $reward Enterprise_Reward_Model_Reward */
            $reward = $quote->getRewardInstance();
            if (!$reward || !$reward->getId()) {
                $reward = Mage::getModel('enterprise_reward/reward')
                    ->setCustomer($quote->getCustomer())
                    ->setWebsite($quote->getStore()->getWebsiteId())
                    ->loadByCustomer();
            }
            if ($reward->isEnoughPointsToCoverAmount($address->getBaseGrandTotal())) {
                $pointsBalance = $reward->getPointsEquivalent($address->getBaseGrandTotal());
                $pointsCurrencyAmount = $address->getGrandTotal();
                $basePointsCurrencyAmount = $address->getBaseGrandTotal();

                $address->setGrandTotal(0);
                $address->setBaseGrandTotal(0);
            } else {
                $grandTotal = 0;
                $baseGrandTotal = 0;

                $pointsBalance = $reward->getPointsBalance();
                $pointsCurrencyAmount = $quote->getStore()->convertPrice($reward->getCurrencyAmount());
                $basePointsCurrencyAmount = $reward->getCurrencyAmount();

                $grandTotal = $address->getGrandTotal() - $pointsCurrencyAmount;
                $baseGrandTotal = $address->getBaseGrandTotal() - $basePointsCurrencyAmount;

                $address->setGrandTotal($grandTotal);
                $address->setBaseGrandTotal($baseGrandTotal);
            }
        }

        $quote->setRewardPointsBalance($pointsBalance);
        $quote->setRewardCurrencyAmount($pointsCurrencyAmount);
        $quote->setBaseRewardCurrencyAmount($basePointsCurrencyAmount);

        $address->setRewardPointsBalance($pointsBalance);
        $address->setRewardCurrencyAmount($pointsCurrencyAmount);
        $address->setBaseRewardCurrencyAmount($basePointsCurrencyAmount);

        return $this;
    }

    /**
     * Retrieve reward total data and set it to quote address
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Enterprise_Reward_Model_Total_Quote_Reward
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled()) {
            return $this;
        }
        if ($address->getRewardCurrencyAmount()) {
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => Mage::helper('enterprise_reward')->__('%d Reward Points', $address->getRewardPointsBalance()),
                'value' => -$address->getRewardCurrencyAmount()
            ));
        }
        return $this;
    }
}
