<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Payment\Model\Method\Specification;

use Magento\Framework\ObjectManager;
use Magento\Payment\Model\Method\SpecificationInterface;

/**
 * Specification Factory
 */
class Factory
{
    /**
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Factory constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create specification instance
     *
     * @param string $specificationClass
     * @return SpecificationInterface
     * @throws \InvalidArgumentException
     */
    public function create($specificationClass)
    {
        $specification = $this->objectManager->get($specificationClass);
        if (!$specification instanceof SpecificationInterface) {
            throw new \InvalidArgumentException('Specification must implement SpecificationInterface');
        }
        return $specification;
    }
}
