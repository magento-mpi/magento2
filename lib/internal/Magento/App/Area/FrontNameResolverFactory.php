<?php
/**
 * Application area front name resolver factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Area;

class FrontNameResolverFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
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