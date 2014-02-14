<?php
/**
 * Acl object factory.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento;

class AclFactory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new magento acl instance
     *
     * @return \Magento\Acl
     */
    public function create()
    {
        return $this->_objectManager->create('Magento\Acl');
    }
}
