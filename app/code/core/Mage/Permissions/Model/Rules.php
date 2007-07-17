<?php
class Mage_Permissions_Model_Rules extends Varien_Object {
	public function getResource() {
        return Mage::getResourceSingleton('permissions/rules');
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

    public function saveRel() {
    	$this->getResource()->saveRel($this);
        return $this;
    }
}
?>