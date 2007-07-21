<?php
/**
 * Wishlist item model
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Model_Item extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('wishlist/item');
	}
	
	public function getDataForSave()
	{
		$data = array();
		$data['product_id']  = $this->getProductId();
		$data['wishlist_id'] = $this->getWishlistId();
		$data['added_at'] 	 = $this->getAddedAt() ? $this->getAddedAt() : now();
		$data['description'] = $this->getDescription();
		$data['store_id']	 = $this->getStoreId() ? $this->getStoreId() : Mage::getSingleton('core/store')->getId();
		
		return $data;
	}
	
	public function loadByProductWishlist($wishlistId, $productId) 
	{
		$this->getResource()->loadByProductWishlist($this, $wishlistId, $productId);
		return $this;
	}
	
	public function getImageUrl()
    {
        $url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).$this->getImage();
        return $url;
    }
        
    public function getSmallImageUrl()
    {
        $url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).$this->getSmallImage();
        return $url;
    }
    
    public function getCategoryId()
    {
        $categoryId = ($this->getData('category_id')) ? $this->getData('category_id') : $this->getDefaultCategory();
        return $categoryId;
    }
    
    public function getProductUrl()
    {
        $url = Mage::getUrl('catalog/product/view', 
            array(
                'id'=>$this->getProductId(),
                'category'=>$this->getCategoryId()
            ));
        return $url;
    }
    
    public function getFormatedPrice()
    {
        return Mage::getSingleton('core/store')->formatPrice($this->getPrice());
    }
		
}// Class Mage_Wishlist_Model_Item END