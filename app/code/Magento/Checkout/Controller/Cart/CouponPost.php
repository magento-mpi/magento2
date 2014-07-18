<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Controller\Cart;

class CouponPost extends \Magento\Checkout\Controller\Cart
{
    /**
     * Initialize coupon
     *
     * @return void
     */
    public function execute()
    {
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->cart->getQuote()->getItemsCount()) {
            $this->_goBack();
            return;
        }

        $couponCode = $this->getRequest()->getParam(
            'remove'
        ) == 1 ? '' : trim(
            $this->getRequest()->getParam('coupon_code')
        );
        $oldCouponCode = $this->cart->getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_goBack();
            return;
        }

        try {
            $codeLength = strlen($couponCode);
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

            $this->cart->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->cart->getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals()->save();

            if ($codeLength) {
                if ($isCodeLengthValid && $couponCode == $this->cart->getQuote()->getCouponCode()) {
                    $this->messageManager->addSuccess(
                        __(
                            'The coupon code "%1" was applied.',
                            $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($couponCode)
                        )
                    );
                } else {
                    $this->messageManager->addError(
                        __(
                            'The coupon code "%1" is not valid.',
                            $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($couponCode)
                        )
                    );
                }
            } else {
                $this->messageManager->addSuccess(__('The coupon code was canceled.'));
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We cannot apply the coupon code.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }

        $this->_goBack();
    }
}
