<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Payment\Model\Method\Specification;

use Magento\Payment\Model\Method\SpecificationInterface;

/**
 * Composite specification
 */
class Composite implements SpecificationInterface
{
    /**
     * Specifications collection
     *
     * @var SpecificationInterface[]
     */
    protected $specifications = array();

    /**
     * Construct
     *
     * @param Factory $factory
     * @param array $specifications
     */
    public function __construct(Factory $factory, $specifications = array())
    {
        foreach ($specifications as $specification) {
            $this->specifications[] = $factory->create($specification);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($paymentMethod)
    {
        foreach ($this->specifications as $specification) {
            if (!$specification->isSatisfiedBy($paymentMethod)) {
                return false;
            }
        }
        return true;
    }
}
