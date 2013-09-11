<?php
/**
 * Factory for \Magento\Webapi\Model\Authorization\Role
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Authorization\Role;

class Factory
{
    const ROLE_CLASS_NAME = '\Magento\Webapi\Model\Authorization\Role';

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
     * Return new ACL role model.
     *
     * @param array $arguments
     * @return \Magento\Webapi\Model\Authorization\Role
     */
    public function createRole(array $arguments = array())
    {
        return $this->_objectManager->create(self::ROLE_CLASS_NAME, $arguments);
    }
}
