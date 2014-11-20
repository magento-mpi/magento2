<?php
/**
 * Application area front name resolver factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Area;

class FrontNameResolverFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create front name resolver
     *
     * @param string $className
     * @return FrontNameResolverInterface
     */
    public function create($className)
    {
        return $this->_objectManager->create($className);
    }
}
