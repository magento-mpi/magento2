<?php
class Mage_Permissions_IndexController extends Mage_Core_Controller_Front_Action {
    public function indexAction() {

    }
    
    public function saveuserAction() {
    	
    }
    
    public function saveroleAction() {
    	
    }
    
    public function deleteuserAction() {
    	
    }
    
    public function deleteroleAction() {
    	
    }
    
    public function deleteuserfromroleAction() {
    	Mage::getModel("permissions/users")
    		->setRoleId($this->getRequest()->getParam('role_id', false))
    		->setUserId($this->getRequest()->getParam('user_id', false))
    		->deleteFromRole();
    	echo json_encode(array('error' => 0, 'error_message' => 'test message'));
    }
    
    public function adduser2roleAction() {
    	Mage::getModel("permissions/users")
    		->setRoleId($this->getRequest()->getParam('role_id', false))
    		->setUserId($this->getRequest()->getParam('user_id', false))
    		->add();
   		echo json_encode(array('error' => 0, 'error_message' => 'test message'));
    }
    
    public function addrole2resourceAction() {
    	
    }
}