<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category   Enterprise
 * @package    Enterprise_GiftCertificate
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Enterprise_GiftCardAccount_Model_Total_Quote_GiftCardAccount extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Init total model, set total code
     *
     */
    public function __construct(){
        $this->setCode('giftcardaccount');
    }

    /**
     * Collect giftcertificate totals for specified address
     *
     * @param Mage_Sales_Model_Quote_Address $address
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_collectQuoteGiftCards($address->getQuote());
        $baseAmountLeft = $address->getQuote()->getBaseGiftCardsAmount()-$address->getQuote()->getBaseGiftCardsAmountUsed();
        $amountLeft = $address->getQuote()->getGiftCardsAmount()-$address->getQuote()->getGiftCardsAmountUsed();

        $baseTotalUsed = $totalUsed = $baseUsed = $used = 0;
        if ($baseAmountLeft >= $address->getBaseGrandTotal()) {
            $baseUsed = $address->getBaseGrandTotal();
            $used = $address->getGrandTotal();

            $address->setBaseGrandTotal(0);
            $address->setGrandTotal(0);
        } else {
            $baseUsed = $baseAmountLeft;
            $used = $amountLeft;

            $address->setBaseGrandTotal($address->getBaseGrandTotal()-$baseAmountLeft);
            $address->setGrandTotal($address->getGrandTotal()-$amountLeft);
        }

        $baseTotalUsed = $address->getQuote()->getBaseGiftCardsAmountUsed() + $baseUsed;
        $totalUsed = $address->getQuote()->getGiftCardsAmountUsed() + $used;

        $address->getQuote()->setBaseGiftCardsAmountUsed($baseTotalUsed);
        $address->getQuote()->setGiftCardsAmountUsed($totalUsed);

        $address->setBaseGiftCardsAmount($baseUsed);
        $address->setGiftCardsAmount($used);

        return $this;
    }

    protected function _collectQuoteGiftCards($quote)
    {
        if (!$quote->getGiftCardsTotalCollected()) {
            $quote->setBaseGiftCardsAmount(0);
            $quote->setGiftCardsAmount(0);

            $quote->setBaseGiftCardsAmountUsed(0);
            $quote->setGiftCardsAmountUsed(0);

            $baseAmount = 0;
            $amount = 0;
            $cards = Mage::helper('enterprise_giftcardaccount')->getCards($quote);
            foreach ($cards as $card) {
                $baseAmount += $card['ba'];
                $amount += $card['a'];
            }

            $quote->setBaseGiftCardsAmount($baseAmount);
            $quote->setGiftCardsAmount($amount);

            $quote->setGiftCardsTotalCollected(true);
        }
    }

    /**
     * Return shopping cart total row items
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Enterprise_GiftCertificate_Model_Total_Giftcertificate
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if (!$address->getQuote()->getGiftCardsTotalFetched()) {
            $giftCards = $this->_sortGiftCards(Mage::helper('enterprise_giftcardaccount')->getCards($address->getQuote()));
            foreach ($giftCards as $card) {
                $title = Mage::helper('enterprise_giftcardaccount')->__('Gift Card (%s)', $card['c']);
                $address->addTotal(array(
                    'code'=>"{$this->getCode()}_{$card['c']}",
                    'as'=>$this->getCode(),
                    'title'=>$title,
                    'value'=>-$card['a'],
                    'gift_card'=>$card['c'],
                ));
            }
            $address->getQuote()->setGiftCardsTotalFetched(true);
        }
        return $this;
    }

    protected function _sortGiftCards($in)
    {
        usort($in, array($this, 'compareGiftCards'));
        return $in;
    }

    public static function compareGiftCards($a, $b)
    {
        if ($a['ba'] == $b['ba']) {
            return 0;
        }
        return ($a['ba'] > $b['ba']) ? 1 : -1;
    }
}