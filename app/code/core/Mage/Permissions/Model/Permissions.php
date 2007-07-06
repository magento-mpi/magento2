<?php
class Mage_Tag_Model_Permissions extends Varien_Object {
	public function getResource() {
        return Mage::getResourceSingleton('permissions/permissions');
    }
    
    public function load($userId) {
        $this->setData($this->getResource()->load($userId));        
        return $this;
    }
    
    public function save() {
        $this->getResource()->save($this);
        return $this;        
    }
    
    public function update() {
        $this->getResource()->update($this);
        return $this;        
    }
    
    public function delete() {
        $this->getResource()->delete($this);
        return $this;
    }
    
    public function getCollection() {
        return Mage::getResourceModel('permissions/permissions_collection');
    }
}
?>