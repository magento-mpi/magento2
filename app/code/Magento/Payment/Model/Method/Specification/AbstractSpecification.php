<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Payment\Model\Method\Specification;

use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Payment\Model\Method\SpecificationInterface;

/**
 * Abstract specification
 */
abstract class AbstractSpecification implements SpecificationInterface
{
    /**
     * Payment methods info
     *
     * @var array
     */
    protected $methodsInfo = [];

    /**
     * Construct
     *
     * @param PaymentConfig $paymentConfig
     */
    public function __construct(PaymentConfig $paymentConfig)
    {
        $this->methodsInfo = $paymentConfig->getMethodsInfo();
    }
}
