<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Method;

class PaymentMethodsList implements \Magento\Framework\Option\ArrayInterface
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
        $result = [];
        foreach ($this->paymentHelper->getPaymentMethods() as $code => $data) {
            if ($this->specification->isSatisfiedBy($code)) {
                $result[$code] = isset(
                    $data['title']
                ) ? $data['title'] : $this->paymentHelper->getMethodInstance(
                    $code
                )->getTitle();
            }
        }
        return $result;
    }
}
