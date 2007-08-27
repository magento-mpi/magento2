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

class Mage_Wishlist_Block_Share_Email_Items extends Mage_Core_Block_Template
{
	protected $_wishlistLoaded = false;

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('wishlist/email/items.phtml');
	}

	public function getWishlist()
	{
		if(!$this->_wishlistLoaded) {
			Mage::registry('wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());
			Mage::registry('wishlist')->getProductCollection()
				->addAttributeToSelect('name')
	            ->addAttributeToSelect('price')
	            ->addAttributeToSelect('image')
	            ->addAttributeToSelect('small_image')
	            ->addAttributeToFilter('store_id', array('in'=>Mage::registry('wishlist')->getSharedStoreIds()))
				->load();

			$this->_wishlistLoaded = true;
		}

		return Mage::registry('wishlist')->getProductCollection();
	}

	public function getEscapedDescription(Varien_Object $item)
	{
		return $this->htmlEscape($item->getDescription());
	}

	public function getFormatedDate($date)
	{
		return strftime(Mage::getStoreConfig('general/local/datetime_format_medium'), strtotime($date));
	}
}// Class Mage_Wishlist_Block_Share_Email_Items END