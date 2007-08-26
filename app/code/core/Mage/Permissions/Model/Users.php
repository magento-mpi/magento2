<?php
class Mage_Permissions_Model_Users extends Varien_Object {
	public function getResource() {
        return Mage::getResourceSingleton('permissions/users');
    }

    public function load($userId) {
        $this->setData($this->getResource()->load($userId));
        return $this;
    }

    public function save() {
        $this->getResource()->save($this);
        return $this;
    }

    public function saveRel() {
    	$this->getResource()->saveRel($this);
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
        return Mage::getResourceModel('permissions/users_collection');
    }

    public function add() {
    	$this->getResource()->add($this);
        return $this;
    }

    public function deleteFromRole() {
    	$this->getResource()->deleteFromRole($this);
    	return $this;
    }

    public function encodePwd($pwd) {
    	return md5($pwd);
    }

    public function roleUserExists()
    {
        $result = $this->getResource()->roleUserExists($this);
        return ( is_array($result) && count($result) > 0 ) ? true : false;
    }

    public function hasAssigned2Role()
    {
    	$res = $this->getResource()->hasAssigned2Role($this);
    	return ( is_array($res) && count($res) > 0 ) ? true : false;
    }

    public function userExists()
    {
        $result = $this->getResource()->userExists($this);
        return ( is_array($result) && count($result) > 0 ) ? true : false;
    }
}
?>