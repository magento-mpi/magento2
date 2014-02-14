<?php
/**
 * Factory for Acl resource
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Acl;

use Magento\ObjectManager;

class ResourceFactory
{
    const RESOURCE_CLASS_NAME = 'Magento\Acl\Resource';

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
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
