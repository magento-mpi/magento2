<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart api
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Checkout\Model\Cart\Coupon;

class Api extends \Magento\Checkout\Model\Api\Resource
{
    /**
     * @param  $quoteId
     * @param  $couponCode
     * @param  $store
     * @return bool
     */
    public function add($quoteId, $couponCode, $store = null)
    {
        return $this->_applyCoupon($quoteId, $couponCode, $store = null);
    }

    /**
     * @param  $quoteId
     * @param  $store
     * @return bool
     */
    public function remove($quoteId, $store = null)
    {
        $couponCode = '';
        return $this->_applyCoupon($quoteId, $couponCode, $store);
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
        } catch (\Exception $e) {
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
