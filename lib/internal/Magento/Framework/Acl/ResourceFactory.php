<?php
/**
 * Factory for Acl resource
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Acl;

use Magento\Framework\ObjectManagerInterface;

class ResourceFactory
{
    const RESOURCE_CLASS_NAME = 'Magento\Framework\Acl\Resource';

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return new ACL resource model
     *
     * @param array $arguments
     * @return Resource
     */
    public function createResource(array $arguments = array())
    {
        return $this->_objectManager->create(self::RESOURCE_CLASS_NAME, $arguments);
    }
}
