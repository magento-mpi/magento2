<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_User_Model_Acl_Loader_Role implements Magento_Acl_LoaderInterface
{
    /**
     * @var Magento_Core_Model_Resource
     */
    protected $_resource;

    public function __construct(array $data = array())
    {
        $this->_resource = isset($data['resource'])
            ? $data['resource']
            : Mage::getSingleton('Magento_Core_Model_Resource');

        $this->_objectFactory = isset($data['objectFactory'])
            ? $data['objectFactory']
            : Mage::getConfig();
    }

    /**
     * Populate ACL with roles from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        $roleTableName = $this->_resource->getTableName('admin_role');
        $adapter = $this->_resource->getConnection('read');

        $select = $adapter->select()
            ->from($roleTableName)
            ->order('tree_level');

        foreach ($adapter->fetchAll($select) as $role) {
            $parent = ($role['parent_id'] > 0) ?
                Magento_User_Model_Acl_Role_Group::ROLE_TYPE . $role['parent_id'] : null;
            switch ($role['role_type']) {
                case Magento_User_Model_Acl_Role_Group::ROLE_TYPE:
                    $roleId = $role['role_type'] . $role['role_id'];
                    $acl->addRole(
                        $this->_objectFactory->getModelInstance('Magento_User_Model_Acl_Role_Group',
                            array('roleId' => $roleId)
                        ),
                        $parent
                    );
                    break;

                case Magento_User_Model_Acl_Role_User::ROLE_TYPE:
                    $roleId = $role['role_type'] . $role['user_id'];
                    if (!$acl->hasRole($roleId)) {
                        $acl->addRole(
                            $this->_objectFactory->getModelInstance('Magento_User_Model_Acl_Role_User',
                                array('roleId' => $roleId)
                            ),
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
