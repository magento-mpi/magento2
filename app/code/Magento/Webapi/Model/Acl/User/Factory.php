<?php
/**
 * ACL User factory.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Acl\User;

class Factory extends \Magento\Oauth\Model\Consumer\Factory
{
    const CLASS_NAME = '\Magento\Webapi\Model\Acl\User';

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
     * Create ACL user model.
     *
     * @param array $arguments
     * @return \Magento\Webapi\Model\Acl\User
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $arguments);
    }
}
