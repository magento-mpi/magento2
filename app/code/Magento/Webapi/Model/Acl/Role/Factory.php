<?php
/**
 * ACL Role factory.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Acl\Role;

class Factory
{
    const CLASS_NAME = 'Magento\Webapi\Model\Acl\Role';

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
     * Create ACL role model.
     *
     * @param array $arguments
     * @return \Magento\Webapi\Model\Acl\Role
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $arguments);
    }
}
