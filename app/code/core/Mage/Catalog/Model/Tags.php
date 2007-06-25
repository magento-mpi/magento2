<?php
class Mage_Catalog_Model_Tags extends Varien_Object {
	public function getResource() {
		
        return Mage::getResourceSingleton('catalog/tags');
    }
	
	public function __construct() {
		
	}
	
	/**
	 * @param $filter	int - 1=pending, 2=approved, 4=unapproved (bit operations)
	 */
	public function getTags($product_id = 0, $uid = 0, $filter = 7) {	
		$list = $this->getResource()->getProductTags($product_id, $uid, $filter);
		
		return $list;
	}
	
	public function getUserTags($uid, $filter = 0) {
		return $this->getTags(0, $uid, $filter);
	}
	
	public function getProductTags($product_id, $filter) {
		return $this->getTags($product_id, 0, $filter);
	}
	
	/**
	 * @param $tags = array('tag_id' => 'status', ...)
	 * 
	 */
	public function setStatus($tags) {
		$this->getResource()->setStatus($tags);		
	}	
	
	public function addTag($tag_name, $uid, $product_id) {
		$this->setTagName($tag_name);
		$this->setUserId($uid);
		$this->setProductId($product_id);
		
		$this->getResource()->add($this);
        return $this; 
	}
	
	public function updateTag($id, $tag_name) {
		$this->setTagName($tag_name);
		$this->setTagId($id);
		
		$this->getResource()->update($this);
	}
	
	public function deleteTag($id, $uid) {
		$this->setUserId($uid);
		$this->setTagId($id);
		
		$this->getResource()->delete($this);
	}
	
	public function getTaggedProducts($uid) {
		return $this->getResource()->getTaggedProducts($uid);
	}
	
	public function getTaggedCustomers($pid) {
		return $this->getResource()->getTaggedCustomers($pid);
	}
}
?>