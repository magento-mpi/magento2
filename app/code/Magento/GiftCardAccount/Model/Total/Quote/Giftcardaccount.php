<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Model_Total_Quote_Giftcardaccount extends Magento_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Gift card account data
     *
     * @var Magento_GiftCardAccount_Helper_Data
     */
    protected $_giftCardAccountData = null;

    /**
     * Gift card account giftcardaccount
     *
     * @var Magento_GiftCardAccount_Model_GiftcardaccountFactory
     */
    protected $_giftCAFactory = null;

    /**
     * Init total model, set total code
     *
     * @param Magento_GiftCardAccount_Helper_Data $giftCardAccountData
     * @param Magento_GiftCardAccount_Model_GiftcardaccountFactory $giftCAFactory
     */
    public function __construct(
        Magento_GiftCardAccount_Helper_Data $giftCardAccountData,
        Magento_GiftCardAccount_Model_GiftcardaccountFactory $giftCAFactory
    ) {
        $this->_giftCAFactory = $giftCAFactory;
        $this->_giftCardAccountData = $giftCardAccountData;
        $this->setCode('giftcardaccount');
    }

    /**
     * Collect giftcertificate totals for specified address
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Magento_GiftCardAccount_Model_Total_Quote_Giftcardaccount
     */
    public function collect(Magento_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        $this->_collectQuoteGiftCards($quote);
        $baseAmountLeft = $quote->getBaseGiftCardsAmount() - $quote->getBaseGiftCardsAmountUsed();
        $amountLeft = $quote->getGiftCardsAmount() - $quote->getGiftCardsAmountUsed();

        if ($baseAmountLeft >= $address->getBaseGrandTotal()) {
            $baseUsed = $address->getBaseGrandTotal();
            $used = $address->getGrandTotal();

            $address->setBaseGrandTotal(0);
            $address->setGrandTotal(0);
        } else {
            $baseUsed = $baseAmountLeft;
            $used = $amountLeft;

            $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseAmountLeft);
            $address->setGrandTotal($address->getGrandTotal() - $amountLeft);
        }

        $addressCards = array();
        $usedAddressCards = array();
        if ($baseUsed) {
            $quoteCards = $this->_sortGiftCards($this->_giftCardAccountData->getCards($quote));
            $skipped = 0;
            $baseSaved = 0;
            $saved = 0;
            foreach ($quoteCards as $quoteCard) {
                $card = $quoteCard;
                if ($quoteCard['ba'] + $skipped <= $quote->getBaseGiftCardsAmountUsed()) {
                    $baseThisCardUsedAmount = $thisCardUsedAmount = 0;
                } elseif ($quoteCard['ba'] + $baseSaved > $baseUsed) {
                    $baseThisCardUsedAmount = min($quoteCard['ba'], $baseUsed - $baseSaved);
                    $thisCardUsedAmount = min($quoteCard['a'], $used - $saved);

                    $baseSaved += $baseThisCardUsedAmount;
                    $saved += $thisCardUsedAmount;
                } elseif ($quoteCard['ba'] + $skipped + $baseSaved > $quote->getBaseGiftCardsAmountUsed()) {
                    $baseThisCardUsedAmount = min($quoteCard['ba'], $baseUsed);
                    $thisCardUsedAmount = min($quoteCard['a'], $used);

                    $baseSaved += $baseThisCardUsedAmount;
                    $saved += $thisCardUsedAmount;
                } else {
                    $baseThisCardUsedAmount = $thisCardUsedAmount = 0;
                }
                // avoid possible errors in future comparisons
                $card['ba'] = round($baseThisCardUsedAmount, 4);
                $card['a'] = round($thisCardUsedAmount, 4);
                $addressCards[] = $card;
                if ($baseThisCardUsedAmount) {
                    $usedAddressCards[] = $card;
                }

                $skipped += $quoteCard['ba'];
            }
        }
        $this->_giftCardAccountData->setCards($address, $usedAddressCards);
        $address->setUsedGiftCards($address->getGiftCards());
        $this->_giftCardAccountData->setCards($address, $addressCards);

        $baseTotalUsed = $quote->getBaseGiftCardsAmountUsed() + $baseUsed;
        $totalUsed = $quote->getGiftCardsAmountUsed() + $used;

        $quote->setBaseGiftCardsAmountUsed($baseTotalUsed);
        $quote->setGiftCardsAmountUsed($totalUsed);

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
            $cards = $this->_giftCardAccountData->getCards($quote);
            foreach ($cards as $k=>&$card) {
                $model = $this->_giftCAFactory->create()->load($card['i']);
                if ($model->isExpired() || $model->getBalance() == 0) {
                    unset($cards[$k]);
                } else if ($model->getBalance() != $card['ba']) {
                    $card['ba'] = $model->getBalance();
                } else {
                    $card['a'] = $quote->getStore()->roundPrice($quote->getStore()->convertPrice($card['ba']));
                    $baseAmount += $card['ba'];
                    $amount += $card['a'];
                }
            }
            $this->_giftCardAccountData->setCards($quote, $cards);

            $quote->setBaseGiftCardsAmount($baseAmount);
            $quote->setGiftCardsAmount($amount);

            $quote->setGiftCardsTotalCollected(true);
        }
    }

    /**
     * Return shopping cart total row items
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Magento_GiftCardAccount_Model_Total_Quote_Giftcardaccount
     */
    public function fetch(Magento_Sales_Model_Quote_Address $address)
    {
        if ($address->getQuote()->isVirtual()) {
            $giftCards = $this->_giftCardAccountData->getCards($address->getQuote()->getBillingAddress());
        } else {
            $giftCards = $this->_giftCardAccountData->getCards($address);
        }
        $address->addTotal(array(
            'code'=>$this->getCode(),
            'title'=>__('Gift Cards'),
            'value'=>-$address->getGiftCardsAmount(),
            'gift_cards'=>$giftCards,
        ));

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
