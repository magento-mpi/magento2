<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model\Acl\Loader;

class Role implements \Magento\Acl\LoaderInterface
{
    /**
     * @var \Magento\Core\Model\Resource
     */
    protected $_resource;

    /**
     * @var Magento_User_Model_Acl_Role_GroupFactory
     */
    protected $_groupFactory;

    /**
     * @var Magento_User_Model_Acl_Role_UserFactory
     */
    protected $_roleFactory;

    /**
     * @param Magento_User_Model_Acl_Role_GroupFactory $groupFactory
     * @param Magento_User_Model_Acl_Role_UserFactory $roleFactory
     * @param \Magento\Core\Model\Resource $resource
     */
    public function __construct(
        Magento_User_Model_Acl_Role_GroupFactory $groupFactory,
        Magento_User_Model_Acl_Role_UserFactory $roleFactory,
        \Magento\Core\Model\Resource $resource
    ) {
        $this->_resource = $resource;
        $this->_groupFactory = $groupFactory;
        $this->_roleFactory = $roleFactory;
    }

    /**
     * Populate ACL with roles from external storage
     *
     * @param \Magento\Acl $acl
     */
    public function populateAcl(\Magento\Acl $acl)
    {
        $roleTableName = $this->_resource->getTableName('admin_role');
        $adapter = $this->_resource->getConnection('read');

        $select = $adapter->select()
            ->from($roleTableName)
            ->order('tree_level');

        foreach ($adapter->fetchAll($select) as $role) {
            $parent = ($role['parent_id'] > 0) ?
                \Magento\User\Model\Acl\Role\Group::ROLE_TYPE . $role['parent_id'] : null;
            switch ($role['role_type']) {
                case \Magento\User\Model\Acl\Role\Group::ROLE_TYPE:
                    $roleId = $role['role_type'] . $role['role_id'];
                    $acl->addRole(
                        $this->_groupFactory->create(array('roleId' => $roleId)),
                        $parent
                    );
                    break;

                case \Magento\User\Model\Acl\Role\User::ROLE_TYPE:
                    $roleId = $role['role_type'] . $role['user_id'];
                    if (!$acl->hasRole($roleId)) {
                        $acl->addRole(
                            $this->_roleFactory->create(array('roleId' => $roleId)),
                            $parent
                        );
                    } else {
                        $acl->addRoleParent($roleId, $parent);
                    }
                    break;
            }
        }
    }
}
