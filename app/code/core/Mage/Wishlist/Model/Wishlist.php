<?php
/**
 * Wishlist model
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Model_Wishlist extends Mage_Core_Model_Abstract 
{
	protected $_itemCollection = null;
	
	protected function _construct()
	{
		$this->_init('wishlist/wishlist');
	}
	
	public function loadByCustomer(Mage_Customer_Model_Customer $customer, $create=false) 
	{
		$this->getResource()->load($this, 
								   $customer->getId(), 
								   $this->getResource()->getCustomerIdFieldName());
		if(!$this->getId() && $create) {
			$this->setCustomerId($customer->getId());
			$this->setSharingCode($this->_getSharingRandomCode());
			$this->save();
		}
		return $this;
	}
	
	public function loadByCode($code) 
	{
		$this->getResource()->load($this, 
								   $code, 
								   'sharing_code');
		if(!$this->getShared()) {
			$this->setId(null);
		}
		return $this;
	}
	
	protected function _getSharingRandomCode() 
	{
		return md5(microtime() . rand());
	}
	
	public function getItemCollection() 
	{
		if(is_null($this->_itemCollection)) {
			$this->_itemCollection =  Mage::getResourceModel('wishlist/item_collection')
				->addWishlistFilter($this);
		}
		
		return $this->_itemCollection;
	}
	
	public function addNewItem($productId) 
	{
		$item = Mage::getModel('wishlist/item');
		$item->loadByProductWishlist($this->getId(), $productId, Mage::getSingleton('core/store')->getDatashareStores('wishlist'));
		
		if($item->getId()) {
			Mage::throwException('Product already added to wishlist');
		}
		
		$item->setProductId($productId)
			->setWishlistId($this->getId())
			->setAddedAt(now())
			->setStoreId(Mage::getSingleton('core/store')->getId())
			->save();
		
		return $item;
	}
	
	public function setCustomerId($customerId) 
	{
		return $this->setData($this->getResource()->getCustomerIdFieldName(), $customerId);
	}
	
	public function getCustomerId() 
	{
		return $this->getData($this->getResource()->getCustomerIdFieldName());
	}
	
	public function getDataForSave()
	{
		$data = array();
		$data[$this->getResource()->getCustomerIdFieldName()] = $this->getCustomerId();
		$data['shared']		 = (int) $this->getShared();
		$data['sharing_code']= $this->getSharingCode();
		return $data;
	}
	
}// Class Mage_Wishlist_Model_Wishlist END