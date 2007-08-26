<?php
/**
 * Links block
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Wishlist_Block_Links extends Mage_Core_Block_Template
{
    protected $_wishlist = null;

    public function addWishlistLink()
    {
        $count = $this->getWishlistItems()->getSize();
        if( $count > 1 ) {
            $text = __('My Wishlist (%d items)', $count);
        } elseif( $count == 1 ) {
            $text = __('My Wishlist (%d item)', $count);
        } else {
            $text = __('My Wishlist');
        }

        $this->getParentBlock()->addLink(null, 'href="'.Mage::getUrl('wishlist').'"', $text);
    }

	public function getWishlist()
	{
		if(is_null($this->_wishlist)) {
			$this->_wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());

			$this->_wishlist->getProductCollection()
				->addAttributeToSelect('name')
				->addAttributeToSelect('price')
                ->addAttributeToSelect('small_image')
				->addAttributeToFilter('store_id', array('in'=>$this->_wishlist->getSharedStoreIds()))
				->addAttributeToSort('added_at', 'desc')
                ->setCurPage(1)
				->setPageSize(3)
				->load();
		}

		return $this->_wishlist;
	}

	public function getWishlistItems()
	{
		return $this->getWishlist()->getProductCollection();
	}
}