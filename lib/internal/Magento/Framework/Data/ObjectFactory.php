<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Data;

/**
 * Class ObjectFactory
 * @package Magento\Framework\Data
 */
class ObjectFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create data object
     *
     * @param string $className
     * @param array $arguments
     * @return \Magento\Framework\Object
     */
    public function create($className, array $arguments)
    {
        return $this->objectManager->create($className, $arguments);
    }
}
