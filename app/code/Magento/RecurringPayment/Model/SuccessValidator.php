<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model;

class SuccessValidator
{
    /**
     * Is valid session?
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @return bool
     */
    public function isValid(\Magento\Checkout\Model\Session $checkoutSession)
    {
        if (!$checkoutSession->getLastSuccessQuoteId()) {
            return false;
        }

        if (!$checkoutSession->getLastQuoteId()
            || (!$checkoutSession->getLastOrderId() && count($checkoutSession->getLastRecurringPaymentIds()) == 0)
        ) {
            return false;
        }

        return true;
    }
} 
