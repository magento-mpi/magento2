<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GiftCard api
 *
 * @category   Enterprise
 * @package    Enterprise_GiftCard
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_GiftCard_Model_Checkout_Cart_Api extends Mage_Checkout_Model_Api_Resource
{
    /**
     * List gift cards account belonging to quote
     *
     * @param  string $quoteId
     * @param null|string $storeId
     * @return array
     */
    public function items($quoteId, $storeId = null)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $this->_getQuote($quoteId, $storeId);

        $giftcardsList = Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->getCards($quote);
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
     * @param null|string $storeId
     * @return bool
     */
    public function add($giftcardAccountCode, $quoteId, $storeId = null)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $this->_getQuote($quoteId, $storeId);

        /** @var $giftcardAccount Enterprise_GiftCardAccount_Model_Giftcardaccount */
        $giftcardAccount = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount')
                ->loadByCode($giftcardAccountCode);
        if (!$giftcardAccount->getId()) {
            $this->_fault('giftcard_account_not_found_by_code');
        }
        try {
            $giftcardAccount->addToCart(true, $quote);
        } catch (Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * Remove gift card account to quote
     *
     * @param string $giftcardAccountCode
     * @param  string $quoteId
     * @param null|string $storeId
     * @return bool
     */
    public function remove($giftcardAccountCode, $quoteId, $storeId = null)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $this->_getQuote($quoteId, $storeId);

        /** @var $giftcardAccount Enterprise_GiftCardAccount_Model_Giftcardaccount */
        $giftcardAccount = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount')
                ->loadByCode($giftcardAccountCode);
        if (!$giftcardAccount->getId()) {
            $this->_fault('giftcard_account_not_found_by_code');
        }
        try {
            $giftcardAccount->removeFromCart(true, $quote);
        } catch (Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }
}
