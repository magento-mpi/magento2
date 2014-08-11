<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data\PaymentMethod;

use Magento\Checkout\Service\V1\Data\PaymentMethod as QuotePaymentMethod;

class Converter
{
    /**
     * @var \Magento\Checkout\Service\V1\Data\Cart\PaymentMethodBuilder
     */
    protected $builder;

    /**
     * @param \Magento\Checkout\Service\V1\Data\PaymentMethodBuilder $builder
     */
    public function __construct(\Magento\Checkout\Service\V1\Data\PaymentMethodBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Convert quote payment object to payment data object
     *
     * @return QuotePaymentMethod
     */
    public function toDataObject(\Magento\Payment\Model\MethodInterface $object)
    {
        $data = [
            QuotePaymentMethod::CODE => $object->getCode(),
            QuotePaymentMethod::TITLE => $object->getTitle(),
        ];
        return $this->builder->populateWithArray($data)->create();
    }
}
