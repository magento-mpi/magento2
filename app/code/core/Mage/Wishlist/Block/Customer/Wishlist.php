<?php
/**
 * Wishlist block customer items
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Block_Customer_Wishlist extends Mage_Core_Block_Template 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		$this->setTemplate('wishlist/view.phtml');
	}
	
	public function getWishlist()
	{
		if(is_null($this->_wishlist)) {
			Mage::registry('wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());
			Mage::registry('wishlist')->getItemCollection()
				->addAttributeToSelect('name')
	            ->addAttributeToSelect('price')
	            ->addAttributeToSelect('image')
	            ->addAttributeToSelect('small_image')
				->load();
		}
		
		return Mage::registry('wishlist')->getItemCollection();
	}
	
	public function getEscapedDescription(Mage_Wishlist_Model_Item $item) 
	{
		return htmlspecialchars($item->getDescription());
	}
	
	public function getFormatedDate($date) 
	{
		return strftime(Mage::getStoreConfig('general/local/datetime_format_medium'), strtotime($date));
	}
}// Class Mage_Wishlist_Block_Customer_Wishlist END