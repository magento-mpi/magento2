<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data\Cart\PaymentMethod;

use Magento\Checkout\Service\V1\Data\Cart\PaymentMethod as QuotePaymentMethod;
use Magento\Sales\Model\Quote;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Builder
 */
class Builder
{
    /**
     * @param QuotePaymentMethod $object
     * @param Quote $quote
     * @return \Magento\Sales\Model\Quote\Payment
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function build(QuotePaymentMethod $object, Quote $quote)
    {
        $payment = $quote->getPayment();
        try {
            $payment->importData($object->__toArray());
        } catch(\Exception $e) {
            throw new LocalizedException('The requested Payment Method is not available.');
        }
        return $payment;
    }
}
