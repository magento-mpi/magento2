<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

class CopyConstructorFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create link builder instance
     *
     * @param string $instance
     * @param array $arguments
     * @return CopyConstructorInterface
     * @throws \InvalidArgumentException
     */
    public function create($instance, array $arguments = array())
    {
        if (!is_subclass_of($instance, '\Magento\Catalog\Model\Product\CopyConstructorInterface')) {
            throw new \InvalidArgumentException(
                $instance . ' does not implement \Magento\Catalog\Model\Product\CopyConstructorInterface'
            );
        }

        return $this->objectManager->create($instance, $arguments);
    }
} 
