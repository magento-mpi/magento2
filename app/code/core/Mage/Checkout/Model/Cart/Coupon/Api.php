<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart api
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Checkout_Model_Cart_Coupon_Api extends Mage_Checkout_Model_Api_Resource
{
    /**
     * @param  $quoteId
     * @param  $couponCode
     * @param  $storeId
     * @return bool
     */
    public function add($quoteId, $couponCode, $store = null)
    {
        return $this->_applyCoupon($quoteId, $couponCode, $store = null);
    }

    /**
     * @param  $quoteId
     * @param  $storeId
     * @return void
     */
    public function remove($quoteId, $store = null)
    {
        $couponCode = '';
        return $this->_applyCoupon($quoteId, $couponCode, $store);
    }

    /**
     * @param  $quoteId
     * @param  $storeId
     * @return string
     */
    public function get($quoteId, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        return $quote->getCouponCode();
    }

    /**
     * @param  $quoteId
     * @param  $couponCode
     * @param  $store
     * @return bool
     */
    protected function _applyCoupon($quoteId, $couponCode, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        if (!$quote->getItemsCount()) {
            $this->_fault('quote_is_empty');
        }

        $oldCouponCode = $quote->getCouponCode();
        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            return false;
        }

        try {
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();
        } catch (Exception $e) {
            $this->_fault("cannot_apply_coupon_code", $e->getMessage());
        }

        if ($couponCode) {
            if (!$couponCode == $quote->getCouponCode()) {
                $this->_fault('coupon_code_is_not_valid');
            }
        }

        return true;
    }


}
