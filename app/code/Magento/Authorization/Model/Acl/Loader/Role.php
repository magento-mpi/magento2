<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorization\Model\Acl\Loader;

use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\Acl\Role\User as RoleUser;

class Role implements \Magento\Framework\Acl\LoaderInterface
{
    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $_resource;

    /**
     * @var \Magento\Authorization\Model\Acl\Role\GroupFactory
     */
    protected $_groupFactory;

    /**
     * @var \Magento\Authorization\Model\Acl\Role\UserFactory
     */
    protected $_roleFactory;

    /**
     * @param \Magento\Authorization\Model\Acl\Role\GroupFactory $groupFactory
     * @param \Magento\Authorization\Model\Acl\Role\UserFactory $roleFactory
     * @param \Magento\Framework\App\Resource $resource
     */
    public function __construct(
        \Magento\Authorization\Model\Acl\Role\GroupFactory $groupFactory,
        \Magento\Authorization\Model\Acl\Role\UserFactory $roleFactory,
        \Magento\Framework\App\Resource $resource
    ) {
        $this->_resource = $resource;
        $this->_groupFactory = $groupFactory;
        $this->_roleFactory = $roleFactory;
    }

    /**
     * Populate ACL with roles from external storage
     *
     * @param \Magento\Framework\Acl $acl
     * @return void
     */
    public function populateAcl(\Magento\Framework\Acl $acl)
    {
        $roleTableName = $this->_resource->getTableName('authorization_role');
        $adapter = $this->_resource->getConnection('core_read');

        $select = $adapter->select()->from($roleTableName)->order('tree_level');

        foreach ($adapter->fetchAll($select) as $role) {
            $parent = $role['parent_id'] > 0 ? $role['parent_id'] : null;
            switch ($role['role_type']) {
                case RoleGroup::ROLE_TYPE:
                    $acl->addRole($this->_groupFactory->create(array('roleId' => $role['role_id'])), $parent);
                    break;

                case RoleUser::ROLE_TYPE:
                    if (!$acl->hasRole($role['role_id'])) {
                        $acl->addRole($this->_roleFactory->create(array('roleId' => $role['role_id'])), $parent);
                    } else {
                        $acl->addRoleParent($role['role_id'], $parent);
                    }
                    break;
            }
        }
    }
}
