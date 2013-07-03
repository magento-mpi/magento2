<?php
/**
 * Acl object factory.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_AclFactory
{
    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    private $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new magento acl instance
     *
     * @return Magento_Acl
     */
    public function create()
    {
        return $this->_objectManager->create('Magento_Acl');
    }
}
