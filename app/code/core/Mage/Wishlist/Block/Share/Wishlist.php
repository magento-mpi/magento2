<?php
/**
 * Wishlist block shared items
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Block_Share_Wishlist extends Mage_Core_Block_Template 
{
	protected $_wishlistLoaded = false;
	protected $_customer = null;
	
	public function __construct() 
	{
		parent::__construct();
		$this->setTemplate('wishlist/shared.phtml');
        Mage::registry('action')->getLayout()->getBlock('root')->setHeaderTitle($this->getHeader());
	}
	
	public function getWishlist()
	{
		if(!$this->_wishlistLoaded) {
			
			
			Mage::registry('shared_wishlist')->getItemCollection()
				->addAttributeToSelect('name')
	            ->addAttributeToSelect('price')
	            ->addAttributeToSelect('image')
	            ->addAttributeToSelect('small_image')
	            ->addAttributeToFilter('store_id', array('in'=>Mage::getSingleton('core/store')->getDatashareStores('wishlist')))
				->load();
				
			$this->_wishlistLoaded = true;
		}
		
		return Mage::registry('shared_wishlist')->getItemCollection();
	}
	
	public function getWishlistCustomer()
	{
		if(is_null($this->_customer)) {
			$this->_customer = Mage::getModel('customer/customer')
				->load(Mage::registry('shared_wishlist')->getCustomerId());	
				
		}
		
		return $this->_customer;
	}
	
	
	public function getEscapedDescription(Mage_Wishlist_Model_Item $item) 
	{
		return htmlspecialchars($item->getDescription());
	}
	
	public function getHeader() 
	{
		return __("%s's Wishlist", $this->getWishlistCustomer()->getFirstname());
	}
	
	public function getFormatedDate($date) 
	{
		return strftime(Mage::getStoreConfig('general/local/datetime_format_medium'), strtotime($date));
	}
}// Class Mage_Wishlist_Block_Customer_Wishlist END
