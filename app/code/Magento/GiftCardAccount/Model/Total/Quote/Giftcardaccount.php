<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Model\Total\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Quote;

class Giftcardaccount extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Gift card account data
     *
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $_giftCardAccountData = null;

    /**
     * Gift card account giftcardaccount
     *
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory
     */
    protected $_giftCAFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardAccountData
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCAFactory
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\GiftCardAccount\Helper\Data $giftCardAccountData,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCAFactory,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->_giftCAFactory = $giftCAFactory;
        $this->_giftCardAccountData = $giftCardAccountData;
        $this->priceCurrency = $priceCurrency;
        $this->setCode('giftcardaccount');
    }

    /**
     * Collect giftcertificate totals for specified address
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
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

    /**
     * @param Quote $quote
     * @return void
     */
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
            foreach ($cards as $k => &$card) {
                $model = $this->_giftCAFactory->create()->load($card['i']);
                if ($model->isExpired() || $model->getBalance() == 0) {
                    unset($cards[$k]);
                } else if ($model->getBalance() != $card['ba']) {
                    $card['ba'] = $model->getBalance();
                } else {
                    $card['a'] = $this->priceCurrency->round(
                        $this->priceCurrency->convert($card['ba'], $quote->getStore())
                    );
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
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return $this
     */
    public function fetch(\Magento\Sales\Model\Quote\Address $address)
    {
        if ($address->getQuote()->isVirtual()) {
            $giftCards = $this->_giftCardAccountData->getCards($address->getQuote()->getBillingAddress());
        } else {
            $giftCards = $this->_giftCardAccountData->getCards($address);
        }
        $address->addTotal(
            array(
                'code' => $this->getCode(),
                'title' => __('Gift Cards'),
                'value' => -$address->getGiftCardsAmount(),
                'gift_cards' => $giftCards
            )
        );

        return $this;
    }

    /**
     * @param array $in
     * @return mixed
     */
    protected function _sortGiftCards($in)
    {
        usort($in, array($this, 'compareGiftCards'));
        return $in;
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function compareGiftCards($a, $b)
    {
        if ($a['ba'] == $b['ba']) {
            return 0;
        }
        return $a['ba'] > $b['ba'] ? 1 : -1;
    }
}
