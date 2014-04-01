<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Checks;

use Magento\Sales\Model\Quote;
use Magento\Payment\Model\Method\AbstractMethod;

class Composite implements SpecificationInterface
{
    /** @var SpecificationInterface[]  */
    protected $list = array();

    /**
     * @param SpecificationInterface[] $list
     */
    public function __construct(array $list)
    {
        $this->list = $list;
    }

    /**
     * Check whether payment method is applicable to quote
     *
     * @param \Magento\Payment\Model\Method\AbstractMethod $paymentMethod
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function isApplicable(AbstractMethod $paymentMethod, Quote $quote)
    {
        foreach ($this->list as $specification) {
            if (!$specification->isApplicable($paymentMethod, $quote)) {
                return false;
            }
        }
        return true;
    }
}
