<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Model\Method;

use Magento\Core\Model\Option\ArrayInterface;

class PaymentMethodsList implements ArrayInterface
{
    /** @var  \Magento\Payment\Helper\Data */
    protected $paymentHelper;

    /** @var  RecurringPaymentSpecification */
    protected $specification;

    /**
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param RecurringPaymentSpecification $specification
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper,
        RecurringPaymentSpecification $specification
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->specification = $specification;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = array();
        foreach ($this->paymentHelper->getPaymentMethods() as $code => $data) {
            if ($this->specification->isSatisfiedBy($code)) {
                $result[$code] =  isset($data['title'])
                    ? $data['title']
                    : $this->paymentHelper->getMethodInstance($code)->getTitle();
            }
        }
        return $result;
    }
}