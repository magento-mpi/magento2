<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model;

use \Magento\Payment\Model\Method\AbstractMethod;

class MethodList
{
    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Payment\Model\Checks\SpecificationFactory
     */
    protected $methodSpecificationFactory;

    /**
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param Checks\SpecificationFactory $specificationFactory
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Payment\Model\Checks\SpecificationFactory $specificationFactory
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->methodSpecificationFactory = $specificationFactory;
    }

    public function getAvailableMethods(\Magento\Sales\Model\Quote $quote = null)
    {
        $store = $quote ? $quote->getStoreId() : null;
        $methods = array();
        $specification = $this->methodSpecificationFactory->create(array(AbstractMethod::CHECK_ZERO_TOTAL));
        foreach ($this->paymentHelper->getStoreMethods($store, $quote) as $method) {
            if ($this->_canUseMethod($method, $quote) && $specification->isApplicable($method, $quote)) {
                $method->setInfoInstance($quote->getPayment());
                $methods[] = $method;
            }
        }
        return $methods;
    }

    /**
     * Check payment method model
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    protected function _canUseMethod($method, \Magento\Sales\Model\Quote $quote)
    {
        return $this->methodSpecificationFactory->create(
            array(
                AbstractMethod::CHECK_USE_FOR_COUNTRY,
                AbstractMethod::CHECK_USE_FOR_CURRENCY,
                AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX
            )
        )->isApplicable(
            $method,
            $quote
        );
    }
}
