<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
    protected $specifications = [];

    /**
     * Construct
     *
     * @param Factory $factory
     * @param array $specifications
     */
    public function __construct(Factory $factory, $specifications = [])
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
