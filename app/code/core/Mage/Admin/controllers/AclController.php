<?php

class Mage_Admin_AclController extends Mage_Core_Controller_Front_Action
{
    public function userTreeAction()
    {
        $users = Mage::getModel('admin_resource/user_collection')
            ->setOrder('firstname,lastname')->loadData();
        $result = array();
        foreach ($users->getItems() as $user) {
            $result[] = array('allowDrop'=>true, 'allowDrag'=>true, 'leaf'=>true, 
                'id'=>Mage_Admin_Model_Acl::ROLE_TYPE_USER.$user->getUserId(), 
                'text'=>$user->getFirstname().' '.$user->getLastname(), 
            );
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    public function roleTreeAction()
    {
        $roles = Mage::getModel('admin_resource/acl_role_collection')->loadData();
        $roleTree = $this->_buildRoleTree($roles);
        $this->getResponse()->setBody(Zend_Json::encode($roleTree));
    }
    
    public function _buildRoleTree(Varien_Data_Collection_Db $roles, $parentId=0)
    {
        $nodesArr = array();
        foreach ($roles->getItems() as $role) {
            if ($role->getRoleType()!=Mage_Admin_Model_Acl::ROLE_TYPE_GROUP) {
                continue;
            }
            if ($role->getParentId()!=$parentId) {
                continue;
            }
            $nodesArr[] = array('allowDrop'=>true, 'allowDrag'=>true, 'leaf'=>false,
                'id'=>$role->getRoleType().$role->getRoleId(),
                'text'=>$role->getRoleName(),
                'children'=>$this->_buildRoleTree($roles, $role->getRoleId()),
            );
        }
        return $nodesArr;
    }
    
    public function resourceTreeAction()
    {
        $resources = Mage::getSingleton('admin/config')->getNode('admin/acl/resources');
        $result = $this->_buildResourceTree($resources);
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    protected function _buildResourceTree(Varien_Simplexml_Element $parentNode, $path='')
    {
        $nodesArr = array();
        foreach ($parentNode->children() as $node) {
            $nodesArr[] = array('allowDrop'=>true, 'allowDrag'=>true, 'leaf'=>false,
                'id'=>$path.$node->getName(),
                'text'=>$node->getName(),
                'children'=>$this->_buildResourceTree($node, $path.$node->getName().'/'),
            );
        }
        return $nodesArr;
    }
}