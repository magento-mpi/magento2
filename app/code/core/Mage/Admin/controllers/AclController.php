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
                'id'=>'U'.$user->getUserId(), 
                'text'=>$user->getFirstname().' '.$user->getLastname(), 
            );
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    public function roleTreeAction()
    {
        #$acl = Mage::getModel('admin/acl');
        
        #$this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    public function resourceTreeAction()
    {
        #$resources = Mage::getConfig()->getNode('admin/acl/resources');
        
        #$this->getResponse()->setBody(Zend_Json::encode($result));
    }
}