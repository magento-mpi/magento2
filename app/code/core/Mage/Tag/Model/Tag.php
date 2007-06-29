<?php
class Mage_Tag_Model_Tag extends Varien_Object {
	public function getResource() {
        return Mage::getResourceSingleton('tag/tag');
    }
    
    public function load($reviewId) {
        $this->setData($this->getResource()->load($tagId));        
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
        return Mage::getResourceModel('tag/tag_collection');
    }
    
    public function getSize() {
    	$size = "14px";
    	return $size;
    }
}
?>