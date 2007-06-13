<?php

class Mage_Admin_Model_Mysql4_Acl_Role_Tree
{
    public function toArray()
    {
        $dbTree = $this->_buildRoleTree();
        
        
        return $roleTree;
    }

    protected function _buildRoleTree($parent=0)
    {
        $roles = Mage::getModel('admin_resource/acl_role_collection');
        $roles->getSelectSql()
            ->where($roles->getConnection()->quoteInto('parent_id=?', $parent))
            ->setOrder('role_type,sort_order')
            ->loadData();
        $rolesArr = array();
/*
        foreach ($roles->getItems() as $role) {
            $a = $role->getData();
            $roleArr['id'] = $a['role_type']
            $a['children'] = $this->_buildRoleTree($role->getData('role_id'));
            $rolesArr[] = array(
                #'id'=>$a['role_type'].$a['user_id']
                #'children'=>$a['children'],
            );
        }
*/
        return $rolesArr;
    }

}