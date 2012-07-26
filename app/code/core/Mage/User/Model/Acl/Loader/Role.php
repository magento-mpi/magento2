<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_User_Acl_Loader_Role implements Magento_Acl_Loader
{
    /**
     * Populate ACL with roles from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        $roleTable   = $this->getTable('admin_role');

        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($roleTable)
            ->order('tree_level');

        foreach ($adapter->fetchAll($select) as $role) {
            $parent = ($role['parent_id'] > 0) ? Mage_User_Model_Acl_Role_Group::ROLE_TYPE . $role['parent_id'] : null;
            switch ($role['role_type']) {
                case Mage_User_Model_Acl_Role_Group::ROLE_TYPE:
                    $roleId = $role['role_type'] . $role['role_id'];
                    $acl->addRole(Mage::getModel('Mage_User_Model_Acl_Role_Group', $roleId), $parent);
                    break;

                case Mage_User_Model_Acl_Role_User::ROLE_TYPE:
                    $roleId = $role['role_type'] . $role['user_id'];
                    if (!$acl->hasRole($roleId)) {
                        $acl->addRole(Mage::getModel('Mage_User_Model_Acl_Role_User', $roleId), $parent);
                    } else {
                        $acl->addRoleParent($roleId, $parent);
                    }
                    break;
            }
        }
    }
}
