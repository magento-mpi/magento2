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
        $roleTree = Mage::getModel('admin_resource/acl_role_tree')->toArray();
        $result = $this->_buildRoleTree($roleTree);
        
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    protected function _buildRoleTree(array $parentNode, $path='')
    {
        
        #$roles->
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
            $nodeArr = array('allowDrop'=>true, 'allowDrag'=>true, 'leaf'=>false);
            $nodeArr['id'] = $path.$node->getName();
            $nodeArr['text'] = $node->getName();
            $nodeArr['children'] = $this->_buildResourceTree($node, $nodeArr['id'].'/');
            $nodesArr[] = $nodeArr;
        }
        return $nodesArr;
    }
}