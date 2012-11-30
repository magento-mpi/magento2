<?php
/**
 * Factory for Acl resource
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_ResourceFactory
{
    const RESOURCE_CLASS_NAME = 'Magento_Acl_Resource';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return new ACL resource model
     *
     * @param array $arguments
     * @return Magento_Acl_Resource
     */
    public function createResource(array $arguments = array())
    {
        return $this->_objectManager->create(self::RESOURCE_CLASS_NAME, $arguments, false);
    }
}
