<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\CopyConstructor;

use Magento\Catalog\Model\Product\CopyConstructorInterface;
use Magento\Catalog\Model\Product\CopyConstructorFactory;

class Composite implements CopyConstructorInterface
{
    /**
     * @var CopyConstructorInterface[]
     */
    protected $constructors;

    /**
     * @param CopyConstructorFactory $factory
     * @param string[] $constructors
     */
    public function __construct(CopyConstructorFactory $factory, array $constructors = array())
    {
        foreach ($constructors as $instance) {
            $this->constructors[] = $factory->create($instance);
        }
    }

    /**
     * Build product duplicate
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product $duplicate
     * @return void
     */
    public function build(\Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Product $duplicate)
    {
        foreach ($this->constructors as $constructor) {
            $constructor->build($product, $duplicate);
        }
    }
}
