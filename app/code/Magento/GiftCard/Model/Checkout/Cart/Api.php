<?php
/**
 * Gift card API
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Checkout\Cart;

class Api extends \Magento\Checkout\Model\Api\Resource
{
    /**
     * List gift cards account belonging to quote
     *
     * @param  string $quoteId
     * @param null|string $store
     * @return array
     */
    public function items($quoteId, $store = null)
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->_getQuote($quoteId, $store);

        $giftcardsList = \Mage::helper('Magento\GiftCardAccount\Helper\Data')->getCards($quote);
        // map short names of giftcard account attributes to long
        foreach($giftcardsList as $id => $card) {
            $giftcardsList[$id] = array(
                'giftcardaccount_id' => $card['i'],
                'code' => $card['c'],
                'used_amount' => $card['a'],
                'base_amount' => $card['ba'],
            );
        }
        return $giftcardsList;
    }

    /**
     * Add gift card account to quote
     *
     * @param string $giftcardAccountCode
     * @param  string $quoteId
     * @param null|string $store
     * @return bool
     */
    public function add($giftcardAccountCode, $quoteId, $store = null)
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->_getQuote($quoteId, $store);

        /** @var $giftcardAccount \Magento\GiftCardAccount\Model\Giftcardaccount */
        $giftcardAccount = \Mage::getModel('Magento\GiftCardAccount\Model\Giftcardaccount')
                ->loadByCode($giftcardAccountCode);
        if (!$giftcardAccount->getId()) {
            $this->_fault('giftcard_account_not_found_by_code');
        }
        try {
            $giftcardAccount->addToCart(true, $quote);
        } catch (\Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * Remove gift card account to quote
     *
     * @param string $giftcardAccountCode
     * @param  string $quoteId
     * @param null|string $store
     * @return bool
     */
    public function remove($giftcardAccountCode, $quoteId, $store = null)
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->_getQuote($quoteId, $store);

        /** @var $giftcardAccount \Magento\GiftCardAccount\Model\Giftcardaccount */
        $giftcardAccount = \Mage::getModel('Magento\GiftCardAccount\Model\Giftcardaccount')
                ->loadByCode($giftcardAccountCode);
        if (!$giftcardAccount->getId()) {
            $this->_fault('giftcard_account_not_found_by_code');
        }
        try {
            $giftcardAccount->removeFromCart(true, $quote);
        } catch (\Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }
}
