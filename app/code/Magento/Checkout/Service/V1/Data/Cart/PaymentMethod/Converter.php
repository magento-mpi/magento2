<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data\Cart\PaymentMethod;

use Magento\Checkout\Service\V1\Data\Cart\PaymentMethod as QuotePaymentMethod;

class Converter
{
    /**
     * @var \Magento\Checkout\Service\V1\Data\Cart\PaymentMethodBuilder
     */
    protected $builder;

    /**
     * @param \Magento\Checkout\Service\V1\Data\Cart\PaymentMethodBuilder $builder
     */
    public function __construct(
        \Magento\Checkout\Service\V1\Data\Cart\PaymentMethodBuilder $builder
    ) {
        $this->builder = $builder;
    }

    /**
     * Convert quote payment object to payment data object
     *
     * @param \Magento\Sales\Model\Quote\Payment $object
     * @return QuotePaymentMethod
     */
    public function toDataObject(\Magento\Sales\Model\Quote\Payment $object)
    {
        $data = [
            QuotePaymentMethod::PAYMENT_ID => $object->getId(),
            QuotePaymentMethod::METHOD => $object->getMethod(),
            QuotePaymentMethod::PO_NUMBER => $object->getPoNumber(),
            QuotePaymentMethod::CC_CID => $object->getCcCidEnc(),
            QuotePaymentMethod::CC_OWNER => $object->getCcOwner(),
            QuotePaymentMethod::CC_NUMBER => $object->getCcNumber(),
            QuotePaymentMethod::CC_TYPE => $object->getCcType(),
            QuotePaymentMethod::CC_EXP_YEAR => $object->getCcExpYear(),
            QuotePaymentMethod::CC_EXP_MONTH => $object->getCcExpMonth(),
        ];

        return $this->builder->populateWithArray($data)->create();
    }
}
